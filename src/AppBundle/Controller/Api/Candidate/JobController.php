<?php
/**
 * Created by PhpStorm.
 * Date: 07.05.18
 * Time: 15:26
 */

namespace AppBundle\Controller\Api\Candidate;

use AppBundle\Entity\Applicants;
use AppBundle\Entity\CompanyDetails;
use AppBundle\Entity\HideJob;
use AppBundle\Entity\Job;
use AppBundle\Entity\Opportunities;
use AppBundle\Entity\ProfileDetails;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class JobController
 * @package AppBundle\Controller\Api\Candidate
 *
 * @Rest\Route("job")
 * @Security("has_role('ROLE_CANDIDATE')")
 */
class JobController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/")
     * @SWG\Get(path="/api/candidate/job/",
     *   tags={"Candidate Job"},
     *   security={true},
     *   summary="Get Job",
     *   description="The method for getting job for candidate",
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
     *      description="search by jobTitle"
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
     *                      property="endDate",
     *                      type="datetime",
     *                      example="2018-09-12",
     *                  ),
     *                  @SWG\Property(
     *                      property="createdDate",
     *                      type="datetime",
     *                      example="2018-09-12",
     *                  ),
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
    public function getFindJob(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
        if($profileDetails instanceof ProfileDetails){
            if($profileDetails->getPercentage()>50 && isset($profileDetails->getVideo()['approved']) && $profileDetails->getVideo()['approved'] == true){
                $result = $em->getRepository("AppBundle:Job")->getJobsForCandidate($profileDetails,$request->query->all());
                $applicants = $em->getRepository("AppBundle:Applicants")->findBy(['candidate'=>$user]);
                $opportunities = $em->getRepository("AppBundle:Opportunities")->findBy(['candidate'=>$user]);
                if(!empty($applicants) || !empty($opportunities)){
                    $hideJobsIds = [];
                    if(!empty($applicants)){
                        foreach ($applicants as $applicant){
                            if($applicant instanceof Applicants){
                                if($applicant->getJob() instanceof Job){
                                    $hideJobsIds[] = $applicant->getJob()->getId();
                                }
                            }
                        }
                    }
                    if(!empty($opportunities)){
                        foreach ($opportunities as $opportunity){
                            if($opportunity instanceof Opportunities){
                                if($opportunity->getJob() instanceof Job){
                                    $hideJobsIds[] = $opportunity->getJob()->getId();
                                }
                            }
                        }
                    }

                    $jobs = [];
                    if(!empty($hideJobsIds)){
                        foreach ($result as $job){
                            if(isset($job['id']) && !in_array($job['id'], $hideJobsIds)){
                                $jobs[] = $job;
                            }
                        }
                    }
                    else{
                        $jobs = $result;
                    }

                }
                else{
                    $jobs = $result;
                }


                $paginator  = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $jobs,
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
                $view = $this->view(['error'=>'Complete profile >50% and video should be upload and approved'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'Profile not Found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     *
     * @Rest\Get("/{id}", requirements={"id"="\d+"})
     * @SWG\Get(path="/api/candidate/job/{id}",
     *   tags={"Candidate Job"},
     *   security={true},
     *   summary="Get Job Details By ID",
     *   description="The method for getting job details by ID for candidate",
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
     *              property="id",
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
     *              property="endDate",
     *              type="datetime",
     *              example="2018-09-12",
     *          ),
     *          @SWG\Property(
     *              property="createdDate",
     *              type="datetime",
     *              example="2018-09-12",
     *          ),
     *          @SWG\Property(
     *              property="startedDate",
     *              type="datetime",
     *              example="2018-09-12",
     *          ),
     *          @SWG\Property(
     *              property="clientID",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="status",
     *              type="integer",
     *              description="
     *                  0 = New (Only button 'Apply', NOT Message),
     *                  1 = job awaiting approve (Only button 'Accept', 'Decline', NOT Message)
     *                  2 = Application awaiting approve (Message and Button 'Cancel')
     *                  3 = Already interview set up (Only Message NOT Button)
     *                  4 = Application was declined (Only Message NOT Button)
     *                  5 = You declined job (Message and button 'Re-Accept')
     *                  6 = You have already applied (Only Message NOT Button)
     *                  7 = You missed job (Nothing show)
     *              "
     *          ),
     *          @SWG\Property(
     *              property="candidateAddress",
     *              type="string",
     *              example="candidateAddress"
     *          ),
     *          @SWG\Property(
     *              property="spec",
     *              type="object",
     *              @SWG\Property(
     *                  property="url",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="adminUrl",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="name",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="size",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="approved",
     *                  type="boolean"
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
     *      description="NOT FOUND",
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
    public function getJobDetailsByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        //$job = $em->getRepository("AppBundle:Job")->getJobByIdForCandidate($id);

        $job = $em->getRepository("AppBundle:Job")->find($id);
        if($job instanceof Job){
            $applicant = NULL;
            $applicantCheck = $em->getRepository("AppBundle:Applicants")->findOneBy(['client'=>$job->getUser(), 'job'=>$job, 'candidate'=>$this->getUser()]);
            if($applicantCheck instanceof Applicants){
                $applicant = $applicantCheck->getStatus();
            }

            $opportunityCheck = $em->getRepository("AppBundle:Opportunities")->findOneBy(['client'=>$job->getUser(), 'job'=>$job, 'candidate'=>$this->getUser()]);
            $opportunity = NULL;
            if($opportunityCheck instanceof Opportunities){
                $opportunity = $opportunityCheck->getStatus();
            }

            //New (Only button "Apply")
            $status = 0;
            if(!is_null($applicant) && !is_null($opportunity)){
                if($opportunity == 1){
                    //job awaiting approve (Only button "Accept", "Decline", NOT Message)
                    $status = 1;
                }
                elseif ($applicant == 1){
                    //Application awaiting approve (Message and Button "Cancel")
                    $status = 2;
                }
                elseif($applicant == 3){
                    //Already interview set up (Only Message NOT Button)
                    $status = 3;
                }
                elseif ($applicant == 4){
                    //Application was declined (Only Message NOT Button)
                    $status = 4;
                }
                elseif($opportunity == 2){
                    //You declined job (Message and button "Re-Accept")
                    $status = 5;
                }
                elseif($applicant == 2){
                    //You have already applied (Only Message NOT Button)
                    $status = 6;
                }
                elseif($opportunity == 3){
                    //You missed job (Nothing show)
                    $status = 7;
                }
            }
            elseif(!is_null($applicant) && is_null($opportunity)){
                if ($applicant == 1){
                    //Application awaiting approve (Message and Button "Cancel")
                    $status = 2;
                }
                elseif($applicant == 3){
                    //Already interview set up (Only Message NOT Button)
                    $status = 3;
                }
                elseif ($applicant == 4){
                    //Application was declined (Only Message NOT Button)
                    $status = 4;
                }
                elseif($applicant == 2){
                    //You have already applied (Only Message NOT Button)
                    $status = 6;
                }
            }
            elseif(is_null($applicant) && !is_null($opportunity)){
                if($opportunity == 1){
                    //job awaiting approve (Only button "Accept", "Decline", NOT Message)
                    $status = 1;
                }
                elseif($opportunity == 2){
                    //You declined job (Message and button "Re-Accept")
                    $status = 5;
                }
                elseif($opportunity == 3){
                    //You missed job (Nothing show)
                    $status = 7;
                }
            }

            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);

            $view = $this->view([
                'id' => $job->getId(),
                'jobTitle' => $job->getJobTitle(),
                'industry' => $job->getIndustry(),
                'companyAddress' => $job->getCompanyAddress(),
                'addressCity' => $job->getAddressCity(),
                'companyDescription' => (!empty($job->getCompanyDescriptionChange())) ? $job->getCompanyDescriptionChange() : $job->getCompanyDescription(),
                'roleDescription' => (!empty($job->getRoleDescriptionChange())) ? $job->getRoleDescriptionChange() : $job->getRoleDescription(),
                'createdDate' => $job->getCreated(),
                'endDate' => $job->getClosureDate(),
                'startedDate' => $job->getStarted(),
                'clientID' => $job->getUser()->getId(),
                'status' => $status,
                'candidateAddress' => ($profileDetails instanceof ProfileDetails) ? $profileDetails->getHomeAddress() : null,
                'spec' => $job->getSpec()

            ], Response::HTTP_OK);
        }
        else{
            $view = $this->view(['error'=>'Job Not Found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);


    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     *
     * @Rest\Get("/client/{id}", requirements={"id"="\d+"})
     * @SWG\Get(path="/api/candidate/job/client/{id}",
     *   tags={"Candidate Job"},
     *   security={true},
     *   summary="Get Job Client Details By ID",
     *   description="The method for getting job Client details by ID for candidate",
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
     *              property="id",
     *              type="integer",
     *              example=null
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
     *              property="endDate",
     *              type="datetime",
     *              example=null,
     *          ),
     *          @SWG\Property(
     *              property="createdDate",
     *              type="datetime",
     *              example=null,
     *          ),
     *          @SWG\Property(
     *              property="startedDate",
     *              type="datetime",
     *              example=null,
     *          ),
     *          @SWG\Property(
     *              property="clientID",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="candidateAddress",
     *              type="string",
     *          ),
     *          @SWG\Property(
     *              property="status",
     *              type="integer",
     *              description="
     *                  0 = New (Only button 'Apply', NOT Message),
     *                  1 = job awaiting approve (Only button 'Accept', 'Decline', NOT Message)
     *                  2 = Application awaiting approve (Message and Button 'Cancel')
     *                  3 = Already interview set up (Only Message NOT Button)
     *                  4 = Application was declined (Only Message NOT Button)
     *                  5 = You declined job (Message and button 'Re-Accept')
     *                  6 = You have already applied (Only Message NOT Button)
     *                  7 = You missed job (Nothing show)
     *              "
     *          ),
     *          @SWG\Property(
     *              property="spec",
     *              type="object",
     *              @SWG\Property(
     *                  property="url",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="adminUrl",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="name",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="size",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="approved",
     *                  type="boolean"
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
     *      description="NOT FOUND",
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
    public function getJobClientDetailsByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        $client = $em->getRepository("AppBundle:User")->find($id);
        $clientDetails = $em->getRepository("AppBundle:CompanyDetails")->findOneBy(['user'=>$client]);
        if($client instanceof User && $clientDetails instanceof CompanyDetails){
            $applicant = NULL;
            $job = NULL;
            $applicantCheck = $em->getRepository("AppBundle:Applicants")->findOneBy(['client'=>$client, 'job'=>$job, 'candidate'=>$this->getUser()]);
            if($applicantCheck instanceof Applicants){
                $applicant = $applicantCheck->getStatus();
            }

            $opportunityCheck = $em->getRepository("AppBundle:Opportunities")->findOneBy(['client'=>$client, 'job'=>$job, 'candidate'=>$this->getUser()]);
            $opportunity = NULL;
            if($opportunityCheck instanceof Opportunities){
                $opportunity = $opportunityCheck->getStatus();
            }

            //New (Only button "Apply")
            $status = 0;
            if(!is_null($applicant) && !is_null($opportunity)){
                if($opportunity == 1){
                    //job awaiting approve (Only button "Accept", "Decline", NOT Message)
                    $status = 1;
                }
                elseif ($applicant == 1){
                    //Application awaiting approve (Message and Button "Cancel")
                    $status = 2;
                }
                elseif($applicant == 3){
                    //Already interview set up (Only Message NOT Button)
                    $status = 3;
                }
                elseif ($applicant == 4){
                    //Application was declined (Only Message NOT Button)
                    $status = 4;
                }
                elseif($opportunity == 2){
                    //You declined job (Message and button "Re-Accept")
                    $status = 5;
                }
                elseif($applicant == 2){
                    //You have already applied (Only Message NOT Button)
                    $status = 6;
                }
                elseif($opportunity == 3){
                    //You missed job (Nothing show)
                    $status = 7;
                }
            }
            elseif(!is_null($applicant) && is_null($opportunity)){
                if ($applicant == 1){
                    //Application awaiting approve (Message and Button "Cancel")
                    $status = 2;
                }
                elseif($applicant == 3){
                    //Already interview set up (Only Message NOT Button)
                    $status = 3;
                }
                elseif ($applicant == 4){
                    //Application was declined (Only Message NOT Button)
                    $status = 4;
                }
                elseif($applicant == 2){
                    //You have already applied (Only Message NOT Button)
                    $status = 6;
                }
            }
            elseif(is_null($applicant) && !is_null($opportunity)){
                if($opportunity == 1){
                    //job awaiting approve (Only button "Accept", "Decline", NOT Message)
                    $status = 1;
                }
                elseif($opportunity == 2){
                    //You declined job (Message and button "Re-Accept")
                    $status = 5;
                }
                elseif($opportunity == 3){
                    //You missed job (Nothing show)
                    $status = 7;
                }
            }

            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);

            $view = $this->view([
                'id' => null,
                'jobTitle' => $client->getJobTitle(),
                'industry' => $clientDetails->getIndustry(),
                'companyAddress' => $clientDetails->getAddress(),
                'addressCity' => $clientDetails->getAddressCity(),
                'companyDescription' => $clientDetails->getDescription(),
                'roleDescription' => null,
                'createdDate' => null,
                'endDate' => null,
                'startedDate' => null,
                'clientID' => $client->getId(),
                'status' => $status,
                'candidateAddress' => ($profileDetails instanceof ProfileDetails) ? $profileDetails->getHomeAddress() : null,
                'spec' => null

            ], Response::HTTP_OK);
        }
        else{
            $view = $this->view(['error'=>'Job Not Found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);


    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     *
     * @Rest\Patch("/{id}",requirements={"id"="\d+"})
     * @SWG\Patch(path="/api/candidate/job/{id}",
     *   tags={"Candidate Job"},
     *   security={true},
     *   summary="Hide Job By Id for Candidate",
     *   description="The method for Hide status job by id for Admin",
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
     *      default="jobId",
     *      description="jobId"
     *   ),
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="hide",
     *              type="boolean",
     *              example=true,
     *              description="required, only true"
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success. Job is hide",
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
    public function hideJobClientByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        $job = $em->getRepository("AppBundle:Job")->find($id);
        if($job instanceof Job){
            if($request->request->has('hide') && $request->request->getBoolean('hide', false) == true){
                $hideJob = $em->getRepository("AppBundle:HideJob")->findOneBy(['user'=>$this->getUser(),'job'=>$job]);
                if(!$hideJob instanceof HideJob){
                    $hideJob = new HideJob($this->getUser(), $job);
                    $em->persist($hideJob);
                    $em->flush();
                }
                $view = $this->view([], Response::HTTP_NO_CONTENT);
            }
            else{
                $view = $this->view(['error'=>'hide should be true'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'Job Not Found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }
}