<?php
/**
 * Created by PhpStorm.
 * Date: 23.04.18
 * Time: 16:48
 */

namespace AppBundle\Controller\Api\Admin;

use AppBundle\Entity\Logging;
use AppBundle\Entity\NotificationAdmin;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class ManageSystemController
 * @package AppBundle\Controller\Api\Admin
 *
 * @Rest\Route("manage_user")
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class ManageSystemController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/")
     * @SWG\Get(path="/api/admin/manage_user/",
     *   tags={"Admin Manage System"},
     *   security={true},
     *   summary="Get All Admins",
     *   description="The method for getting all admins",
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
     *      default="",
     *      description="search"
     *   ),
     *   @SWG\Parameter(
     *      name="orderBy",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="Name, Surname, Email, Role"
     *   ),
     *   @SWG\Parameter(
     *      name="orderSort",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="asc OR desc"
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
     *                      property="id",
     *                      type="integer"
     *                  ),
     *                  @SWG\Property(
     *                      property="firstName",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="lastName",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="phone",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="email",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="roles",
     *                      type="array",
     *                      @SWG\Items(type="string")
     *                  )
     *              )
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
     *     @SWG\Response(
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
    public function getAllAdminsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $admins = $em->getRepository("AppBundle:User")->findByRolesForSystem(['ROLE_SUPER_ADMIN','ROLE_ADMIN'], $request->query->all());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $admins,
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
     * @return Response
     *
     * @Rest\Post("/")
     * @SWG\Post(path="/api/admin/manage_user/",
     *   tags={"Admin Manage System"},
     *   security={true},
     *   summary="Create Admin",
     *   description="The method for created Admin",
     *   produces={"application/json"},
     *   consumes={"application/json"},
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
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="firstName",
     *              type="string",
     *              example="firstName",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="lastName",
     *              type="string",
     *              example="lastName",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="phone",
     *              type="string",
     *              example="123213123",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="email",
     *              type="string",
     *              example="email@gmail.com",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="role",
     *              type="string",
     *              example="ROLE_ADMIN | ROLE_SUPER_ADMIN",
     *              description="required"
     *          ),
     *      ),
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success. Admin Create",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="id",
     *              type="integer"
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="error",
     *              type="string"
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
    public function createAdminAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        if($request->request->has('firstName') && $request->request->has('lastName') && $request->request->has('email') && $request->request->has('phone')
            && $request->request->has('role') && ($request->request->get('role') == "ROLE_ADMIN" || $request->request->get('role') == "ROLE_SUPER_ADMIN")){
            $user = new User();
            $password = substr(md5(time()),0,6);
            $user->setRegisterDetails($request->request->get('role'), $request->request->get('firstName'), $request->request->get('lastName'), $request->request->get('email'), $request->request->get('phone'), $password);
            $user->setEnabled(true);
            $user->setApproved(true);
            $errors = $this->get('validator')->validate($user, null, array('registerCandidate'));
            if(count($errors) === 0){
                $em->persist($user);
                $em->flush();
                $notifyAdmin = new NotificationAdmin($user);
                $em->persist($notifyAdmin);
                $logging = new Logging($this->getUser(),24, $user->getFirstName()." ".$user->getLastName(), $user->getId());
                $em->persist($logging);
                $em->flush();

                $message = (new \Swift_Message('Welcome to Yes2Work!'))
                    ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView('emails/user/admin_registered.html.twig', [
                            'user' => $user,
                            'password' => $password,
                            'link' => $request->getSchemeAndHttpHost()
                        ]),
                        'text/html'
                    );
                try{
                    $this->get('mailer')->send($message);
                }catch(\Swift_TransportException $e){

                }

                $view = $this->view(['id'=>$user->getId()], Response::HTTP_OK);
            }
            else {
                $error_description = [];
                foreach ($errors as $er) {
                    $error_description[] = $er->getMessage();
                }
                $view = $this->view(['error'=>$error_description], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'all fields required'], Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     *
     * @Rest\Put("/{id}",requirements={"id"="\d+"})
     * @SWG\Put(path="/api/admin/manage_user/{id}",
     *   tags={"Admin Manage System"},
     *   security={true},
     *   summary="Edit Admin",
     *   description="The method for Edit Admin",
     *   produces={"application/json"},
     *   consumes={"application/json"},
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
     *      default="adminId",
     *      description="adminId"
     *   ),
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="firstName",
     *              type="string",
     *              example="firstName",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="lastName",
     *              type="string",
     *              example="lastName",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="phone",
     *              type="string",
     *              example="123213123",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="email",
     *              type="string",
     *              example="email@gmail.com",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="role",
     *              type="string",
     *              example="ROLE_ADMIN | ROLE_SUPER_ADMIN",
     *              description="required"
     *          ),
     *      ),
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success. Admin Update"
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="error",
     *              type="string"
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
     *   ),
     *   @SWG\Response(
     *      response=404,
     *      description="NOT FOUND",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="error",
     *              type="string"
     *          )
     *      )
     *   ),
     * )
     */
    public function updateAdminByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);
        if($user instanceof User and ($user->hasRole("ROLE_ADMIN") || $user->hasRole("ROLE_SUPER_ADMIN"))){
            if($request->request->has('firstName') && $request->request->has('lastName') && $request->request->has('email')  && $request->request->has('phone')
                && $request->request->has('role') && ($request->request->get('role') == "ROLE_ADMIN" || $request->request->get('role') == "ROLE_SUPER_ADMIN")){
                $user->setFirstName($request->request->get('firstName'));
                $user->setLastName($request->request->get('lastName'));
                $user->setEmail($request->request->get('email'));
                $user->setUsername($request->request->get('email'));
                $user->setPhone($request->request->get('phone'));
                $user->setRoles([$request->request->get('role')]);
                $errors = $this->get('validator')->validate($user, null, array('updateCandidate'));
                if(count($errors) === 0){
                    $em->persist($user);
                    $em->flush();
                    $logging = new Logging($this->getUser(),25, $user->getFirstName()." ".$user->getLastName(), $user->getId());
                    $em->persist($logging);
                    $em->flush();
                    $view = $this->view([], Response::HTTP_NO_CONTENT);
                }
                else {
                    $error_description = [];
                    foreach ($errors as $er) {
                        $error_description[] = $er->getMessage();
                    }
                    $view = $this->view(['error'=>$error_description], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>'all fields required'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'Admin not found or user not ADMIN'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     *
     * @Rest\Delete("/{id}",requirements={"id"="\d+"})
     * @SWG\Delete(path="/api/admin/manage_user/{id}",
     *   tags={"Admin Manage System"},
     *   security={true},
     *   summary="Delete Admin",
     *   description="The method for Delete Admin",
     *   produces={"application/json"},
     *   consumes={"application/json"},
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
     *      default="adminId",
     *      description="adminId"
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success. Admin Delete"
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="error",
     *              type="string"
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
     *   ),
     *   @SWG\Response(
     *      response=404,
     *      description="NOT FOUND",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="error",
     *              type="string"
     *          )
     *      )
     *   ),
     * )
     */
    public function removeAdminByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);
        if($user instanceof User and ($user->hasRole("ROLE_ADMIN") || $user->hasRole("ROLE_SUPER_ADMIN"))){
            if($user->getId() != $this->getUser()->getId()){
                $em->remove($user);
                $em->flush();
                $logging = new Logging($this->getUser(),26, $user->getFirstName()." ".$user->getLastName());
                $em->persist($logging);
                $em->flush();
                $view = $this->view([], Response::HTTP_NO_CONTENT);
            }
            else{
                $view = $this->view(['error'=>'can not delete yourself'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'Admin not found or user not ADMIN'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

}
