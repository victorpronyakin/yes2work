<?php
/**
 * Created by PhpStorm.
 * Date: 26.04.18
 * Time: 13:34
 */

namespace AppBundle\Controller\Api\Admin;


use AppBundle\Entity\Applicants;
use AppBundle\Entity\CompanyDetails;
use AppBundle\Entity\Job;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class MainController
 * @package AppBundle\Controller\Api\Admin
 *
 * @Security("has_role('ROLE_ADMIN')")
 */
class MainController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     * @Rest\Get("/dashboard")
     * @SWG\Get(path="/api/admin/dashboard",
     *   tags={"Admin Main"},
     *   security={true},
     *   summary="Get All Data for dashboard",
     *   description="The method for getting all Data for dashboard admin",
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
     *              type="array",
     *              property="newClients",
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
     *              )
     *          ),
     *          @SWG\Property(
     *              type="array",
     *              property="clientFiles",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="jobId",
     *                      type="integer"
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="userId",
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
     *                      property="url",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="adminUrl",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="fileName",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="time",
     *                      type="string"
     *                  )
     *              )
     *          ),
     *
     *          @SWG\Property(
     *              type="array",
     *              property="newFiles",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="userId",
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
     *                      property="url",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="adminUrl",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="fileName",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="fieldName",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="type",
     *                      type="string"
     *                  ),
     *              )
     *          ),
     *          @SWG\Property(
     *              type="array",
     *              property="newVideos",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="userId",
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
     *                      property="url",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="adminUrl",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="fileName",
     *                      type="string"
     *                  ),
     *              ),
     *          ),
     *          @SWG\Property(
     *              type="array",
     *              property="newJobs",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="id",
     *                      type="integer"
     *                  ),
     *                  @SWG\Property(
     *                      property="companyName",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="jobDate",
     *                      type="date",
     *                      example="2018-09-09"
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
     *                  )
     *              )
     *          ),
     *          @SWG\Property(
     *              property="awaitingApplicants",
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
     *              )
     *          ),
     *          @SWG\Property(
     *              property="shortlistApplicants",
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
     *              )
     *          ),
     *          @SWG\Property(
     *              property="interviewsSetUpCandidate",
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
     *              )
     *          ),
     *          @SWG\Property(
     *              property="interviewsSetUpClient",
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
     *              )
     *          ),
     *          @SWG\Property(
     *              property="interviewsPending",
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
     *              )
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
    public function dashboardAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $newClients = $em->getRepository("AppBundle:User")->getClientApprove(['lm'=>5]);
        $clientFiles = $em->getRepository("AppBundle:Job")->getJobSpecApprove(['lm'=>5]);
//        $newCandidates = $em->getRepository("AppBundle:User")->getCandidateApprove(['lm'=>5]);
        $newFiles = $em->getRepository("AppBundle:User")->getCandidateFilesApprove(['lm'=>5]);
        $newJobs = $em->getRepository("AppBundle:Job")->getJobApprove(['lm'=>5]);

        $resultAwaitingApplicants = $em->getRepository("AppBundle:Applicants")->findBy(
            ['status'=>1],
            ['created'=>'ASC'],
            5
        );

        $awaitingApplicants = $this->transformDetailsApplicants($resultAwaitingApplicants, $em);

        $resultShortlistsApplicants = $em->getRepository("AppBundle:Applicants")->findBy(
            ['status'=>2],
            ['created'=>'ASC'],
            5
        );

        $shortlistApplicants = $this->transformDetailsApplicants($resultShortlistsApplicants, $em);

        $resultSetUpInterviewsCandidate = $em->getRepository("AppBundle:Interviews")->findBy(
            ['status'=>1, 'type'=>true],
            ['created'=>'ASC'],
            5
        );
        $setUpInterviewsCandidate = $this->transformDetails($resultSetUpInterviewsCandidate, $em);

        $resultSetUpInterviewsClient = $em->getRepository("AppBundle:Interviews")->findBy(
            ['status'=>1, 'type'=>false],
            ['created'=>'ASC'],
            5
        );
        $setUpInterviewsClient = $this->transformDetails($resultSetUpInterviewsClient, $em);

        $resultPendingInterviews = $em->getRepository("AppBundle:Interviews")->findBy(
            ['status'=>2],
            ['created'=>'ASC'],
            5
        );
        $pendingInterviews = $this->transformDetails($resultPendingInterviews, $em);


        $view = $this->view([
            'newClients'=>$newClients,
            'clientFiles'=>$clientFiles,
//            'newCandidates'=>$newCandidates,
            'newFiles'=>$newFiles,
            'newJobs'=>$newJobs,
            'awaitingApplicants'=>$awaitingApplicants,
            'shortlistApplicants'=>$shortlistApplicants,
            'interviewsSetUpCandidate'=>$setUpInterviewsCandidate,
            'interviewsSetUpClient'=>$setUpInterviewsClient,
            'interviewsPending'=>$pendingInterviews
        ], Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/badges")
     * @SWG\Get(path="/api/admin/badges",
     *   tags={"Admin Main"},
     *   security={true},
     *   summary="Get All badges data",
     *   description="The method for getting all badges data for admin",
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
     *              property="clientNew",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="clientFiles",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="clientAll",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="jobNew",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="jobAll",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="candidateNew",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="candidateFileNew",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="candidateVideoNew",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="candidateAll",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="interviewAll",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="awaitingApplicants",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="shortlistApplicants",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="interviewSetUp",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="interviewPending",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="interviewPlaced",
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

        $clientNew = count($em->getRepository("AppBundle:User")->getClientApprove());
        $clientFiles = count($em->getRepository("AppBundle:Job")->getJobSpecApprove());
        $clientAll = count($em->getRepository("AppBundle:User")->getAllClient());

        $jobNew = $em->getRepository("AppBundle:Job")->count(['approve'=>NULL]);
        $jobAll = $em->getRepository("AppBundle:Job")->count(['status'=>true]);

        $candidateNew = count($em->getRepository("AppBundle:User")->getCandidateApprove());
        $candidateFileNew = count($em->getRepository("AppBundle:User")->getCandidateFilesApprove());
        $candidateVideoNew = count($em->getRepository("AppBundle:User")->getCandidateVideosApprove());
        $candidateAll = count($em->getRepository("AppBundle:User")->getAllCandidateNew());

        $awaitingApplicants = $em->getRepository("AppBundle:Applicants")->count(['status'=>1]);
        $shortlistApplicants = $em->getRepository("AppBundle:Applicants")->count(['status'=>2]);
        $interviewSetUp = $em->getRepository("AppBundle:Interviews")->count(['status'=>1]);
        $interviewPending = $em->getRepository("AppBundle:Interviews")->count(['status'=>2]);
        $interviewPlaced = $em->getRepository("AppBundle:Interviews")->count(['status'=>3]);

        $view = $this->view([
            'clientNew' => $clientNew,
            'clientFiles' => $clientFiles,
            'clientAll' => $clientAll,
            'jobNew' => $jobNew,
            'jobAll' => $jobAll,
            'candidateNew' => $candidateNew,
            'candidateFileNew' => $candidateFileNew,
            'candidateVideoNew' => $candidateVideoNew,
            'candidateAll' => $candidateAll,
            'interviewAll' => ($awaitingApplicants + $shortlistApplicants + $interviewSetUp + $interviewPending + $interviewPlaced),
            'awaitingApplicants' => $awaitingApplicants,
            'shortlistApplicants' => $shortlistApplicants,
            'interviewSetUp' => $interviewSetUp,
            'interviewPending' => $interviewPending,
            'interviewPlaced' => $interviewPlaced,
        ], Response::HTTP_OK);
        return $this->handleView($view);
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
}
