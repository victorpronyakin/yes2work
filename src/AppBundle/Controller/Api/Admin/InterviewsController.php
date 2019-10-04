<?php
/**
 * Created by PhpStorm.
 * Date: 07.05.18
 * Time: 12:11
 */

namespace AppBundle\Controller\Api\Admin;

use AppBundle\Entity\Applicants;
use AppBundle\Entity\CompanyDetails;
use AppBundle\Entity\Interviews;
use AppBundle\Entity\Job;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class InterviewsController
 * @package AppBundle\Controller\Api\Admin
 *
 * @Rest\Route("interviews")
 * @Security("has_role('ROLE_ADMIN')")
 */
class InterviewsController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/")
     * @SWG\Get(path="/api/admin/interviews/",
     *   tags={"Admin Interviews"},
     *   security={true},
     *   summary="Get Interviews",
     *   description="The method for Getting Interviews for admin",
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
     *      name="search",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="search all applicants"
     *   ),
     *   @SWG\Parameter(
     *      name="orderBy",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="Candidate, Company, Position"
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
     *      name="csv",
     *      in="query",
     *      required=false,
     *      type="boolean",
     *      default=false,
     *      description="use for export csv, for use csv = true, response only what in items"
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
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateFirstName",
     *                      type="string",
     *                      example="candidateFirstName",
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateLastName",
     *                      type="string",
     *                      example="candidateLastName",
     *                  ),
     *                  @SWG\Property(
     *                      property="clientID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="companyName",
     *                      type="string",
     *                      example="companyName",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                      example="jobID",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  ),
     *                  @SWG\Property(
     *                      property="status",
     *                      type="integer",
     *                      example="1",
     *                      description="1 = Interview to be set up, 2 = Interview pending, 3 = Successfully placed, 4 = Applicants awaiting approval, 5 = Shortlisted for consideration"
     *                  ),
     *              )
     *          ),
     *          @SWG\Property(
     *              type="object",
     *              property="pagination",
     *              @SWG\Property(
     *                  type="integer",
     *                  property="current_page_number",
     *              ),
     *              @SWG\Property(
     *                  type="integer",
     *                  property="total_count",
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
    public function getInterviewsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $params = $request->query->all();
        $interviews = $em->getRepository("AppBundle:Interviews")->getAllInterviews([1,2,3], $params);
        $applicants = $em->getRepository("AppBundle:Applicants")->getAllApplicants([1,2], $params);
        $interviews = array_merge($interviews, $applicants);
        $interviews = $this->transformDetailsAll($interviews, $em);
        $interviews = $this->sortApplicants($interviews, ['Candidate', 'Company', 'Position'], $params);

        if($request->query->getBoolean('csv', false) == false){
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $interviews,
                $request->query->getInt('page', 1),
                $request->query->getInt('limit', 20)
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
            if(!empty($interviews)){
                foreach ($interviews as $interview){

                    $candidate = '';
                    if(isset($interview['candidateFirstName'])){
                        $candidate .= $interview['candidateFirstName'].' ';
                    }
                    if(isset($interview['candidateLastName'])){
                        $candidate .= $interview['candidateLastName'];
                    }
                    $status = '';
                    if(isset($interview['status'])){
                        if($interview['status'] == 1){
                            $status = 'Interview to be set up';
                        }
                        elseif($interview['status'] == 2){
                            $status = 'Interview pending';
                        }
                        elseif($interview['status'] == 3){
                            $status = 'Successfully placed';
                        }
                        elseif($interview['status'] == 4){
                            $status = 'Applicants awaiting approval';
                        }
                        elseif($interview['status'] == 5){
                            $status = 'Shortlisted for consideration';
                        }
                    }
                    $result[] = [
                        'candidate' => $candidate,
                        'company' => (isset($interview['companyName'])) ? $interview['companyName'] : '',
                        'jobTitle' => (isset($interview['jobTitle'])) ? $interview['jobTitle'] : '',
                        'status' => $status
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
     * @Rest\Get("/awaiting")
     * @SWG\Get(path="/api/admin/interviews/awaiting",
     *   tags={"Admin Interviews"},
     *   security={true},
     *   summary="Get Awaiting approval applicants",
     *   description="The method for Get Awaiting approval applicants for admin",
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
     *      description="search awaiting applicants"
     *   ),
     *   @SWG\Parameter(
     *      name="orderBy",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="Candidate, Company, Position, Days"
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
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateFirstName",
     *                      type="string",
     *                      example="candidateFirstName",
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateLastName",
     *                      type="string",
     *                      example="candidateLastName",
     *                  ),
     *                  @SWG\Property(
     *                      property="clientID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="companyName",
     *                      type="string",
     *                      example="companyName",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                      example="jobID",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  )
     *              )
     *          ),
     *          @SWG\Property(
     *              type="object",
     *              property="pagination",
     *              @SWG\Property(
     *                  type="integer",
     *                  property="current_page_number",
     *              ),
     *              @SWG\Property(
     *                  type="integer",
     *                  property="total_count",
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
    public function getAwaitingApplicantsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $params = $request->query->all();
        $awaitingApplicants = $em->getRepository("AppBundle:Applicants")->getAllApplicants(1, $params);
        $awaitingApplicants = $this->transformDetailsApplicants($awaitingApplicants, $em);
        $awaitingApplicants = $this->sortApplicants($awaitingApplicants, ['Candidate', 'Company', 'Position', 'Days'], $params);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $awaitingApplicants,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
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
     * @Rest\Get("/shortlist")
     * @SWG\Get(path="/api/admin/interviews/shortlist",
     *   tags={"Admin Interviews"},
     *   security={true},
     *   summary="Get Shortlist applicants",
     *   description="The method for Get Shortlist applicants for admin",
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
     *      description="search shortlist applicants"
     *   ),
     *   @SWG\Parameter(
     *      name="orderBy",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="Candidate, Company, Position, Days"
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
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateFirstName",
     *                      type="string",
     *                      example="candidateFirstName",
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateLastName",
     *                      type="string",
     *                      example="candidateLastName",
     *                  ),
     *                  @SWG\Property(
     *                      property="clientID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="companyName",
     *                      type="string",
     *                      example="companyName",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                      example="jobID",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  )
     *              )
     *          ),
     *          @SWG\Property(
     *              type="object",
     *              property="pagination",
     *              @SWG\Property(
     *                  type="integer",
     *                  property="current_page_number",
     *              ),
     *              @SWG\Property(
     *                  type="integer",
     *                  property="total_count",
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
    public function getShortlistApplicantsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $params = $request->query->all();
        $shortListApplicants = $em->getRepository("AppBundle:Applicants")->getAllApplicants(2, $params);
        $shortListApplicants = $this->transformDetailsApplicants($shortListApplicants, $em);
        $shortListApplicants = $this->sortApplicants($shortListApplicants, ['Candidate', 'Company', 'Position', 'Days'], $params);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $shortListApplicants,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
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
     * @Rest\Get("/setUp/candidate")
     * @SWG\Get(path="/api/admin/interviews/setUp/candidate",
     *   tags={"Admin Interviews"},
     *   security={true},
     *   summary="Get Set Up Interviews Candidate",
     *   description="The method for Getting Set Up Interviews Candidate for admin",
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
     *      description="search setup interviews"
     *   ),
     *   @SWG\Parameter(
     *      name="orderBy",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="Candidate, Company, Position, Days"
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
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateFirstName",
     *                      type="string",
     *                      example="candidateFirstName",
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateLastName",
     *                      type="string",
     *                      example="candidateLastName",
     *                  ),
     *                  @SWG\Property(
     *                      property="clientID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="companyName",
     *                      type="string",
     *                      example="companyName",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                      example="jobID",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  )
     *              )
     *          ),
     *          @SWG\Property(
     *              type="object",
     *              property="pagination",
     *              @SWG\Property(
     *                  type="integer",
     *                  property="current_page_number",
     *              ),
     *              @SWG\Property(
     *                  type="integer",
     *                  property="total_count",
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
    public function getSetUpInterviewsCandidateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $params = $request->query->all();
        $setUpInterviews = $em->getRepository("AppBundle:Interviews")->getInterviews(1, true, $params);
        $setUpInterviews = $this->transformDetails($setUpInterviews, $em);
        $setUpInterviews = $this->sortApplicants($setUpInterviews, ['Candidate', 'Company', 'Position', 'Days'], $params);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $setUpInterviews,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
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
     * @Rest\Get("/setUp/client")
     * @SWG\Get(path="/api/admin/interviews/setUp/client",
     *   tags={"Admin Interviews"},
     *   security={true},
     *   summary="Get Set Up Interviews Client",
     *   description="The method for Getting Set Up Interviews Client for admin",
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
     *      description="search setup interviews"
     *   ),
     *   @SWG\Parameter(
     *      name="orderBy",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="Candidate, Company, Position, Days"
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
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateFirstName",
     *                      type="string",
     *                      example="candidateFirstName",
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateLastName",
     *                      type="string",
     *                      example="candidateLastName",
     *                  ),
     *                  @SWG\Property(
     *                      property="clientID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="companyName",
     *                      type="string",
     *                      example="companyName",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                      example="jobID",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  )
     *              )
     *          ),
     *          @SWG\Property(
     *              type="object",
     *              property="pagination",
     *              @SWG\Property(
     *                  type="integer",
     *                  property="current_page_number",
     *              ),
     *              @SWG\Property(
     *                  type="integer",
     *                  property="total_count",
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
    public function getSetUpInterviewsClientAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $params = $request->query->all();
        $setUpInterviews = $em->getRepository("AppBundle:Interviews")->getInterviews(1, false, $params);
        $setUpInterviews = $this->transformDetails($setUpInterviews, $em);
        $setUpInterviews = $this->sortApplicants($setUpInterviews, ['Candidate', 'Company', 'Position', 'Days'], $params);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $setUpInterviews,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
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
     * @Rest\Patch("/{id}/setUp", requirements={"id"="\d+"})
     * @SWG\Patch(path="/api/admin/interviews/{id}/setUp",
     *   tags={"Admin Interviews"},
     *   security={true},
     *   summary="Set Up Interviews",
     *   description="The method for setting Up Interview for admin",
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
     *      default=1,
     *      description="id"
     *   ),
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="setUp",
     *              type="boolean",
     *              example=true,
     *              description="required. Only true"
     *          )
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
    public function setUpInterviewAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        $interview = $em->getRepository("AppBundle:Interviews")->find($id);
        if($interview instanceof Interviews){
            if($request->request->has('setUp') && $request->request->get('setUp') == true){
                $interview->setStatus(2);
                //$interview->setCreated(new \DateTime());
                $em->persist($interview);
                $em->flush();
                //$logging = new Logging($this->getUser(),17, "between the client ".$interview->getClient()->getFirstName()." ".$interview->getClient()->getLastName()." and the candidate ".$interview->getCandidate()->getFirstName()." ".$interview->getCandidate()->getLastName(), $interview->getId());
                //$em->persist($logging);
                //$em->flush();
                $view = $this->view([], Response::HTTP_NO_CONTENT);
            }
            else{
                $view = $this->view(['error'=>'setUp field is required and should be = true'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'Interview Not Found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/pending")
     * @SWG\Get(path="/api/admin/interviews/pending",
     *   tags={"Admin Interviews"},
     *   security={true},
     *   summary="Get Pending Interviews",
     *   description="The method for Getting Pending Interviews for admin",
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
     *      description="search pending interviews"
     *   ),
     *   @SWG\Parameter(
     *      name="orderBy",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="Candidate, Company, Position, Days"
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
     *          @SWG\Property(
     *              property="items",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateFirstName",
     *                      type="string",
     *                      example="candidateFirstName",
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateLastName",
     *                      type="string",
     *                      example="candidateLastName",
     *                  ),
     *                  @SWG\Property(
     *                      property="clientID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="companyName",
     *                      type="string",
     *                      example="companyName",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                      example="jobID",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  )
     *              )
     *          ),
     *          @SWG\Property(
     *              type="object",
     *              property="pagination",
     *              @SWG\Property(
     *                  type="integer",
     *                  property="current_page_number",
     *              ),
     *              @SWG\Property(
     *                  type="integer",
     *                  property="total_count",
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
    public function getPendingInterviewAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $params = $request->query->all();
        $pendingInterviews = $em->getRepository("AppBundle:Interviews")->getInterviews(2, null, $params);
        $pendingInterviews = $this->transformDetails($pendingInterviews, $em);
        $pendingInterviews = $this->sortApplicants($pendingInterviews, ['Candidate', 'Company', 'Position', 'Days'], $params);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $pendingInterviews,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
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
     * @Rest\Patch("/{id}/placed", requirements={"id"="\d+"})
     * @SWG\Patch(path="/api/admin/interviews/{id}/placed",
     *   tags={"Admin Interviews"},
     *   security={true},
     *   summary="Placed Interviews",
     *   description="The method for setting Placed Interview for admin",
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
     *      default=1,
     *      description="id"
     *   ),
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="placed",
     *              type="boolean",
     *              example=true,
     *              description="required."
     *          )
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
    public function placedInterviewAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        $interview = $em->getRepository("AppBundle:Interviews")->find($id);
        if($interview instanceof Interviews){
            if($request->request->has('placed') && is_bool($request->request->get('placed'))){
                if($request->request->get('placed') == true){
                    $interview->setStatus(3);
                }
                else{
                    $interview->setStatus(4);
                }
                //$interview->setCreated(new \DateTime());
                $em->persist($interview);
                $em->flush();

                $applicant = $em->getRepository("AppBundle:Applicants")->findOneBy(['candidate'=>$interview->getCandidate(),'client'=>$interview->getClient(),'job'=>$interview->getJob(), 'status'=>3]);
                if($applicant instanceof Applicants){
                    $em->remove($applicant);
                    $em->flush();
                }

                $view = $this->view([], Response::HTTP_NO_CONTENT);
            }
            else{
                $view = $this->view(['error'=>'placed field is required and should be boolean type'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'Interview Not Found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/placed")
     * @SWG\Get(path="/api/admin/interviews/placed",
     *   tags={"Admin Interviews"},
     *   security={true},
     *   summary="Get Successful Placed Interviews",
     *   description="The method for Getting Successful Placed Interviews for admin",
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
     *      description="search placed interviews"
     *   ),
     *   @SWG\Parameter(
     *      name="orderBy",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="Candidate, Company, Position, Days"
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
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateFirstName",
     *                      type="string",
     *                      example="candidateFirstName",
     *                  ),
     *                  @SWG\Property(
     *                      property="candidateLastName",
     *                      type="string",
     *                      example="candidateLastName",
     *                  ),
     *                  @SWG\Property(
     *                      property="clientID",
     *                      type="integer",
     *                      example=1,
     *                  ),
     *                  @SWG\Property(
     *                      property="companyName",
     *                      type="string",
     *                      example="companyName",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  )
     *              )
     *          ),
     *          @SWG\Property(
     *              type="object",
     *              property="pagination",
     *              @SWG\Property(
     *                  type="integer",
     *                  property="current_page_number",
     *              ),
     *              @SWG\Property(
     *                  type="integer",
     *                  property="total_count",
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
    public function getPlacedInterviewAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $params = $request->query->all();
        $placedInterviews = $em->getRepository("AppBundle:Interviews")->getInterviews(3, null, $params);
        $placedInterviews = $this->transformDetails($placedInterviews, $em);
        $placedInterviews = $this->sortApplicants($placedInterviews, ['Candidate', 'Company', 'Position', 'Days'], $params);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $placedInterviews,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
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
     * @param array $interviews
     * @param EntityManager $em
     * @return array
     */
    private function transformDetailsAll(array $interviews, EntityManager $em){
        $result = [];
        if(!empty($interviews)){
            foreach ($interviews as $interview){
                if($interview instanceof Interviews || $interview instanceof Applicants){
                    $temp = [
                        'id' => $interview->getId(),
                        'candidateID' => $interview->getCandidate()->getId(),
                        'candidateFirstName' => $interview->getCandidate()->getFirstName(),
                        'candidateLastName' => $interview->getCandidate()->getLastName(),
                        'clientID' => $interview->getClient()->getId(),
                    ];
                    $job = $interview->getJob();
                    if($job instanceof Job){
                        $temp['companyName'] = $job->getCompanyName();
                        $temp['jobTitle'] = $job->getJobTitle();
                        $temp['jobID'] = $job->getId();
                    }
                    else{
                        $companyDetails = $em->getRepository('AppBundle:CompanyDetails')->findOneBy(['user'=>$interview->getClient()]);
                        if($companyDetails instanceof CompanyDetails){
                            $temp['companyName'] = $companyDetails->getName();
                            $temp['jobTitle'] = $interview->getClient()->getJobTitle();
                            $temp['jobID'] = null;
                        }
                    }
                    $temp['created'] = $interview->getCreated();
                    if($interview instanceof Interviews){
                        $temp['status'] = $interview->getStatus();
                    }
                    else{
                        if($interview->getStatus() == 1){
                            $temp['status'] = 4;
                        }
                        else{
                            $temp['status'] = 5;
                        }
                    }
                    $result[] = $temp;
                }
            }
        }
        return $result;
    }

    /**
     * @param array $interviews
     * @param EntityManager $em
     * @return array
     */
    private function transformDetails(array $interviews, EntityManager $em){
        $result = [];
        if(!empty($interviews)){
            foreach ($interviews as $interview){
                if($interview instanceof Interviews){
                    if($interview->getStatus() != 4){
                        $temp = [
                            'id' => $interview->getId(),
                            'candidateID' => $interview->getCandidate()->getId(),
                            'candidateFirstName' => $interview->getCandidate()->getFirstName(),
                            'candidateLastName' => $interview->getCandidate()->getLastName(),
                            'clientID' => $interview->getClient()->getId(),
                        ];
                        $job = $interview->getJob();
                        if($job instanceof Job){
                            $temp['companyName'] = $job->getCompanyName();
                            $temp['jobTitle'] = $job->getJobTitle();
                            $temp['jobID'] = $job->getId();
                        }
                        else{
                            $companyDetails = $em->getRepository('AppBundle:CompanyDetails')->findOneBy(['user'=>$interview->getClient()]);
                            if($companyDetails instanceof CompanyDetails){
                                $temp['companyName'] = $companyDetails->getName();
                                $temp['jobTitle'] = $interview->getClient()->getJobTitle();
                                $temp['jobID'] = null;
                            }
                        }
                        $temp['created'] = $interview->getCreated();
                        $result[] = $temp;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param array $applicants
     * @param EntityManager $em
     * @return array
     */
    private function transformDetailsApplicants(array $applicants, EntityManager $em){
        $result = [];
        if(!empty($applicants)){
            foreach ($applicants as $applicant){
                if($applicant instanceof Applicants){
                    if(in_array($applicant->getStatus(),[1,2])){
                        $temp = [
                            'id' => $applicant->getId(),
                            'candidateID' => $applicant->getCandidate()->getId(),
                            'candidateFirstName' => $applicant->getCandidate()->getFirstName(),
                            'candidateLastName' => $applicant->getCandidate()->getLastName(),
                            'clientID' => $applicant->getClient()->getId(),
                        ];
                        $job = $applicant->getJob();
                        if($job instanceof Job){
                            $temp['companyName'] = $job->getCompanyName();
                            $temp['jobTitle'] = $job->getJobTitle();
                            $temp['jobID'] = $job->getId();
                        }
                        else{
                            $companyDetails = $em->getRepository('AppBundle:CompanyDetails')->findOneBy(['user'=>$applicant->getClient()]);
                            if($companyDetails instanceof CompanyDetails){
                                $temp['companyName'] = $companyDetails->getName();
                                $temp['jobTitle'] = $applicant->getClient()->getJobTitle();
                                $temp['jobID'] = null;
                            }
                        }
                        $temp['created'] = $applicant->getCreated();

                        $result[] = $temp;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param $items
     * @param $sortFields
     * @param array $params
     * @return mixed
     */
    private function sortApplicants($items, $sortFields, $params = array()){
        if(!empty($items)){
            if(isset($params['orderBy']) && !empty($params['orderBy']) && in_array($params['orderBy'], $sortFields)){
                if(isset($params['orderSort']) && !empty($params['orderSort']) && in_array($params['orderSort'], ['asc', 'desc'])){
                    if($params['orderBy'] == 'Candidate'){
                        if($params['orderSort'] == 'asc'){
                            usort($items, function($a, $b) {
                                return $a['candidateFirstName'] > $b['candidateFirstName'];
                            });
                        }
                        else{
                            usort($items, function($a, $b) {
                                return $a['candidateFirstName'] < $b['candidateFirstName'];
                            });
                        }
                    }
                    elseif ($params['orderBy'] == 'Company'){
                        if($params['orderSort'] == 'asc'){
                            usort($items, function($a, $b) {
                                return $a['companyName'] > $b['companyName'];
                            });
                        }
                        else{
                            usort($items, function($a, $b) {
                                return $a['companyName'] < $b['companyName'];
                            });
                        }
                    }
                    elseif ($params['orderBy'] == 'Position'){
                        if($params['orderSort'] == 'asc'){
                            usort($items, function($a, $b) {
                                return $a['jobTitle'] > $b['jobTitle'];
                            });
                        }
                        else{
                            usort($items, function($a, $b) {
                                return $a['jobTitle'] < $b['jobTitle'];
                            });
                        }
                    }
                    elseif ($params['orderBy'] == 'Days'){
                        if($params['orderSort'] == 'asc'){
                            usort($items, function($a, $b) {
                                return $a['created'] > $b['created'];
                            });
                        }
                        else{
                            usort($items, function($a, $b) {
                                return $a['created'] < $b['created'];
                            });
                        }
                    }
                }
            }
        }

        return $items;
    }
}
