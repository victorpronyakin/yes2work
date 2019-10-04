<?php
/**
 * Created by PhpStorm.
 * Date: 18.04.18
 * Time: 16:49
 */

namespace AppBundle\Controller\Api\Admin;


use AppBundle\Entity\CompanyDetails;
use AppBundle\Entity\Logging;
use AppBundle\Entity\NotificationClient;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class MainController
 * @package AppBundle\Controller\Api\Admin
 * @Rest\Route("business")
 * @Security("has_role('ROLE_ADMIN')")
 */
class ClientController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/")
     * @SWG\Get(path="/api/admin/business/",
     *   tags={"Admin Business"},
     *   security={true},
     *   summary="Get All Business Profile Details",
     *   description="The method for getting all business profile details for admin",
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
     *      name="csv",
     *      in="query",
     *      required=false,
     *      type="boolean",
     *      default=false,
     *      description="use for export csv, for use csv = true, response only what in items"
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
     *      description="search by firstName or lastName or email or phone or companyName"
     *   ),
     *   @SWG\Parameter(
     *      name="orderBy",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="Name, Company, Email, Phone"
     *   ),
     *   @SWG\Parameter(
     *      name="orderSort",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="asc OR desc"
     *   ),
     *   @SWG\Parameter(
     *      name="jobTitle",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="search by jobTitle"
     *   ),
     *   @SWG\Parameter(
     *      name="address",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="search by company Address"
     *   ),
     *   @SWG\Parameter(
     *      name="jse",
     *      in="query",
     *      required=false,
     *      type="boolean",
     *      description="search by JSE"
     *   ),
     *   @SWG\Parameter(
     *      name="industry",
     *      in="query",
     *      required=false,
     *      type="integer",
     *      default="",
     *      description="search by industry. 1 = Financial Services, 2 = Non-Financial Services"
     *   ),
     *   @SWG\Parameter(
     *      name="size",
     *      in="query",
     *      required=false,
     *      type="integer",
     *      default="",
     *      description="search by industry. 1= 1-10E, 2= 11-50E, 3= 50-200E, 4= 200-1000E, 5= >1000E"
     *   ),
     *   @SWG\Parameter(
     *      name="enabled",
     *      in="query",
     *      required=false,
     *      type="boolean",
     *      description="search by status"
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
     *                      property="agentName",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="companyName",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="approved",
     *                      type="boolean"
     *                  ),
     *                  @SWG\Property(
     *                      property="enabled",
     *                      type="boolean"
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
    public function getAllClientDetailsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $clients = $em->getRepository("AppBundle:User")->getAllClient($request->query->all());

        if($request->query->getBoolean('csv', false) == false){
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $clients,
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
        }
        else{
            $result = [];
            if(!empty($clients)){
                foreach ($clients as $client){

                    $name = '';
                    if(isset($client['firstName'])){
                        $name .= $client['firstName'].' ';
                    }
                    if(isset($client['lastName'])){
                        $name .= $client['lastName'];
                    }

                    $result[] = [
                        'name' => $name,
                        'company' => (isset($client['companyName'])) ? $client['companyName'] : '',
                        'email' => (isset($client['email'])) ? $client['email'] : '',
                        'phone' => (isset($client['phone'])) ? $client['phone'] : '',
                        'agentName' => (isset($client['agentName'])) ? $client['agentName'] : '',
                        'active' => (isset($client['enabled'])) ? $client['enabled'] : '',
                    ];
                }
            }
            $view = $this->view($result, Response::HTTP_OK);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/full")
     * @SWG\Get(path="/api/admin/business/full",
     *   tags={"Admin Business"},
     *   security={true},
     *   summary="Get All Business Profile Full Details",
     *   description="The method for getting all business profile full details for admin",
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
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              type="object",
     *              @SWG\Property(
     *                  property="id",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="companyName",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="jobTitle",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="address",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressCountry",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressState",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressZipCode",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressCity",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressSuburb",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressStreet",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressStreetNumber",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressBuildName",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressUnit",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="companySize",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="jse",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="industry",
     *                  type="array",
     *                  @SWG\Items(type="string")
     *              ),
     *              @SWG\Property(
     *                  property="description",
     *                  type="string"
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
    public function getAllFullClientDetailsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $clients = $em->getRepository("AppBundle:User")->getAllClientFullDetails();

        $view = $this->view($clients, Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Post("/")
     * @SWG\Post(path="/api/admin/business/",
     *   tags={"Admin Business"},
     *   security={true},
     *   summary="Create Business Profile",
     *   description="The method for creating business profile  for admin",
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
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="user",
     *              type="object",
     *              @SWG\Property(
     *                  property="firstName",
     *                  type="string",
     *                  example="firstName",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="lastName",
     *                  type="string",
     *                  example="lastName",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="jobTitle",
     *                  type="string",
     *                  example="jobTitle",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="phone",
     *                  type="string",
     *                  example="phone",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="email",
     *                  type="string",
     *                  example="email@email.com",
     *                  description="required"
     *              )
     *          ),
     *          @SWG\Property(
     *              property="company",
     *              type="object",
     *              @SWG\Property(
     *                  property="name",
     *                  type="string",
     *                  example="name",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="address",
     *                  type="string",
     *                  example="address",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressCountry",
     *                  type="string",
     *                  example="addressCountry",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressState",
     *                  type="string",
     *                  example="addressState",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressZipCode",
     *                  type="string",
     *                  example="addressZipCode",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressCity",
     *                  type="string",
     *                  example="addressCity",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressSuburb",
     *                  type="string",
     *                  example="addressSuburb",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressStreet",
     *                  type="string",
     *                  example="addressStreet",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressStreetNumber",
     *                  type="string",
     *                  example="addressStreetNumber",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressBuildName",
     *                  type="string",
     *                  example="addressBuildName",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressUnit",
     *                  type="string",
     *                  example="addressUnit",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="companySize",
     *                  type="integer",
     *                  description="required. 1= 1-10E, 2= 11-50E, 3= 50-200E, 4= 200-1000E,5= >1000E",
     *                  example=1
     *              ),
     *              @SWG\Property(
     *                  property="jse",
     *                  type="boolean",
     *                  description="required. true|false",
     *                  example=true
     *              ),
     *              @SWG\Property(
     *                  property="industry",
     *                  type="array",
     *                  @SWG\Items(type="string")
     *              ),
     *              @SWG\Property(
     *                  property="description",
     *                  type="string",
     *                  description="required. description",
     *                  example="description"
     *              ),
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=201,
     *      description="Success. Business Profile Create",
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad request",
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
    public function createClientAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        if($request->request->has('user') && $request->request->has('company')){
            $profile = $request->request->get('user');
            $companyData = $request->request->get('company');
            if(isset($profile['firstName']) && isset($profile['lastName']) && isset($profile['email']) && isset($profile['phone']) && isset($profile['jobTitle'])
                && isset($companyData['name'])
            ){
                $user = new User();
                $password = substr(md5(time()),0,6);
                $user->setRegisterDetails("ROLE_CLIENT", $profile['firstName'], $profile['lastName'], $profile['email'], $profile['phone'], $password, $profile['jobTitle']);
                $user->setEnabled(true);
                $user->setApproved(true);
                $errors = $this->get('validator')->validate($user, null, array('registerClient'));
                if(count($errors) === 0){
                    $companyDetails = new CompanyDetails($user, $companyData['name']);
                    $companyDetails->update($companyData);
                    $errorsCheckCompany = $this->get('validator')->validate($companyDetails, null, array('updateCompany'));
                    if(count($errorsCheckCompany) === 0){
                        $em->persist($user);
                        $em->persist($companyDetails);
                        $em->flush();
                        $notificationClient= new NotificationClient($user);
                        $em->persist($notificationClient);
                        $em->flush();
                        $message = (new \Swift_Message('Welcome to Yes2Work!'))
                            ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                            ->setTo($user->getEmail())
                            ->setBody(
                                $this->renderView('emails/user/client_registered.html.twig', [
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
                        $logging = new Logging($this->getUser(),10, $user->getFirstName()." ".$user->getLastName(), $user->getId());
                        $em->persist($logging);
                        $em->flush();
                        $view = $this->view([], Response::HTTP_CREATED);
                        return $this->handleView($view);
                    }
                    else {
                        $error_description = [];
                        foreach ($errorsCheckCompany as $er) {
                            $error_description[] = $er->getMessage();
                        }
                        $view = $this->view(['error'=>$error_description], Response::HTTP_BAD_REQUEST);
                        return $this->handleView($view);
                    }
                }
                else {
                    $error_description = [];
                    foreach ($errors as $er) {
                        $error_description[] = $er->getMessage();
                    }
                    $view = $this->view(['error'=>$error_description], Response::HTTP_BAD_REQUEST);
                    return $this->handleView($view);
                }
            }
            else{
                $view = $this->view(['error'=>'all user fields and companyName is required'], Response::HTTP_BAD_REQUEST);
                return $this->handleView($view);
            }
        }
        else{
            $view = $this->view(['error'=>'field user and company is required'], Response::HTTP_BAD_REQUEST);
            return $this->handleView($view);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Rest\Get("/{id}",requirements={"id"="\d+"})
     * @SWG\Get(path="/api/admin/business/{id}",
     *   tags={"Admin Business"},
     *   security={true},
     *   summary="Get Business Profile Details",
     *   description="The method for getting business profile details for admin",
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
     *      default="clientId",
     *      description="Client ID"
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="user",
     *              type="object",
     *              @SWG\Property(
     *                  property="firstName",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="lastName",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="jobTitle",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="phone",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="email",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="agentName",
     *                  type="string",
     *                  example="agentName"
     *              )
     *          ),
     *          @SWG\Property(
     *              property="company",
     *              type="object",
     *              @SWG\Property(
     *                  property="name",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="address",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressCountry",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressState",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressZipCode",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressCity",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressSuburb",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressStreet",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressStreetNumber",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressBuildName",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="addressUnit",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="companySize",
     *                  type="integer",
     *                  description="1= 1-10E, 2= 11-50E, 3= 50-200E, 4= 200-1000E,5= >1000E"
     *              ),
     *              @SWG\Property(
     *                  property="jse",
     *                  type="boolean",
     *                  description="0|1"
     *              ),
     *              @SWG\Property(
     *                  property="industry",
     *                  type="array",
     *                  @SWG\Items(type="string")
     *              ),
     *              @SWG\Property(
     *                  property="description",
     *                  type="string"
     *              ),
     *          ),
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
     *      description="Not found",
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
    public function getClientDetailsByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);
        if($user instanceof User && $user->hasRole("ROLE_CLIENT")){
            $profileInfo = $em->getRepository("AppBundle:User")->getBusinessProfile($id);
            $companyDetails = $em->getRepository("AppBundle:CompanyDetails")->getBusinessCompanyDetails($id);

            $view = $this->view(['user'=>$profileInfo,'company'=>$companyDetails], Response::HTTP_OK);
            return $this->handleView($view);
        }

        $view = $this->view(['error'=>'Client Not found'], Response::HTTP_NOT_FOUND);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     *
     * @Rest\Put("/{id}",requirements={"id"="\d+"})
     * @SWG\Put(path="/api/admin/business/{id}",
     *   tags={"Admin Business"},
     *   security={true},
     *   summary="Update Business Profile Details",
     *   description="The method for updating business profile details for admin",
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
     *    @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      type="integer",
     *      default="clientID",
     *      description="Client Id"
     *   ),
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="user",
     *              type="object",
     *              @SWG\Property(
     *                  property="firstName",
     *                  type="string",
     *                  example="firstName",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="lastName",
     *                  type="string",
     *                  example="lastName",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="jobTitle",
     *                  type="string",
     *                  example="jobTitle",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="phone",
     *                  type="string",
     *                  example="phone",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="email",
     *                  type="string",
     *                  example="email@email.com",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="agentName",
     *                  type="string",
     *                  example="agentName"
     *              )
     *          ),
     *          @SWG\Property(
     *              property="company",
     *              type="object",
     *              @SWG\Property(
     *                  property="name",
     *                  type="string",
     *                  example="name",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="address",
     *                  type="string",
     *                  example="address",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressCountry",
     *                  type="string",
     *                  example="addressCountry",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressState",
     *                  type="string",
     *                  example="addressState",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressZipCode",
     *                  type="string",
     *                  example="addressZipCode",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressCity",
     *                  type="string",
     *                  example="addressCity",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressSuburb",
     *                  type="string",
     *                  example="addressSuburb",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressStreet",
     *                  type="string",
     *                  example="addressStreet",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressStreetNumber",
     *                  type="string",
     *                  example="addressStreetNumber",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressBuildName",
     *                  type="string",
     *                  example="addressBuildName",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressUnit",
     *                  type="string",
     *                  example="addressUnit",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="companySize",
     *                  type="integer",
     *                  description="required. 1= 1-10E, 2= 11-50E, 3= 50-200E, 4= 200-1000E,5= >1000E",
     *                  example=1
     *              ),
     *              @SWG\Property(
     *                  property="jse",
     *                  type="boolean",
     *                  description="required. true|false",
     *                  example=true
     *              ),
     *              @SWG\Property(
     *                  property="industry",
     *                  type="array",
     *                  @SWG\Items(type="string")
     *              ),
     *              @SWG\Property(
     *                  property="description",
     *                  type="string",
     *                  description="required.",
     *                  example="description"
     *              ),
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success. Details Updated",
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad request",
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
     *      description="Not Found",
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
    public function updateClientDetailsByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);
        if($user instanceof User && $user->hasRole('ROLE_CLIENT')){
            if($request->request->has('user') && $request->request->has('company')){
                $profile = $request->request->get('user');
                if(isset($profile['firstName']) && isset($profile['lastName']) && isset($profile['email']) && isset($profile['phone']) && isset($profile['jobTitle'])){
                    $user->setFirstName($profile['firstName']);
                    $user->setLastName($profile['lastName']);
                    $user->setEmail($profile['email']);
                    $user->setUsername($profile['email']);
                    $user->setPhone($profile['phone']);
                    $user->setJobTitle($profile['jobTitle']);
                    $user->setAgentName((isset($profile['agentName'])) ? $profile['agentName'] : null);
                    $errors = $this->get('validator')->validate($user, null, array('updateClient'));
                    if(count($errors) === 0){
                        $em->persist($user);
                        $companyData = $request->request->get('company');
                        $companyDetails = $em->getRepository("AppBundle:CompanyDetails")->findOneBy(['user'=>$user]);
                        $companyDetails->update($companyData);
                        $errors = $this->get('validator')->validate($companyDetails, null, array('updateCompany'));
                        if(count($errors) === 0){
                            $em->persist($companyDetails);
                            $em->flush();
                            $logging = new Logging($this->getUser(),11, $user->getFirstName()." ".$user->getLastName(), $user->getId());
                            $em->persist($logging);
                            $em->flush();
                            $view = $this->view([], Response::HTTP_NO_CONTENT);
                            return $this->handleView($view);
                        }
                        else {
                            $error_description = [];
                            foreach ($errors as $er) {
                                $error_description[] = $er->getMessage();
                            }
                            $view = $this->view(['error'=>$error_description], Response::HTTP_BAD_REQUEST);
                            return $this->handleView($view);
                        }

                    }
                    else {
                        $error_description = [];
                        foreach ($errors as $er) {
                            $error_description[] = $er->getMessage();
                        }
                        $view = $this->view(['error'=>$error_description], Response::HTTP_BAD_REQUEST);
                        return $this->handleView($view);
                    }
                }
                else{
                    $view = $this->view(['error'=>'all profile field is required'], Response::HTTP_BAD_REQUEST);
                    return $this->handleView($view);
                }
            }
            else{
                $view = $this->view(['error'=>'field user and company is required'], Response::HTTP_BAD_REQUEST);
                return $this->handleView($view);
            }
        }
        else{
            $view = $this->view(['error'=>'Client Not Found'], Response::HTTP_NOT_FOUND);
            return $this->handleView($view);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     *
     * @Rest\Patch("/{id}",requirements={"id"="\d+"})
     * @SWG\Patch(path="/api/admin/business/{id}",
     *   tags={"Admin Business"},
     *   security={true},
     *   summary="Update Business Status",
     *   description="The method for updating Business status for admin",
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
     *      default="clientId",
     *      description="Client ID"
     *   ),
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="enabled",
     *              type="boolean",
     *              example=false,
     *              description="required"
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success.Status updated"
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
     *      description="Not found",
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
    public function editClientStatusByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);
        if($user instanceof User and $user->hasRole('ROLE_CLIENT')){
            if($request->request->has('enabled') && is_bool($request->request->get('enabled'))){
                $user->setEnabled($request->request->get('enabled'));
                $em->persist($user);
                $em->flush();
                if($request->request->get('enabled') == true){
                    $logging = new Logging($this->getUser(),12, $user->getFirstName()." ".$user->getLastName(), $user->getId());
                    $em->persist($logging);
                    $em->flush();
                }
                else{
                    $logging = new Logging($this->getUser(),13, $user->getFirstName()." ".$user->getLastName(), $user->getId());
                    $em->persist($logging);
                    $em->flush();
                }
                $view = $this->view([], Response::HTTP_NO_CONTENT);
            }
            else{
                $view = $this->view(['error'=>'field enabled should be empty and should be boolean type'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'Client Not found or user not has ROLE_CLIENT'], Response::HTTP_NOT_FOUND);
        }
        return $this->handleView($view);

    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     *
     * @Rest\Delete("/{id}",requirements={"id"="\d+"})
     * @SWG\Delete(path="/api/admin/business/{id}",
     *   tags={"Admin Business"},
     *   security={true},
     *   summary="Delete Business Profile",
     *   description="The method for Delete Business for admin",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      type="integer",
     *      default="clintID",
     *      description="client ID"
     *   ),
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
     *   @SWG\Response(
     *      response=204,
     *      description="Success."
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
     *      description="Not Found",
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
    public function deleteClientAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);
        if($user instanceof User && $user->hasRole("ROLE_CLIENT")){
            $em->remove($user);
            $em->flush();
            $logging = new Logging($this->getUser(),14, $user->getFirstName()." ".$user->getLastName());
            $em->persist($logging);
            $em->flush();
            $view = $this->view([], Response::HTTP_NO_CONTENT);
        }
        else{
            $view = $this->view(['error'=>'Client Not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/approve")
     * @SWG\Get(path="/api/admin/business/approve",
     *   tags={"Admin Business"},
     *   security={true},
     *   summary="Get Business Profile when need Approve",
     *   description="The method for getting Business Profile when need Approve",
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
     *      description="search by firstName or lastName or email or phone or companyName"
     *   ),
     *   @SWG\Parameter(
     *      name="orderBy",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="Name, Company, Email, Phone"
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
     *                      property="companyName",
     *                      type="string"
     *                  ),
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
    public function getClientApproveAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $clientsApprove = $em->getRepository("AppBundle:User")->getClientApprove($request->query->all());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $clientsApprove,
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
     *
     * @Rest\Patch("/{id}/approve",requirements={"id"="\d+"})
     * @SWG\Patch(path="/api/admin/business/{id}/approve",
     *   tags={"Admin Business"},
     *   security={true},
     *   summary="Approve Business Account for Admin",
     *   description="The method for Approve Business Account for Admin",
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
     *      default="clientID",
     *      description="CLIENT ID"
     *   ),
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="approved",
     *              type="boolean",
     *              example=true,
     *              description="required"
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success. Client Approved or Decline",
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad request",
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
     *      description="Not FOUND",
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
    public function clientApprovedAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository("AppBundle:User")->find($id);
        if($client instanceof User){
            if($client->hasRole("ROLE_CLIENT")){
                if($request->request->has('approved') && is_bool($request->request->get('approved'))){
                    $client->setApproved($request->request->get('approved'));
                    $client->setEnabled($request->request->get('approved'));
                    $em->persist($client);
                    $em->flush();
                    if($client->getApproved() === true){
                        $message = (new \Swift_Message('Welcome to Yes2Work!'))
                            ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                            ->setTo($client->getEmail())
                            ->setBody(
                                $this->renderView('emails/client/client_approved.html.twig', [
                                    'user' => $client,
                                    'link' => $request->getSchemeAndHttpHost()
                                ]),
                                'text/html'
                            );
                        $logging = new Logging($this->getUser(),15, $client->getFirstName()." ".$client->getLastName(), $client->getId());
                        $em->persist($logging);
                        $em->flush();
                    }
                    else{
                        $message = (new \Swift_Message('Yes2Work Registration declined'))
                            ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                            ->setTo($client->getEmail())
                            ->setBody(
                                $this->renderView('emails/client/client_decline.html.twig', [
                                    'user' => $client,
                                    'link' => $request->getSchemeAndHttpHost().'/register/candidate'
                                ]),
                                'text/html'
                            );
                        $logging = new Logging($this->getUser(),16, $client->getFirstName()." ".$client->getLastName(), $client->getId());
                        $em->persist($logging);
                        $em->flush();
                    }
                    try{
                        $this->get('mailer')->send($message);
                    }catch(\Swift_TransportException $e){

                    }

                    $view = $this->view([], Response::HTTP_NO_CONTENT);
                }
                else{
                    $view = $this->view(['error'=>'field approved is required or npt boolean'], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>'user NOT ROLE_CLIENT'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'User not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->handleView($view);
    }
}
