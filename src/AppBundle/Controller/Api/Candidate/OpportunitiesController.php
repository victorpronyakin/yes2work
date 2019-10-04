<?php
/**
 * Created by PhpStorm.
 * Date: 04.05.18
 * Time: 11:23
 */

namespace AppBundle\Controller\Api\Candidate;

use AppBundle\Entity\Applicants;
use AppBundle\Entity\CompanyDetails;
use AppBundle\Entity\HideJob;
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
 * Class OpportunitiesController
 * @package AppBundle\Controller\Api\Candidate
 *
 * @Rest\Route("opportunities")
 * @Security("has_role('ROLE_CANDIDATE')")
 */
class OpportunitiesController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/jobAlerts")
     * @SWG\Get(path="/api/candidate/opportunities/jobAlerts",
     *   tags={"Candidate Opportunities"},
     *   security={true},
     *   summary="Get jobAlerts",
     *   description="The method for Getting jobAlerts for candidate",
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
     *              property="new",
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
     *              property="decline",
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
     *              property="expired",
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
     *              property="candidateAddress",
     *              type="string",
     *              example="candidateAddress"
     *          ),
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
    public function getJobAlertsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
        if($profileDetails instanceof ProfileDetails){
            $result = $em->getRepository("AppBundle:Job")->getJobsForCandidate($profileDetails,$request->query->all());
            $applicants = $em->getRepository("AppBundle:Applicants")->findBy(['candidate'=>$user]);
            $interviews = $em->getRepository("AppBundle:Interviews")->findBy(['candidate'=>$user]);
            $hideJobs = $em->getRepository("AppBundle:HideJob")->findBy(['user'=>$user]);
            if(!empty($applicants) || !empty($interviews) || !empty($hideJobs)){
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

                if(!empty($interviews)){
                    foreach ($interviews as $interview){
                        if($interview instanceof Interviews){
                            if($interview->getJob() instanceof Job){
                                $hideJobsIds[] = $interview->getJob()->getId();
                            }
                        }
                    }
                }

                if(!empty($hideJobs)){
                    foreach ($hideJobs as $hideJob){
                        if($hideJob instanceof HideJob){
                            if($hideJob->getJob() instanceof Job){
                                $hideJobsIds[] = $hideJob->getJob()->getId();
                            }
                        }
                    }
                }

                $jobAlerts['new'] = [];
                if(!empty($hideJobsIds)){
                    foreach ($result as $job){
                        if(isset($job['id']) && !in_array($job['id'], $hideJobsIds)){
                            $jobAlerts['new'][] = $job;
                        }
                    }
                }
                else{
                    $jobAlerts['new'] = $result;
                }

            }
            else{
                $jobAlerts['new'] = $result;
            }

            $jobAlerts['decline'] = $em->getRepository('AppBundle:HideJob')->getDeclineJobsForCandidate($user->getId(), $request->query->all());

            $jobAlerts['expired'] = $em->getRepository("AppBundle:Job")->getExpiredJobsForCandidate($profileDetails,$request->query->all());;

            $jobAlerts['candidateAddress'] = $profileDetails->getHomeAddress();

            $view = $this->view($jobAlerts, Response::HTTP_OK);
        }
        else{
            $view = $this->view(['error'=>'Profile not Found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/jobAlerts/new")
     * @SWG\Get(path="/api/candidate/opportunities/jobAlerts/new",
     *   tags={"Candidate Opportunities"},
     *   security={true},
     *   summary="Get New jobAlerts",
     *   description="The method for getting jobAlerts for candidate",
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
    public function getNewJobAlertsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
        if($profileDetails instanceof ProfileDetails){
            $result = $em->getRepository("AppBundle:Job")->getJobsForCandidate($profileDetails,$request->query->all());
            $applicants = $em->getRepository("AppBundle:Applicants")->findBy(['candidate'=>$user]);
            $interviews = $em->getRepository("AppBundle:Interviews")->findBy(['candidate'=>$user]);
            $hideJobs = $em->getRepository("AppBundle:HideJob")->findBy(['user'=>$user]);
            if(!empty($applicants) || !empty($interviews) || !empty($hideJobs)){
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
                if(!empty($interviews)){
                    foreach ($interviews as $interview){
                        if($interview instanceof Interviews){
                            if($interview->getJob() instanceof Job){
                                $hideJobsIds[] = $interview->getJob()->getId();
                            }
                        }
                    }
                }

                if(!empty($hideJobs)){
                    foreach ($hideJobs as $hideJob){
                        if($hideJob instanceof HideJob){
                            if($hideJob->getJob() instanceof Job){
                                $hideJobsIds[] = $hideJob->getJob()->getId();
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
            $view = $this->view(['error'=>'Profile not Found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     *
     * @Rest\Post("/jobAlerts/apply")
     * @SWG\Post(path="/api/candidate/opportunities/jobAlerts/apply",
     *   tags={"Candidate Opportunities"},
     *   security={true},
     *   summary="Apply jobAlerts",
     *   description="The method for Apply jobAlerts for candidate",
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
    public function applyJobAlertsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($request->request->has('jobID') && !empty($request->request->get('jobID'))){
            $job = $em->getRepository("AppBundle:Job")->find($request->request->get('jobID'));
            if($job instanceof Job){
                $client = $job->getUser();
                $applicants = $em->getRepository("AppBundle:Applicants")->findOneBy(['client'=>$client, 'candidate'=>$user,'job'=>$job]);
                if(!$applicants instanceof Applicants){
                    $applicants = new Applicants($client, $user,1,true,$job);
                    $em->persist($applicants);
                    $checkHideJob = $em->getRepository("AppBundle:HideJob")->findOneBy(['user'=>$user,'job'=>$job]);
                    if($checkHideJob instanceof HideJob){
                        $em->remove($checkHideJob);
                    }
                    $em->flush();
                    $link = $request->getSchemeAndHttpHost()."/business/applicants_awaiting";
                    if($job instanceof Job){
                        $link .= "?jobId=".$job->getId();
                    }
                    $emailData = array(
                        'client' => ['firstName'=>$client->getFirstName()],
                        'jobTitle' => ($job instanceof Job) ? $job->getJobTitle() : "",
                        'link' => $link
                    );
                    $message = (new \Swift_Message('A young prospective employee has applied for a job on Yes2Work'))
                        ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                        ->setTo($client->getEmail())
                        ->setBody(
                            $this->renderView('emails/client/candidate_application.html.twig',
                                $emailData
                            ),
                            'text/html'
                        );

                    SendEmail::sendEmailForClient($client, $em, $message, $this->get('mailer'), $emailData, SendEmail::TYPE_CLIENT_CANDIDATE_APPLICANT);
                    $view = $this->view([], Response::HTTP_NO_CONTENT);
                }
                else{
                    $view = $this->view(['error'=>'You already application on this job post'], Response::HTTP_BAD_REQUEST);
                }
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
     * @Rest\Get("/jobAlerts/decline")
     * @SWG\Get(path="/api/candidate/opportunities/jobAlerts/decline",
     *   tags={"Candidate Opportunities"},
     *   security={true},
     *   summary="Get Declined jobAlerts",
     *   description="The method for Getting Declined jobAlerts for candidate",
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
    public function getDeclineJobAlertsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);
        if($profileDetails instanceof ProfileDetails){
            $hideJobs = $em->getRepository('AppBundle:HideJob')->getDeclineJobsForCandidate($this->getUser()->getId(), $request->query->all());

            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $hideJobs,
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
     * @return Response
     *
     * @Rest\Post("/jobAlerts/decline")
     * @SWG\Post(path="/api/candidate/opportunities/jobAlerts/decline",
     *   tags={"Candidate Opportunities"},
     *   security={true},
     *   summary="Decline jobAlerts",
     *   description="The method for  Decline jobAlerts for candidate",
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
    public function declineJobAlertsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        if($request->request->has('jobID') && !empty($request->request->get('jobID'))){
            $job = $em->getRepository("AppBundle:Job")->find($request->request->get('jobID'));
            if($job instanceof Job){
                $hideJob = $em->getRepository("AppBundle:HideJob")->findOneBy(['user'=>$this->getUser(),'job'=>$job]);
                if(!$hideJob instanceof HideJob){
                    $hideJob = new HideJob($this->getUser(), $job);
                    $em->persist($hideJob);
                    $em->flush();
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
     * @Rest\Get("/jobAlerts/expired")
     * @SWG\Get(path="/api/candidate/opportunities/jobAlerts/expired",
     *   tags={"Candidate Opportunities"},
     *   security={true},
     *   summary="Get Expired jobAlerts",
     *   description="The method for getting expired jobAlerts for candidate",
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
     *   ),
     *     @SWG\Response(
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
    public function getExpiredJobAlertsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
        if($profileDetails instanceof ProfileDetails){
            $expiredJobs = $em->getRepository("AppBundle:Job")->getExpiredJobsForCandidate($profileDetails,$request->query->all());

            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $expiredJobs,
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

    //-------------OLD--------------------
    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/")
     * @SWG\Get(path="/api/candidate/opportunities/",
     *   tags={"Candidate Opportunities"},
     *   security={true},
     *   summary="Get Opportunities OLD",
     *   description="The method for Getting Opportunities for candidate",
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
     *              property="new",
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
     *                      property="companyName",
     *                      type="string",
     *                      example="companyName",
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
     *                  @SWG\Property(
     *                      property="dateClose",
     *                      type="datetime",
     *                      example="2018-09-09",
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
     *                      property="industry",
     *                      type="integer",
     *                      description="1 = Financial Services, 2 = Non-Financial Services",
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
     *                      property="location",
     *                      type="string",
     *                      example="location",
     *                  ),
     *                  @SWG\Property(
     *                      property="addressCity",
     *                      type="string",
     *                      example="addressCity",
     *                  ),
     *                  @SWG\Property(
     *                      property="reAccept",
     *                      type="boolean",
     *                      example=true,
     *                      description="if true = show button Re-Accept, else not show"
     *                  ),
     *                  @SWG\Property(
     *                      property="dateClose",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  ),
     *              )
     *          ),
     *          @SWG\Property(
     *              property="missed",
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
     *                      type="integer",
     *                      description="1 = Financial Services, 2 = Non-Financial Services",
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
     *                      property="location",
     *                      type="string",
     *                      example="location",
     *                  ),
     *                  @SWG\Property(
     *                      property="addressCity",
     *                      type="string",
     *                      example="addressCity",
     *                  ),
     *                  @SWG\Property(
     *                      property="dateClose",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  ),
     *              )
     *          ),
     *          @SWG\Property(
     *              property="candidateAddress",
     *              type="string",
     *              example="candidateAddress"
     *          ),
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
    public function getOpportunitiesAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $newOpportunities = $em->getRepository("AppBundle:Opportunities")->getOpportunityForCandidateWithStatus($user->getId(),1,$request->query->all());
        $result['new'] = $this->transformDetails($newOpportunities, $em);

        $declineOpportunities = $em->getRepository("AppBundle:Opportunities")->getOpportunityForCandidateWithStatus($user->getId(),2,$request->query->all());
        $result['decline'] = $this->transformDetails($declineOpportunities, $em);

        $missedOpportunities = $em->getRepository("AppBundle:Opportunities")->getOpportunityForCandidateWithStatus($user->getId(),3, $request->query->all());
        $result['missed'] = $this->transformDetails($missedOpportunities, $em);

        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);

        $result['candidateAddress'] = ($profileDetails instanceof ProfileDetails) ? $profileDetails->getHomeAddress() : null;

        $view = $this->view($result, Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/new")
     * @SWG\Get(path="/api/candidate/opportunities/new",
     *   tags={"Candidate Opportunities"},
     *   security={true},
     *   summary="Get New Jobs Alerts OLD",
     *   description="The method for Getting New Jobs Alerts for candidate",
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
     *                  @SWG\Property(
     *                      property="dateClose",
     *                      type="datetime",
     *                      example="2018-09-09",
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
    public function getNewJobPostAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $newOpportunities = $em->getRepository("AppBundle:Opportunities")->getOpportunityForCandidateWithStatus($user->getId(),1,$request->query->all());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $newOpportunities,
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     *
     * @Rest\Post("/approve")
     * @SWG\Post(path="/api/candidate/opportunities/approve",
     *   tags={"Candidate Opportunities"},
     *   security={true},
     *   summary="Approve Job Post OLD",
     *   description="The method for Approve Job Pos for candidate",
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
    public function approveJobPostAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($request->request->has('clientID') && !empty($request->request->get('clientID'))){
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
                $opportunities = $em->getRepository("AppBundle:Opportunities")->findOneBy(['client'=>$client, 'candidate'=>$user,'job'=>$job]);
                if($opportunities instanceof Opportunities){
                    if($opportunities->getStatus() == 1){
                        $checkApplicants = $em->getRepository("AppBundle:Applicants")->findOneBy(['client'=>$client, 'candidate'=>$user, 'job'=>$job]);
                        if($checkApplicants instanceof Applicants){
                            $checkApplicants->setStatus(3);
                            $checkApplicants->setCheck(1);
                            $checkApplicants->setCreated(new \DateTime());
                        }
                        else{
                            $checkApplicants = new Applicants($client, $user, 3, true, $job);
                        }
                        $em->persist($checkApplicants);
                        $em->remove($opportunities);
                        $checkInterviews = $em->getRepository("AppBundle:Interviews")->findOneBy(['client'=>$client,'candidate'=>$user,'job'=>$job]);
                        if(!$checkInterviews instanceof Interviews){
                            $interviews = new Interviews($client, $user, 1, $job);
                            $em->persist($interviews);
                            $em->flush();

                            $emailData = array(
                                'client' => ['firstName'=>$checkApplicants->getClient()->getFirstName(),'lastName'=>$checkApplicants->getClient()->getLastName()],
                                'candidate' => ['firstName'=>$checkApplicants->getCandidate()->getFirstName(),'lastName'=>$checkApplicants->getCandidate()->getLastName()]
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
                    elseif ($opportunities->getStatus() == 2){
                        $checkApplicants = $em->getRepository("AppBundle:Applicants")->findOneBy(['client'=>$client, 'candidate'=>$user, 'job'=>$job]);
                        if($checkApplicants instanceof Applicants){
                            $checkApplicants->setStatus(1);
                            $checkApplicants->setCheck(true);
                            $checkApplicants->setCreated(new \DateTime());
                        }
                        else{
                            $checkApplicants = new Applicants($client, $user, 1, true, $job);
                        }
                        $em->persist($checkApplicants);
                        $em->remove($opportunities);
                        $em->flush();

                        $emailData = array(
                            'client' => ['firstName'=>$client->getFirstName(),'lastName'=>$client->getLastName()],
                            'jobTitle' => ($job instanceof Job) ? $job->getJobTitle() : "",
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

                        $view = $this->view([], Response::HTTP_NO_CONTENT);
                    }
                    else{
                        $view = $this->view(['error'=>'Job already missed'], Response::HTTP_BAD_REQUEST);
                    }
                }
                else{
                    $view = $this->view(['error'=>'Opportunities Not Found'], Response::HTTP_NOT_FOUND);
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
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/decline")
     * @SWG\Get(path="/api/candidate/opportunities/decline",
     *   tags={"Candidate Opportunities"},
     *   security={true},
     *   summary="Get Declined Job Post OLD",
     *   description="The method for Getting Declined Job Post for candidate",
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
     *                  @SWG\Property(
     *                      property="reAccept",
     *                      type="boolean",
     *                      example=true,
     *                      description="if true = show button Re-Accept, else not show"
     *                  ),
     *                  @SWG\Property(
     *                      property="dateClose",
     *                      type="datetime",
     *                      example="2018-09-09",
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
        $declineOpportunities = $em->getRepository("AppBundle:Opportunities")->getOpportunityForCandidateWithStatus($user->getId(),2,$request->query->all());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $declineOpportunities,
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     *
     * @Rest\Post("/decline")
     * @SWG\Post(path="/api/candidate/opportunities/decline",
     *   tags={"Candidate Opportunities"},
     *   security={true},
     *   summary="Decline Job Post OLD",
     *   description="The method for  Decline Job Post for candidate",
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
    public function declineJobPostAction(Request $request){
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
                $opportunities = $em->getRepository("AppBundle:Opportunities")->findOneBy(['client'=>$client, 'candidate'=>$user,'job'=>$job]);
                if($opportunities instanceof Opportunities){
                    if($opportunities->getStatus() == 1){
                        $opportunities->setStatus(2);
                        $opportunities->setCreated(new \DateTime());
                        $em->persist($opportunities);
                        $em->flush();

                        $emailData = array(
                            'client' => ['firstName'=>$client->getFirstName(),'lastName'=>$client->getLastName()],
                            'jobTitle' => ($job instanceof Job) ? $job->getJobTitle() : "",
                            'link' => $request->getSchemeAndHttpHost()
                        );

                        $message = (new \Swift_Message('Candidates have declined your Job Post'))
                            ->setFrom($this->container->getParameter('mailer_user'), 'Yes2Work')
                            ->setTo($client->getEmail())
                            ->setBody(
                                $this->renderView('emails/client/candidate_decline.html.twig',
                                    $emailData
                                ),
                                'text/html'
                            );
                        SendEmail::sendEmailForClient($client, $em, $message, $this->get('mailer'), $emailData, SendEmail::TYPE_CLIENT_CANDIDATE_DECLINE);

                        $view = $this->view([], Response::HTTP_NO_CONTENT);
                    }
                    else{
                        $view = $this->view(['error'=>'Job already declined or missed'], Response::HTTP_BAD_REQUEST);
                    }
                }
                else{
                    $view = $this->view(['error'=>'Opportunities Not Found'], Response::HTTP_NOT_FOUND);
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
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/missed")
     * @SWG\Get(path="/api/candidate/opportunities/missed",
     *   tags={"Candidate Opportunities"},
     *   security={true},
     *   summary="Get Missed Job Post OLD",
     *   description="The method for Getting Missed Job Post for candidate",
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
     *                  @SWG\Property(
     *                      property="dateClose",
     *                      type="datetime",
     *                      example="2018-09-09",
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
    public function getMissedJobPostAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $missedOpportunities = $em->getRepository("AppBundle:Opportunities")->getOpportunityForCandidateWithStatus($user->getId(),3,$request->query->all());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $missedOpportunities,
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
     * @param array $opportunities
     * @param EntityManager $em
     * @return array
     */
    private function transformDetails(array $opportunities, EntityManager $em){
        $result = [];
        if(!empty($opportunities)){
            foreach ($opportunities as $new){
                $temp = [
                    'created' => $new->getCreated(),
                    'clientID' => $new->getClient()->getId(),
                    'reAccept' => false,
                    'dateClose' => null
                ];
                $job = $new->getJob();
                if($job instanceof Job){
                    $temp['jobID'] = $job->getId();
                    $temp['companyName'] = $job->getCompanyName();
                    $temp['industry'] = $job->getIndustry();
                    $temp['jobTitle'] = $job->getJobTitle();
                    $temp['location'] = $job->getCompanyAddress();
                    $temp['addressCity'] = $job->getAddressCity();
                    $temp['dateClose'] = $job->getClosureDate();
                    if($job->getStatus() == true){
                        $temp['reAccept'] = true;
                    }
                }
                else{
                    $companyDetails = $em->getRepository('AppBundle:CompanyDetails')->findOneBy(['user'=>$new->getClient()]);
                    if($companyDetails instanceof CompanyDetails){
                        $temp['jobID'] = NULL;
                        $temp['companyName'] = $companyDetails->getName();
                        $temp['industry'] = $companyDetails->getIndustry();
                        $temp['jobTitle'] = $new->getClient()->getJobTitle();
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