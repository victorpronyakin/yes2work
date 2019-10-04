<?php

namespace AppBundle\Controller\Api\Admin;

use AppBundle\Entity\Applicants;
use AppBundle\Entity\CompanyDetails;
use AppBundle\Entity\Job;
use AppBundle\Entity\NotificationCandidate;
use AppBundle\Entity\User;
use AppBundle\Helper\HelpersClass;
use AppBundle\Security\OAuth2;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class SwitchUserController
 * @package AppBundle\Controller\Api\Admin
 *
 * @Rest\Route("/switch_user")
 * @Security("has_role('ROLE_ADMIN')")
 */
class SwitchUserController extends FOSRestController
{
	/**
	 * @param Request $request
	 * @return Response
	 * @throws
	 *
	 * @Rest\Get("/")
	 * @SWG\Get(path="/api/admin/switch_user/",
	 *   tags={"Admin Switch User"},
	 *   security={true},
	 *   summary="Get All Candidates And Clients",
	 *   description="The method for getting all Candidate and Client for admin",
	 *   produces={"application/json"},
	 *   @SWG\Parameter(
	 *      name="Authorization",
	 *      in="header",
	 *      required=true,
	 *      type="string",
	 *      default="Bearer <token>",
	 *      description="Authorization Token"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="Content-Type",
	 *      in="header",
	 *      required=true,
	 *      type="string",
	 *      default="application/json",
	 *      description="Content Type"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="page",
	 *      in="query",
	 *      required=false,
	 *      type="integer",
	 *      default=1,
	 *      description="pagination page"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="limit",
	 *      in="query",
	 *      required=false,
	 *      type="integer",
	 *      default=20,
	 *      description="pagination limit"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="search",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      description="find by firstName or lastName or email or phone"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="orderBy",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      description="id / firstName / lastName / phone /  email"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="orderSort",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      description="ASC / DESC"
	 *   ),
	 *   @SWG\Response(
	 *      response=200,
	 *      description="Success.",
	 *      @SWG\Schema(
	 *          type="object",
	 *          @SWG\Property(
	 *              property="items",
	 *              type="array",
	 *              @SWG\Items(
	 *                  type="object",
	 *                  @SWG\Property(
	 *                      property="profile",
	 *                      type="object",
	 *                      @SWG\Property(
	 *                          property="id",
	 *                          type="integer"
	 *                      ),
	 *                      @SWG\Property(
	 *                          property="firstName",
	 *                          type="string"
	 *                      ),
	 *                      @SWG\Property(
	 *                          property="lastName",
	 *                          type="string"
	 *                      ),
	 *                      @SWG\Property(
	 *                          property="phone",
	 *                          type="string"
	 *                      ),
	 *                      @SWG\Property(
	 *                          property="email",
	 *                          type="string"
	 *                      ),
	 *                  )
	 *              ),
	 *          ),
	 *          @SWG\Property(
	 *              type="object",
	 *              property="pagination",
	 *              @SWG\Property(
	 *                  type="integer",
	 *                  property="current_page_number"
	 *              ),
	 *              @SWG\Property(
	 *                  type="integer",
	 *                  property="total_count"
	 *              ),
	 *          )
	 *      )
	 *   ),
	 *   @SWG\Response(
	 *      response=401,
	 *      description="Unauthorized",
	 *      @SWG\Schema(
	 *          type="object",
	 *          @SWG\Property(
	 *              property="error",
	 *              type="string"
	 *          ),
	 *          @SWG\Property(
	 *              property="error_error_description",
	 *              type="string"
	 *          )
	 *      )
	 *   ),
	 *   @SWG\Response(
	 *      response=403,
	 *      description="Forbidden(Access Denied)",
	 *      @SWG\Schema(
	 *          type="object",
	 *          @SWG\Property(
	 *              property="error",
	 *              type="string"
	 *          )
	 *      )
	 *   )
	 * )
	 */
    public function getAllCandidatesClientsAction(Request $request){
		$params = $request->query->all();
		$em = $this->getDoctrine()->getManager();
		$candidates = $em->getRepository("AppBundle:User")->getAllCandidatesAndClients($params);
		$paginator  = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
			$candidates,
			($request->query->getInt('page', 1) > 0) ? $request->query->getInt('page', 1) : 1,
			($request->query->getInt('limit', 20) > 0) ? $request->query->getInt('limit', 20) : 20
		);
		$view = $this->view([
			'items'=>$pagination->getItems(),
			'pagination' => [
				'current_page_number' => $pagination->getCurrentPageNumber(),
				'total_count' => $pagination->getTotalItemCount(),
			]
		], Response::HTTP_OK);
		return $this->handleView($view);
	}

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \OAuth2\OAuth2ServerException
     *
     * @Rest\Post("/{id}",requirements={"id"="\d+"})
     * @SWG\Post(path="/api/admin/switch_user/{id}",
     *   tags={"Admin Switch User"},
     *   security={true},
     *   summary="Get User Token",
     *   description="The method for getting user token for auth",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="Bearer <token>",
     *      description="Authorization Token"
     *   ),
     *   @SWG\Parameter(
     *      name="Content-Type",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="application/json",
     *      description="Content Type"
     *   ),
     *   @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      type="integer",
     *      description="userID"
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              type="integer",
     *              property="id",
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="access_token",
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="refresh_token",
     *          ),
     *          @SWG\Property(
     *              type="integer",
     *              property="expires_in",
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="role",
     *          ),
     *     )
     *   ),
     *   @SWG\Response(
     *      response=401,
     *      description="Unauthorized",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="error",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="error_error_description",
     *              type="string"
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=403,
     *      description="Forbidden(Access Denied)",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="error",
     *              type="string"
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=500,
     *      description="Internal Server Error",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="code",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="message",
     *              type="string"
     *          ),
     *      )
     *   )
     * )
     */
    public function getUserTokenAction(Request $request, $id){
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository("AppBundle:User")->find($id);
		if($user instanceof User){
            $client = $em->getRepository("AppBundle:Client")->findOneBy([]);
            $grantRequest = new Request(array(
                'client_id'  => $client->getId()."_".$client->getRandomId(),
                'client_secret' => $client->getSecret(),
                'grant_type' => OAuth2::GRANT_TYPE_USER_CREDENTIALS,
                'username' => $user->getUsername(),
                'password' => $user->getPassword()
            ));

            $tokenResponse = $this->get('fos_oauth_server.server')->grantAccessToken($grantRequest, true);
            $token = $tokenResponse->getContent();

            $view = $this->view(json_decode($token, true), Response::HTTP_OK);
        }
		else{
            $view = $this->view(['error'=>'Candidate Not found or user not has ROLE_CANDIDATE'], Response::HTTP_NOT_FOUND);
        }
		return $this->handleView($view);
	}
}
