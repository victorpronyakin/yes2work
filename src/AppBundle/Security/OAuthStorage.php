<?php


namespace AppBundle\Security;

use FOS\OAuthServerBundle\Model\ClientInterface;
use FOS\OAuthServerBundle\Storage\OAuthStorage as BaseOAuthStorage;
use OAuth2\Model\IOAuth2Client;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class OAuthStorage
 * @package AppBundle\Security
 */
class OAuthStorage extends BaseOAuthStorage
{
    /**
     * @param IOAuth2Client $client
     * @param $username
     * @param $password
     * @return array|bool
     */
    public function checkMyUserCredentials(IOAuth2Client $client, $username, $password)
    {
        if (!$client instanceof ClientInterface) {
            throw new \InvalidArgumentException('Client has to implement the ClientInterface');
        }

        try {
            $user = $this->userProvider->loadUserByUsername($username);
        } catch (AuthenticationException $e) {
            return false;
        }

        if (null !== $user) {

            if ($user->getPassword() == $password) {
                return array(
                    'data' => $user,
                );
            }
        }

        return false;
    }
}
