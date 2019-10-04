<?php
/**
 * Created by PhpStorm.
 * Date: 07.02.18
 * Time: 17:01
 */

namespace AppBundle\Security;

use AppBundle\Entity\User;
use OAuth2\IOAuth2GrantCode;
use OAuth2\IOAuth2GrantUser;
use OAuth2\IOAuth2RefreshTokens;
use OAuth2\IOAuth2Storage;
use OAuth2\Model\IOAuth2Client;
use OAuth2\OAuth2 as OAuth2Base;
use OAuth2\OAuth2ServerException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OAuth2 extends OAuth2Base
{
    /**
     * @param IOAuth2Client $client
     * @param mixed $data
     * @param null $scope
     * @param null $access_token_lifetime
     * @param bool $issue_refresh_token
     * @param null $refresh_token_lifetime
     * @return array
     */
    public function createAccessToken(IOAuth2Client $client, $data, $scope = null, $access_token_lifetime = null, $issue_refresh_token = true, $refresh_token_lifetime = null)
    {
        $token = array(
            "access_token" => $this->genAccessToken(),
            "expires_in" => ($access_token_lifetime ?: $this->getVariable(self::CONFIG_ACCESS_LIFETIME)),
            "role" => ($data instanceof User) ? (isset($data->getRoles()[0])) ? $data->getRoles()[0] : null : null,
            "id" => ($data instanceof User) ? $data->getId() : null
        );

        $this->storage->createAccessToken(
            $token["access_token"],
            $client,
            $data,
            time() + ($access_token_lifetime ?: $this->getVariable(self::CONFIG_ACCESS_LIFETIME)),
            $scope
        );

        // Issue a refresh token also, if we support them
        if ($this->storage instanceof IOAuth2RefreshTokens && $issue_refresh_token === true) {
            $token["refresh_token"] = $this->genAccessToken();
            $this->storage->createRefreshToken(
                $token["refresh_token"],
                $client,
                $data,
                time() + ($refresh_token_lifetime ?: $this->getVariable(self::CONFIG_REFRESH_LIFETIME)),
                $scope
            );

            // If we've granted a new refresh token, expire the old one
            if (null !== $this->oldRefreshToken) {
                $this->storage->unsetRefreshToken($this->oldRefreshToken);
                $this->oldRefreshToken = null;
            }
        }

        if ($this->storage instanceof IOAuth2GrantCode) {
            if (null !== $this->usedAuthCode) {
                $this->storage->markAuthCodeAsUsed($this->usedAuthCode->getToken());
                $this->usedAuthCode = null;
            }
        }

        return $token;
    }

    /**
     * @param Request|null $request
     * @param bool $manually
     * @return Response
     * @throws OAuth2ServerException
     */
    public function grantAccessToken(Request $request = null, $manually=false)
    {
        $filters = array(
            "grant_type" => array(
                "filter" => FILTER_VALIDATE_REGEXP,
                "options" => array("regexp" => self::GRANT_TYPE_REGEXP),
                "flags" => FILTER_REQUIRE_SCALAR
            ),
            "scope" => array("flags" => FILTER_REQUIRE_SCALAR),
            "code" => array("flags" => FILTER_REQUIRE_SCALAR),
            "redirect_uri" => array("filter" => FILTER_SANITIZE_URL),
            "username" => array("flags" => FILTER_REQUIRE_SCALAR),
            "password" => array("flags" => FILTER_REQUIRE_SCALAR),
            "refresh_token" => array("flags" => FILTER_REQUIRE_SCALAR),
        );

        if ($request === null) {
            $request = Request::createFromGlobals();
        }

        // Input data by default can be either POST or GET
        if ($request->getMethod() === 'POST') {
            $inputData = $request->request->all();
        } else {
            $inputData = $request->query->all();
        }

        // Basic authorization header
        $authHeaders = $this->getAuthorizationHeader($request);
        // Filter input data
        $input = filter_var_array($inputData, $filters);
        // Grant Type must be specified.
        if (!$input["grant_type"]) {
            throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_REQUEST, 'Invalid grant_type parameter or parameter missing');
        }
        // Authorize the client
        $clientCredentials = $this->getClientCredentials($inputData, $authHeaders);

        $client = $this->storage->getClient($clientCredentials[0]);

        if (!$client) {
            throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_CLIENT, 'The client credentials are invalid');
        }

        if ($this->storage->checkClientCredentials($client, $clientCredentials[1]) === false) {
            throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_CLIENT, 'The client credentials are invalid');
        }

        if (!$this->storage->checkRestrictedGrantType($client, $input["grant_type"])) {
            throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_UNAUTHORIZED_CLIENT, 'The grant type is unauthorized for this client_id');
        }

        // Do the granting
        switch ($input["grant_type"]) {
            case self::GRANT_TYPE_AUTH_CODE:
                // returns array('data' => data, 'scope' => scope)
                $stored = $this->grantAccessTokenAuthCode($client, $input);
                break;
            case self::GRANT_TYPE_USER_CREDENTIALS:
                // returns: true || array('scope' => scope)
                if($manually == true){
                    $stored = $this->grantAccessTokenMyUserCredentials($client, $input);
                }
                else{
                    $stored = $this->grantAccessTokenUserCredentials($client, $input);
                }
                break;
            case self::GRANT_TYPE_CLIENT_CREDENTIALS:
                // returns: true || array('scope' => scope)
                $stored = $this->grantAccessTokenClientCredentials($client, $input, $clientCredentials);
                break;
            case self::GRANT_TYPE_REFRESH_TOKEN:
                // returns array('data' => data, 'scope' => scope)
                $stored = $this->grantAccessTokenRefreshToken($client, $input);
                break;
            default:
                if (substr($input["grant_type"], 0, 4) !== 'urn:'
                    && !filter_var($input["grant_type"], FILTER_VALIDATE_URL)
                ) {
                    throw new OAuth2ServerException(
                        self::HTTP_BAD_REQUEST,
                        self::ERROR_INVALID_REQUEST,
                        'Invalid grant_type parameter or parameter missing'
                    );
                }

                // returns: true || array('scope' => scope)
                $stored = $this->grantAccessTokenExtension($client, $inputData, $authHeaders);
        }

        if (!is_array($stored)) {
            $stored = array();
        }

        // if no scope provided to check against $input['scope'] then application defaults are set
        // if no data is provided than null is set
        $stored += array('scope' => $this->getVariable(self::CONFIG_SUPPORTED_SCOPES, null), 'data' => null,
            'access_token_lifetime' => $this->getVariable(self::CONFIG_ACCESS_LIFETIME),
            'issue_refresh_token' => true, 'refresh_token_lifetime' => $this->getVariable(self::CONFIG_REFRESH_LIFETIME));

        $scope = $stored['scope'];
        if ($input["scope"]) {
            // Check scope, if provided
            if (!isset($stored["scope"]) || !$this->checkScope($input["scope"], $stored["scope"])) {
                throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_SCOPE, 'An unsupported scope was requested.');
            }
            $scope = $input["scope"];
        }

        $token = $this->createAccessToken($client, $stored['data'], $scope, $stored['access_token_lifetime'], $stored['issue_refresh_token'], $stored['refresh_token_lifetime']);
        return new Response(json_encode($token), 200, $this->getJsonHeaders());
    }

    /**
     * @param IOAuth2Client $client
     * @param array $input
     * @return array|bool
     * @throws OAuth2ServerException
     */
    public function grantAccessTokenUserCredentials(IOAuth2Client $client, array $input)
    {
        if (!($this->storage instanceof IOAuth2GrantUser)) {
            throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_UNSUPPORTED_GRANT_TYPE);
        }

        if (!$input["username"] || !$input["password"]) {
            throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_REQUEST, 'Missing parameters. "username" and "password" required');
        }

        $stored = $this->storage->checkUserCredentials($client, $input["username"], $input["password"]);

        if ($stored === false) {
            throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_GRANT, "Combination of Username and Password is not correct. Please try again.");
        }

        if($stored['data'] instanceof User){
            if(!$stored['data']->isEnabled()){
                if($stored['data']->getApproved() === NULL){
                    throw new OAuth2ServerException(self::HTTP_FORBIDDEN, 'user_awaiting_approval', "User Awaiting approval");
                }
                else{
                    throw new OAuth2ServerException(self::HTTP_FORBIDDEN, 'user_deactivate', "User Deactivate");
                }
            }
        }
        else{
            throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_GRANT, "Combination of Username and Password is not correct. Please try again.");
        }

        return $stored;
    }

    /**
     * @param IOAuth2Client $client
     * @param array $input
     * @return array|bool
     * @throws OAuth2ServerException
     */
    public function grantAccessTokenMyUserCredentials(IOAuth2Client $client, array $input)
    {
        if (!($this->storage instanceof IOAuth2GrantUser)) {
            throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_UNSUPPORTED_GRANT_TYPE);
        }

        if (!$input["username"] || !$input["password"]) {
            throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_REQUEST, 'Missing parameters. "username" and "password" required');
        }

        $stored = $this->storage->checkMyUserCredentials($client, $input["username"], $input["password"]);

        if ($stored === false) {
            throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_GRANT, "Combination of Username and Password is not correct. Please try again.");
        }

        if($stored['data'] instanceof User){
            if(!$stored['data']->isEnabled()){
                if($stored['data']->getApproved() === NULL){
                    throw new OAuth2ServerException(self::HTTP_FORBIDDEN, 'user_awaiting_approval', "User Awaiting approval");
                }
                else{
                    throw new OAuth2ServerException(self::HTTP_FORBIDDEN, 'user_deactivate', "User Deactivate");
                }
            }
        }
        else{
            throw new OAuth2ServerException(self::HTTP_BAD_REQUEST, self::ERROR_INVALID_GRANT, "Combination of Username and Password is not correct. Please try again.");
        }

        return $stored;
    }

    /**
     * @return array|mixed
     */
    protected function getJsonHeaders()
    {
        $headers = $this->getVariable(self::CONFIG_RESPONSE_EXTRA_HEADERS, array());
        $headers += array(
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        );
        return $headers;
    }
}
