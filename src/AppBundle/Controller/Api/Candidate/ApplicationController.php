<?php
/**
 * Created by PhpStorm.
 * Date: 04.05.18
 * Time: 11:23
 */

namespace AppBundle\Controller\Api\Candidate;

use AppBundle\Entity\Applicants;
use AppBundle\Entity\CompanyDetails;
use AppBundle\Entity\Interviews;
use AppBundle\Entity\Job;
use AppBundle\Entity\Opportunities;
use AppBundle\Entity\ProfileDetails;
use AppBundle\Entity\User;
use AppBundle\Helper\HelpersClass;
use AppBundle\Helper\SendEmail;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class ApplicationController
 * @package AppBundle\Controller\Api\Candidate
 *
 * @Rest\Route("application")
 * @Security("has_role('ROLE_CANDIDATE')")
 */
class ApplicationController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/")
     * @SWG\Get(path="/api/candidate/application/",
     *   tags={"Candidate Application"},
     *   security={true},
     *   summary="Get application",
     *   description="The method for Getting application for candidate",
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
     *      name="startDate",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="2018-09-09",
     *      description="startDate"
     *   ),
     *   @SWG\Parameter(
     *      name="endDate",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="2018-09-09",
     *      description="endDate"
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="awaiting",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  ),
     *                  @SWG\Property(
     *                      property="clientID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="industry",
     *                      type="array",
     *                      @SWG\Items(type="string")
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="location",
     *                      type="string",
     *                      example="location",
     *                  ),
     *                  @SWG\Property(
     *                      property="addressCity",
     *                      type="string",
     *                      example="addressCity",
     *                  ),
     *              )
     *          ),
     *          @SWG\Property(
     *              property="successful",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  ),
     *                  @SWG\Property(
     *                      property="clientID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="company",
     *                      type="string",
     *                      example="company",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="location",
     *                      type="string",
     *                      example="location",
     *                  ),
     *                  @SWG\Property(
     *                      property="addressCity",
     *                      type="string",
     *                      example="addressCity",
     *                  ),
     *              )
     *          ),
     *          @SWG\Property(
     *              property="decline",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  ),
     *                  @SWG\Property(
     *                      property="clientID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="company",
     *                      type="string",
     *                      example="company",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="location",
     *                      type="string",
     *                      example="location",
     *                  ),
     *                  @SWG\Property(
     *                      property="addressCity",
     *                      type="string",
     *                      example="addressCity",
     *                  ),
     *              )
     *          ),
     *          @SWG\Property(
     *              property="candidateAddress",
     *              type="string",
     *              example="candidateAddress"
     *          )
     *      ),
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
    public function getApplicationAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $newApplicants = $em->getRepository("AppBundle:Applicants")->getApplicantsByCandidateWithStatus($user->getId(),1,$request->query->all());

        $result['awaiting'] = $this->transformDetails($newApplicants, $em);
        $params = array_merge($request->query->all(),['check'=>true]);
        $successfulApplicants = $em->getRepository("AppBundle:Applicants")->getApplicantsByCandidateWithStatus($user->getId(),3,$params);
        $result['successful'] = $this->transformDetails($successfulApplicants, $em);

        $declinedApplicants = $em->getRepository("AppBundle:Applicants")->getApplicantsByCandidateWithStatus($user->getId(),4,$request->query->all());
        $result['decline'] = $this->transformDetails($declinedApplicants, $em);

        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);

        $result['candidateAddress'] = ($profileDetails instanceof ProfileDetails) ? $profileDetails->getHomeAddress() : null;

        $view = $this->view($result, Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/awaiting")
     * @SWG\Get(path="/api/candidate/application/awaiting",
     *   tags={"Candidate Application"},
     *   security={true},
     *   summary="Get Awaiting approved application",
     *   description="The method for Getting Awaiting approved application for candidate",
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
     *      name="startDate",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="2018-09-09",
     *      description="startDate"
     *   ),
     *   @SWG\Parameter(
     *      name="endDate",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="2018-09-09",
     *      description="endDate"
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
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  ),
     *                  @SWG\Property(
     *                      property="clientID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="industry",
     *                      type="array",
     *                      @SWG\Items(type="string")
     *                  ),
     *                  @SWG\Property(
     *                      property="companyName",
     *                      type="string",
     *                      example="companyName"
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="location",
     *                      type="string",
     *                      example="location",
     *                  ),
     *                  @SWG\Property(
     *                      property="addressCity",
     *                      type="string",
     *                      example="addressCity",
     *                  ),
     *              )
     *          ),
     *          @SWG\Property(
     *              property="candidateAddress",
     *              type="string",
     *              example="candidateAddress"
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
     *      ),
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
    public function getAwaitingApprovedAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $newApplicants = $em->getRepository("AppBundle:Applicants")->getApplicantsByCandidateWithStatus($user->getId(),1,$request->query->all());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $newApplicants,
            ($request->query->getInt('page', 1) > 0) ? $request->query->getInt('page', 1) : 1,
            ($request->query->getInt('limit', 20) > 0) ? $request->query->getInt('limit', 20) : 20
        );
        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);

        $view = $this->view([
            'items'=>$this->transformDetails($pagination->getItems(), $em),
            'candidateAddress' => ($profileDetails instanceof ProfileDetails) ? $profileDetails->getHomeAddress() : null,
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
     * @Rest\Get("/successful")
     * @SWG\Get(path="/api/candidate/application/successful",
     *   tags={"Candidate Application"},
     *   security={true},
     *   summary="Get successful application",
     *   description="The method for Getting successful application for candidate",
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
     *      name="startDate",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="2018-09-09",
     *      description="startDate"
     *   ),
     *   @SWG\Parameter(
     *      name="endDate",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="2018-09-09",
     *      description="endDate"
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
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  ),
     *                  @SWG\Property(
     *                      property="clientID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="industry",
     *                      type="array",
     *                      @SWG\Items(type="string")
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="location",
     *                      type="string",
     *                      example="location",
     *                  ),
     *                  @SWG\Property(
     *                      property="addressCity",
     *                      type="string",
     *                      example="addressCity",
     *                  ),
     *              )
     *          ),
     *          @SWG\Property(
     *              property="candidateAddress",
     *              type="string",
     *              example="candidateAddress"
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
     *      ),
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
    public function getSuccessfulAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $params = array_merge($request->query->all(),['check'=>true]);
        $successfulApplicants = $em->getRepository("AppBundle:Applicants")->getApplicantsByCandidateWithStatus($user->getId(),3,$params);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $successfulApplicants,
            ($request->query->getInt('page', 1) > 0) ? $request->query->getInt('page', 1) : 1,
            ($request->query->getInt('limit', 20) > 0) ? $request->query->getInt('limit', 20) : 20
        );

        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);

        $view = $this->view([
            'items'=>$this->transformDetails($pagination->getItems(), $em),
            'candidateAddress' => ($profileDetails instanceof ProfileDetails) ? $profileDetails->getHomeAddress() : null,
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
     * @Rest\Get("/decline")
     * @SWG\Get(path="/api/candidate/application/decline",
     *   tags={"Candidate Application"},
     *   security={true},
     *   summary="Get declined application",
     *   description="The method for Getting declined application for candidate",
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
     *      name="startDate",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="2018-09-09",
     *      description="startDate"
     *   ),
     *   @SWG\Parameter(
     *      name="endDate",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="2018-09-09",
     *      description="endDate"
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
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  ),
     *                  @SWG\Property(
     *                      property="clientID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="industry",
     *                      type="array",
     *                      @SWG\Items(type="string")
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="location",
     *                      type="string",
     *                      example="location",
     *                  ),
     *                  @SWG\Property(
     *                      property="addressCity",
     *                      type="string",
     *                      example="addressCity",
     *                  ),
     *              )
     *          ),
     *          @SWG\Property(
     *              property="candidateAddress",
     *              type="string",
     *              example="candidateAddress"
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
     *      ),
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
    public function getDeclinedJobPostAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $declinedApplicants = $em->getRepository("AppBundle:Applicants")->getApplicantsByCandidateWithStatus($user->getId(),4,$request->query->all());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $declinedApplicants,
            ($request->query->getInt('page', 1) > 0) ? $request->query->getInt('page', 1) : 1,
            ($request->query->getInt('limit', 20) > 0) ? $request->query->getInt('limit', 20) : 20
        );
        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);
        $view = $this->view([
            'items'=>$this->transformDetails($pagination->getItems(), $em),
            'candidateAddress' => ($profileDetails instanceof ProfileDetails) ? $profileDetails->getHomeAddress() : null,
            'pagination' => [
                'current_page_number' => $pagination->getCurrentPageNumber(),
                'total_count' => $pagination->getTotalItemCount(),
            ]
        ], Response::HTTP_OK);
        return $this->handleView($view);
    }

    //---------OLD-------------
    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     *
     * @Rest\Post("/apply")
     * @SWG\Post(path="/api/candidate/application/apply",
     *   tags={"Candidate Application"},
     *   security={true},
     *   summary="Create application OLD",
     *   description="The method for Creating application for candidate",
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
     *              property="jobID",
     *              type="integer",
     *              example=1,
     *              description="required"
     *          ),
     *      ),
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success.",
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
    public function applicationJobPostAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($request->request->has('jobID') && !empty($request->request->get('jobID'))){
            $job = $em->getRepository("AppBundle:Job")->find($request->request->get('jobID'));
            if($job instanceof Job){
                $client = $job->getUser();
                $applicants = $em->getRepository("AppBundle:Applicants")->findOneBy(['client'=>$client, 'candidate'=>$user,'job'=>$job]);
                if($applicants instanceof Applicants){
                    if($applicants->getCheck() == true){
                        $view = $this->view(['error'=>'You already application on this job post'], Response::HTTP_BAD_REQUEST);
                        return $this->handleView($view);
                    }
                    else{
                        if($applicants->getStatus() == 2){
                            $applicants->setStatus(1);
                            $applicants->setCheck(true);
                        }
                        else{
                            $view = $this->view(['error'=>'You already application on this job post'], Response::HTTP_BAD_REQUEST);
                            return $this->handleView($view);
                        }
                    }
                }
                else{
                    $applicants = new Applicants($client, $user,1,true,$job);
                }

                $checkOpportunities = $em->getRepository("AppBundle:Opportunities")->findOneBy(['client'=>$client, 'candidate'=>$user,'job'=>$job]);
                if($checkOpportunities instanceof Opportunities ){
                    if($checkOpportunities->getStatus() == 1){
                        $applicants->setStatus(3);
                        $interviews = new Interviews($client, $user, 1, $job);
                        $em->persist($interviews);
                    }
                    $em->remove($checkOpportunities);
                }

                $em->persist($applicants);
                $em->flush();
                if(!$checkOpportunities instanceof Opportunities ){
                    $emailData = array(
                        'client' => ['firstName'=>$client->getFirstName(),'lastName'=>$client->getLastName()],
                        'jobTitle' => $job->getJobTitle(),
                        'link' => $request->getSchemeAndHttpHost()
                    );
                    $message = (new \Swift_Message('Candidates have applied for your Job Post'))
                        ->setFrom($this->container->getParameter('mailer_user'), 'Yes2Work')
                        ->setTo($client->getEmail())
                        ->setBody(
                            $this->renderView('emails/client/candidate_application.html.twig',
                                $emailData
                            ),
                            'text/html'
                        );

                    SendEmail::sendEmailForClient($client, $em, $message, $this->get('mailer'), $emailData, SendEmail::TYPE_CLIENT_CANDIDATE_APPLICANT);
                }
                else{
                    $emailData = array(
                        'client' => ['firstName'=>$applicants->getClient()->getFirstName(),'lastName'=>$applicants->getClient()->getLastName()],
                        'candidate' => ['firstName'=>$applicants->getCandidate()->getFirstName(),'lastName'=>$applicants->getCandidate()->getLastName()]
                    );
                    $message = (new \Swift_Message('Client wants to set up an interview'))
                        ->setFrom($this->container->getParameter('mailer_user'), 'Yes2Work')
                        ->setBody(
                            $this->renderView('emails/admin/set_up_interview.html.twig',
                                $emailData
                            ),
                            'text/html'
                        );
                    SendEmail::sendEmailForAdmin($em, $message, $this->get('mailer'), $emailData, SendEmail::TYPE_ADMIN_INTERVIEW_SET_UP);
                }

                $view = $this->view([], Response::HTTP_NO_CONTENT);
            }
            else{
                $view = $this->view(['error'=>'Job Not Found'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'jobID is required'], Response::HTTP_BAD_REQUEST);
        }
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Post("/cancel")
     * @SWG\Post(path="/api/candidate/application/cancel",
     *   tags={"Candidate Application"},
     *   security={true},
     *   summary="Cancel application OLD",
     *   description="The method for Getting Cancel application for candidate",
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
     *              property="clientID",
     *              type="integer",
     *              example=1,
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="jobID",
     *              type="integer",
     *              example=1,
     *              description="required"
     *          ),
     *      ),
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success.",
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
    public function cancelAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($request->request->has('clientID') && !empty($request->request->get('clientID')) && $request->request->has('jobID')){
            $client = $em->getRepository("AppBundle:User")->find($request->request->get('clientID'));
            if($client instanceof User){
                if(!empty($request->request->get('jobID'))){
                    $job = $em->getRepository('AppBundle:Job')->find($request->request->get('jobID'));
                    if(!$job instanceof Job){
                        $view = $this->view(['error'=>'Job Not Found'], Response::HTTP_NOT_FOUND);
                        return $this->handleView($view);
                    }
                }
                else{
                    $job = NULL;
                }
                $applicants = $em->getRepository("AppBundle:Applicants")->findOneBy(['client'=>$client, 'candidate'=>$user,'job'=>$job]);
                if($applicants instanceof Applicants){
                    if($applicants->getStatus() == 1){
                        $em->remove($applicants);
                        $em->flush();
                        $view = $this->view([], Response::HTTP_NO_CONTENT);
                    }
                    else{
                        $view = $this->view(['error'=>'You cannot cancel application'], Response::HTTP_BAD_REQUEST);
                    }
                }
                else{
                    $view = $this->view(['error'=>'Applicants Not Found'], Response::HTTP_NOT_FOUND);
                }
            }
            else{
                $view = $this->view(['error'=>'Client Not Found'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'clientID and jobID is required'], Response::HTTP_BAD_REQUEST);
        }
        return $this->handleView($view);
    }


    /**
     * @param array $applicants
     * @param EntityManager $em
     * @return array
     */
    private function transformDetails(array $applicants, EntityManager $em){
        $result = [];
        if(!empty($applicants)){
            foreach ($applicants as $applicant){
                $temp = [
                    'created' => $applicant->getCreated(),
                    'clientID' => $applicant->getClient()->getId()
                ];
                $job = $applicant->getJob();
                if($job instanceof Job){
                    $temp['jobID'] = $job->getId();
                    $temp['companyName'] = $job->getCompanyName();
                    $temp['industry'] = $job->getIndustry();
                    $temp['jobTitle'] = $job->getJobTitle();
                    $temp['location'] = $job->getCompanyAddress();
                    $temp['addressCity'] = $job->getAddressCity();
                }
                else{
                    $companyDetails = $em->getRepository('AppBundle:CompanyDetails')->findOneBy(['user'=>$applicant->getClient()]);
                    if($companyDetails instanceof CompanyDetails){
                        $temp['jobID'] = NULL;
                        $temp['companyName'] = $companyDetails->getName();
                        $temp['industry'] = $companyDetails->getIndustry();
                        $temp['jobTitle'] = $applicant->getClient()->getJobTitle();
                        $temp['location'] = $companyDetails->getAddress();
                        $temp['addressCity'] = $companyDetails->getAddressCity();
                    }
                }
                $result[] = $temp;
            }
        }
        return $result;
    }
}