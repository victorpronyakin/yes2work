<?php
/**
 * Created by PhpStorm.
 * Date: 31.08.18
 * Time: 16:39
 */

namespace AppBundle\Controller\Api\Candidate;

use AppBundle\Entity\CompanyDetails;
use AppBundle\Entity\Interviews;
use AppBundle\Entity\Job;
use AppBundle\Entity\ProfileDetails;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class InterviewsController
 * @package AppBundle\Controller\Api\Candidate
 *
 * @Rest\Route("interviews")
 * @Security("has_role('ROLE_CANDIDATE')")
 */
class InterviewsController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/")
     * @SWG\Get(path="/api/candidate/interviews/",
     *   tags={"Candidate Interviews"},
     *   security={true},
     *   summary="Get Interviews Request",
     *   description="The method for getting Interviews Request for candidate",
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
     *      name="status",
     *      in="query",
     *      required=false,
     *      type="integer",
     *      default=0,
     *      description="sort by status, 1 = Pending, 2 = Set up"
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
     *                      property="interviewID",
     *                      type="integer",
     *                      example=0
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                      example=0
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle"
     *                  ),
     *                  @SWG\Property(
     *                      property="industry",
     *                      type="array",
     *                      @SWG\Items(type="string")
     *                  ),
     *                  @SWG\Property(
     *                      property="roleDescription",
     *                      type="string",
     *                      example="roleDescription"
     *                  ),
     *                  @SWG\Property(
     *                      property="companyAddress",
     *                      type="string",
     *                      example="companyAddress"
     *                  ),
     *                  @SWG\Property(
     *                      property="addressCity",
     *                      type="string",
     *                      example="addressCity"
     *                  ),
     *                  @SWG\Property(
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-12",
     *                  ),
     *                  @SWG\Property(
     *                      property="status",
     *                      type="integer",
     *                      example=1,
     *                      description="1 = Pending, 2 = Set Up"
     *                  )
     *              )
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="candidateAddress",
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
    public function getAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
        if($profileDetails instanceof ProfileDetails){
            $resultInterviews = $em->getRepository("AppBundle:Interviews")->getInterviewsRequestForCandidate($user->getId(),$request->query->all());
            $interviews = [];

            if(!empty($resultInterviews)){
                foreach ($resultInterviews as $interview){
                    if($interview instanceof Interviews){
                        if($interview->getJob() instanceof Job){
                            $item = [
                                "jobId" => $interview->getJob()->getId(),
                                "jobTitle" => $interview->getJob()->getJobTitle(),
                                "industry" => $interview->getJob()->getIndustry(),
                                "roleDescription" => $interview->getJob()->getRoleDescriptionChange(),
                                "companyAddress" => $interview->getJob()->getCompanyAddress(),
                                "addressCity" => $interview->getJob()->getAddressCity(),
                            ];
                        }
                        else{
                            $companyDetails = $em->getRepository("AppBundle:CompanyDetails")->findOneBy(['user'=>$interview->getClient()]);
                            if($companyDetails instanceof CompanyDetails){
                                $item = [
                                    "jobId" => null,
                                    "jobTitle" => $interview->getClient()->getJobTitle(),
                                    "industry" => $companyDetails->getIndustry(),
                                    "roleDescription" => null,
                                    "companyAddress" => $companyDetails->getAddress(),
                                    "addressCity" => $companyDetails->getAddressCity(),
                                ];
                            }
                        }
                        if(isset($item)){
                            $item["interviewID"] = $interview->getId();
                            $item["created"] = $interview->getCreated();
                            $item["status"] = $interview->getStatus();
                            $interviews[] = $item;
                        }
                    }
                }
            }

            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $interviews,
                ($request->query->getInt('page', 1) > 0) ? $request->query->getInt('page', 1) : 1,
                ($request->query->getInt('limit', 20) > 0) ? $request->query->getInt('limit', 20) : 20
            );
            $view = $this->view([
                'items'=>$pagination->getItems(),
                'candidateAddress' => $profileDetails->getHomeAddress(),
                'pagination' => [
                    'current_page_number' => $pagination->getCurrentPageNumber(),
                    'total_count' => $pagination->getTotalItemCount(),
                ]
            ], Response::HTTP_OK);

        }
        else{
            $view = $this->view(['error'=>'Profile not Found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $interviewID
     * @return Response
     *
     * @Rest\Get("/{interviewID}", requirements={"interviewID"="\d+"})
     * @SWG\Get(path="/api/candidate/interviews/{interviewID}",
     *   tags={"Candidate Interviews"},
     *   security={true},
     *   summary="Get Interview Request by ID",
     *   description="The method for getting Interview Request by id for candidate",
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
     *          type="object",
     *          @SWG\Property(
     *              property="interviewID",
     *              type="integer",
     *              example=0
     *          ),
     *          @SWG\Property(
     *              property="jobID",
     *              type="integer",
     *              example=0
     *          ),
     *          @SWG\Property(
     *              property="jobTitle",
     *              type="string",
     *              example="jobTitle"
     *          ),
     *          @SWG\Property(
     *              property="industry",
     *              type="array",
     *              @SWG\Items(type="string")
     *          ),
     *          @SWG\Property(
     *              property="companyAddress",
     *              type="string",
     *              example="companyAddress"
     *          ),
     *          @SWG\Property(
     *              property="addressCity",
     *              type="string",
     *              example="addressCity"
     *          ),
     *          @SWG\Property(
     *              property="companyDescription",
     *              type="string",
     *              example="companyDescription"
     *          ),
     *          @SWG\Property(
     *              property="roleDescription",
     *              type="string",
     *              example="roleDescription"
     *          ),
     *          @SWG\Property(
     *              property="createdDate",
     *              type="datetime",
     *              example="2018-09-12",
     *          ),
     *          @SWG\Property(
     *              property="endDate",
     *              type="datetime",
     *              example="2018-09-12",
     *          ),
     *          @SWG\Property(
     *              property="startedDate",
     *              type="datetime",
     *              example="2018-09-12",
     *          ),
     *          @SWG\Property(
     *              property="candidateAddress",
     *              type="string",
     *              example="candidateAddress",
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
    public function getByIdAction(Request $request, $interviewID){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
        if($profileDetails instanceof ProfileDetails){
            $interview = $em->getRepository("AppBundle:Interviews")->find($interviewID);
            if($interview instanceof Interviews){
                if($interview->getJob() instanceof Job){
                    $item = [
                        "jobId" => $interview->getJob()->getId(),
                        "jobTitle" => $interview->getJob()->getJobTitle(),
                        "industry" => $interview->getJob()->getIndustry(),
                        "companyAddress" => $interview->getJob()->getCompanyAddress(),
                        "addressCity" => $interview->getJob()->getAddressCity(),
                        'companyDescription' => $interview->getJob()->getCompanyDescriptionChange(),
                        "roleDescription" => $interview->getJob()->getRoleDescriptionChange(),
                        "createdDate" => $interview->getJob()->getCreated(),
                        "endDate" => $interview->getJob()->getClosureDate(),
                        "startedDate" => $interview->getJob()->getStarted(),
                    ];
                }
                else{
                    $companyDetails = $em->getRepository("AppBundle:CompanyDetails")->findOneBy(['user'=>$interview->getClient()]);
                    if($companyDetails instanceof CompanyDetails){
                        $item = [
                            "jobId" => null,
                            "jobTitle" => $interview->getClient()->getJobTitle(),
                            "industry" => $companyDetails->getIndustry(),
                            "companyAddress" => $companyDetails->getAddress(),
                            "addressCity" => $companyDetails->getAddressCity(),
                            "companyDescription" => $companyDetails->getDescription(),
                            "roleDescription" => null,
                            "createdDate" => null,
                            "endDate" => null,
                            "startedDate" => null,
                        ];
                    }
                }

                $item["interviewID"] = $interview->getId();
                $item["candidateAddress"] = $profileDetails->getHomeAddress();

                $view = $this->view($item, Response::HTTP_OK);
            }
            else{
                $view = $this->view(['error'=>'Interview Request Not Found'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'Profile not Found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }
}