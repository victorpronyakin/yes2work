<?php
/**
 * Created by PhpStorm.
 * Date: 30.05.18
 * Time: 17:02
 */

namespace AppBundle\Controller\Api\Candidate;
use AppBundle\Entity\Applicants;
use AppBundle\Entity\CompanyDetails;
use AppBundle\Entity\HideJob;
use AppBundle\Entity\Interviews;
use AppBundle\Entity\Job;
use AppBundle\Entity\Opportunities;
use AppBundle\Entity\ProfileDetails;
use AppBundle\Entity\Settings;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class MainController
 * @package AppBundle\Controller\Api\Candidate
 *
 * @Security("has_role('ROLE_CANDIDATE')")
 */
class MainController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Rest\Get("/dashboard")
     * @SWG\Get(path="/api/candidate/dashboard",
     *   tags={"Candidate Main"},
     *   security={true},
     *   summary="Get Candidate Dashboard Details",
     *   description="The method for Getting Candidate Dashboard Details for candidate",
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
     *      name="limit",
     *      in="query",
     *      required=false,
     *      type="integer",
     *      default=5,
     *      description="limit"
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="interviewRequest",
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
     *                      description="1 = Pending, OTHER Set Up"
     *                  )
     *              )
     *          ),
     *          @SWG\Property(
     *              property="interviewRequestTotal",
     *              type="integer",
     *              description="All count interviewRequest"
     *          ),
     *          @SWG\Property(
     *              property="jobAlerts",
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
     *              property="jobAlertsTotal",
     *              type="integer",
     *              description="All count job alerts"
     *          ),
     *          @SWG\Property(
     *              property="application",
     *              type="object",
     *              @SWG\Property(
     *                  property="awaiting",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="successful",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="declined",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="unsuccessful",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="missed",
     *                  type="integer"
     *              ),
     *          ),
     *          @SWG\Property(
     *              property="stats",
     *              type="object",
     *              @SWG\Property(
     *                  property="view",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="unique",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="play",
     *                  type="integer"
     *              ),
     *          ),
     *          @SWG\Property(
     *              property="candidateAddress",
     *              type="string",
     *              example="candidateAddress"
     *          ),
     *          @SWG\Property(
     *              property="allowVideo",
     *              type="boolean"
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
    public function getDashboardDetailsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
        $resultInterviews = $em->getRepository("AppBundle:Interviews")->getInterviewsRequestForCandidate($user->getId());
        $interviewRequest = [];
        if(!empty($resultInterviews)){
            foreach ($resultInterviews as $interview){
                if($interview instanceof Interviews){
                    if($interview->getStatus() == 1 || $interview->getStatus() == 2){
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
                            $interviewRequest[] = $item;
                        }
                    }
                }
            }
        }

        $applicationStats = [];
        $applicationStats['awaiting'] = $applicationStats['successful'] = $applicationStats['unsuccessful'] = 0;

        $applications = $em->getRepository("AppBundle:Applicants")->findBy(['candidate'=>$user]);
        foreach ($applications as $application){
            if($application instanceof Applicants){
                if($application->getStatus() == 1){
                    $applicationStats['awaiting']++;
                }
                elseif ($application->getStatus() == 3 && $application->getCheck() == true){
                    $applicationStats['successful']++;
                }
                elseif ($application->getStatus() == 4){
                    $applicationStats['unsuccessful']++;
                }
            }
        }

        $declinedCount = $em->getRepository("AppBundle:HideJob")->getCountDeclineJobsForCandidate($user->getId());
        $applicationStats['declined'] = (isset($declinedCount['declineCount']) && intval($declinedCount['declineCount']) > 0) ? intval($declinedCount['declineCount']) : 0;;

        $expiredCount = $em->getRepository("AppBundle:Job")->getCountExpiredJobsForCandidate($profileDetails);
        $applicationStats['missed'] = (isset($expiredCount['expiredCount']) && intval($expiredCount['expiredCount']) > 0) ? intval($expiredCount['expiredCount']) : 0;

        $uniqueViewCount = $em->getRepository("AppBundle:ViewUniqueProfile")->count(['candidate'=>$user]);

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

            $jobAlerts = [];
            if(!empty($hideJobsIds)){
                foreach ($result as $job){
                    if(isset($job['id']) && !in_array($job['id'], $hideJobsIds)){
                        $jobAlerts[] = $job;
                    }
                }
            }
            else{
                $jobAlerts = $result;
            }

        }
        else{
            $jobAlerts = $result;
        }
        $settings = $em->getRepository('AppBundle:Settings')->findOneBy([]);
        if(!$settings instanceof Settings){
            $settings = new Settings(false);
            $em->persist($settings);
            $em->flush();
        }
        $view = $this->view([
            'interviewRequest' => $interviewRequest,
            'interviewRequestTotal' => count($interviewRequest),
            'jobAlerts' => $jobAlerts,
            'jobAlertsTotal' => count($jobAlerts),
            'application' => [
                'awaiting' => $applicationStats['awaiting'],
                'successful' => $applicationStats['successful'],
                'declined' => $applicationStats['declined'],
                'unsuccessful' => $applicationStats['unsuccessful'],
                'missed' => $applicationStats['missed']
            ],
            'stats'=>[
                'view' => ($profileDetails instanceof ProfileDetails && $profileDetails->getView() > 0) ? $profileDetails->getView() : 0,
                'unique' => $uniqueViewCount,
                'play' => ($profileDetails instanceof ProfileDetails && $profileDetails->getPlay() > 0) ? $profileDetails->getPlay() : 0
            ],
            'candidateAddress' => ($profileDetails instanceof ProfileDetails) ? $profileDetails->getHomeAddress() : null,
            'allowVideo' => $settings->getAllowVideo()
        ], Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Rest\Get("/badges")
     * @SWG\Get(path="/api/candidate/badges",
     *   tags={"Candidate Main"},
     *   security={true},
     *   summary="Get All badges data",
     *   description="The method for getting all badges data for candidate",
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
     *              property="jobAlertsNew",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="jobAlertsDeclined",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="jobAlertsExpired",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="applicantAwaiting",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="applicantApproved",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="applicantDeclined",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="interviewRequest",
     *              type="integer",
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
    public function getBadgesAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
        $jobAlertsNew = $jobAlertsDeclined = $jobAlertsExpired = $applicantAwaiting = $applicantApproved = $applicantDeclined = $interviewRequest = 0;

        if($profileDetails instanceof ProfileDetails){
            //JOB ALERTS NEW
            $result = $em->getRepository("AppBundle:Job")->getJobsForCandidate($profileDetails);
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

            $jobAlertsNew = count($jobs);
            $jobAlertsDeclinedCount = $em->getRepository("AppBundle:HideJob")->getCountDeclineJobsForCandidate($user->getId());
            $jobAlertsDeclined = (isset($jobAlertsDeclinedCount['declineCount']) && intval($jobAlertsDeclinedCount['declineCount']) > 0) ? intval($jobAlertsDeclinedCount['declineCount']) : 0;;
            $jobAlertsExpiredCount = $em->getRepository("AppBundle:Job")->getCountExpiredJobsForCandidate($profileDetails);
            $jobAlertsExpired = (isset($jobAlertsExpiredCount['expiredCount']) && intval($jobAlertsExpiredCount['expiredCount']) > 0) ? intval($jobAlertsExpiredCount['expiredCount']) : 0;

            $applicantAwaiting = $em->getRepository("AppBundle:Applicants")->count(['candidate'=>$user, 'status'=>1]);
            $applicantApproved = $em->getRepository("AppBundle:Applicants")->count(['candidate'=>$user, 'status'=>3, 'check'=>true]);
            $applicantDeclined = $em->getRepository("AppBundle:Applicants")->count(['candidate'=>$user,'status'=>4]);

            $interviewRequestCount = $em->getRepository("AppBundle:Interviews")->getCountInterviewsRequestForCandidate($user->getId());
            $interviewRequest = (isset($interviewRequestCount['interviewRequestCount']) && intval($interviewRequestCount['interviewRequestCount']) > 0) ? intval($interviewRequestCount['interviewRequestCount']) : 0;;
        }



        $view = $this->view([
            'jobAlertsNew' => $jobAlertsNew,
            'jobAlertsDeclined' => $jobAlertsDeclined,
            'jobAlertsExpired' => $jobAlertsExpired,
            'applicantAwaiting' => $applicantAwaiting,
            'applicantApproved' => $applicantApproved,
            'applicantDeclined' => $applicantDeclined,
            'interviewRequest' => $interviewRequest
        ], Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/settings")
     * @SWG\Get(path="/api/candidate/settings",
     *   tags={"Candidate Main"},
     *   security={true},
     *   summary="Get All Settings",
     *   description="The method for getting all settings for candidate",
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
     *              property="allowVideo",
     *              type="boolean",
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
    public function getSettingsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository('AppBundle:Settings')->findOneBy([]);
        if(!$settings instanceof Settings){
            $settings = new Settings(false);
            $em->persist($settings);
            $em->flush();
        }
        $view = $this->view(['allowVideo'=>$settings->getAllowVideo()], Response::HTTP_OK);
        return $this->handleView($view);
    }
}