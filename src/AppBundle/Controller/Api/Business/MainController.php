<?php
/**
 * Created by PhpStorm.
 * Date: 11.05.18
 * Time: 14:55
 */

namespace AppBundle\Controller\Api\Business;

use AppBundle\Entity\Applicants;
use AppBundle\Entity\Job;
use AppBundle\Entity\ProfileDetails;
use AppBundle\Entity\Settings;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class MainController
 * @package AppBundle\Controller\Api\Business
 *
 * @Security("has_role('ROLE_CLIENT')")
 */
class MainController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/dashboard")
     * @SWG\Get(path="/api/business/dashboard",
     *   tags={"Business Main"},
     *   security={true},
     *   summary="Get All dashboard data",
     *   description="The method for getting all dashboard data for business",
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
     *      type="string",
     *      default="5",
     *      description="limit record"
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="totalJobs",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="jobs",
     *              type="array",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                  ),
     *                  @SWG\Property(
     *                      property="closureDate",
     *                      type="datetime",
     *                      example="2018-09-09"
     *                  ),
     *                  @SWG\Property(
     *                      property="awaiting",
     *                      type="integer",
     *                  ),
     *                  @SWG\Property(
     *                      property="shortlist",
     *                      type="integer",
     *                  ),
     *                  @SWG\Property(
     *                      property="approved",
     *                      type="integer",
     *                  ),
     *              )
     *          ),
     *          @SWG\Property(
     *              property="stats",
     *              type="object",
     *              @SWG\Property(
     *                  property="awaiting",
     *                  type="integer",
     *              ),
     *              @SWG\Property(
     *                  property="shortlist",
     *                  type="integer",
     *              ),
     *              @SWG\Property(
     *                  property="approved",
     *                  type="integer",
     *              ),
     *          ),
     *          @SWG\Property(
     *              property="firstName",
     *              type="string",
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
    public function getDashboardAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $settings = $em->getRepository('AppBundle:Settings')->findOneBy([]);
        if(!$settings instanceof Settings){
            $settings = new Settings(false);
            $em->persist($settings);
            $em->flush();
        }
        $totalJobs = $em->getRepository("AppBundle:Job")->count(['user'=>$user, 'status'=>true, 'approve'=>true]);

        $resultJobs = $em->getRepository("AppBundle:Job")->findBy(
            ['user'=>$user,'status'=>true,'approve'=>true],
            ['created'=>'DESC'],
            $request->query->getInt('limit', 5)
            );
        $jobs = [];
        foreach ($resultJobs as $job){
            if($job instanceof Job){
                $awaitingCount = 0;
                $shortListCount = 0;
                $approvedCount = 0;
                $applicants = $em->getRepository("AppBundle:Applicants")->findBy(['client'=>$user,'job'=>$job]);
                if(!empty($applicants)){
                    foreach ($applicants as $applicant) {
                        if($applicant instanceof Applicants){
                            if($applicant->getCandidate() instanceof User){
                                $profileDetails = $em->getRepository('AppBundle:ProfileDetails')->findOneBy(['user'=>$applicant->getCandidate()]);
                                if($profileDetails instanceof ProfileDetails){
                                    if(($settings->getAllowVideo() == true) || (isset($profileDetails->getVideo()['approved']) && $profileDetails->getVideo()['approved'] == true)){
                                        $cvFiles = [];
                                        if(!empty($profileDetails->getCopyOfID())){
                                            foreach ($profileDetails->getCopyOfID() as $cvFile){
                                                if(isset($cvFile['approved']) && $cvFile['approved'] == true){
                                                    $cvFiles[] = $cvFile;
                                                }
                                            }
                                        }
                                        if(!empty($cvFiles)){
                                            if($applicant->getStatus() == 1){
                                                $awaitingCount++;
                                            }
                                            elseif($applicant->getStatus() == 2){
                                                $shortListCount++;
                                            }
                                            elseif($applicant->getStatus() == 3){
                                                $approvedCount++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $jobs[] = [
                    'id' => $job->getId(),
                    'jobTitle' => $job->getJobTitle(),
                    'closureDate' => $job->getClosureDate(),
                    'awaiting' => $awaitingCount,
                    'shortlist' => $shortListCount,
                    'approved' => $approvedCount
                ];
            }
        }

        $stats = [];
        $stats['awaiting'] = $stats['shortlist'] = $stats['approved'] = 0;
        $applicants = $em->getRepository("AppBundle:Applicants")->findBy(['client'=>$user]);
        if(!empty($applicants)){
            $candidateIds = [];
            foreach ($applicants as $applicant) {
                if($applicant instanceof Applicants){
                    if($applicant->getCandidate() instanceof User){
                        $profileDetails = $em->getRepository('AppBundle:ProfileDetails')->findOneBy(['user'=>$applicant->getCandidate()]);
                        if($profileDetails instanceof ProfileDetails){
                            $cvFiles = [];
                            if(!empty($profileDetails->getCopyOfID())){
                                foreach ($profileDetails->getCopyOfID() as $cvFile){
                                    if(isset($cvFile['approved']) && $cvFile['approved'] == true){
                                        $cvFiles[] = $cvFile;
                                    }
                                }
                            }
                            if(!empty($cvFiles)){
                                if($applicant->getStatus() == 1){
                                    $stats['awaiting']++;
                                }
                                elseif($applicant->getStatus() == 2){
                                    $stats['shortlist']++;
                                }
                                elseif($applicant->getStatus() == 3){
                                    if(!in_array($applicant->getCandidate()->getId(), $candidateIds)){
                                        $stats['approved']++;
                                        $candidateIds[] = $applicant->getCandidate()->getId();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $view = $this->view([
            'totalJobs' => $totalJobs,
            'jobs' => $jobs,
            'stats' => $stats,
            'firstName' => $user->getFirstName()
        ], Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Get("/badges")
     * @SWG\Get(path="/api/business/badges",
     *   tags={"Business Main"},
     *   security={true},
     *   summary="Get All badges data",
     *   description="The method for getting all badges data for business",
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
     *              property="applicantAll",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="applicantAwaiting",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="applicantShortlist",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="applicantApprove",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="applicantDecline",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="jobAwaiting",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="jobApproved",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="jobOld",
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

        //$applicantAll = $em->getRepository("AppBundle:Applicants")->count(['client'=>$user]);

        $result = $em->getRepository("AppBundle:Applicants")->getApplicantsByClientWithStatusNew($user->getId(),1,$request->query->all());
        $applicantAwaiting = $this->countApplicants($em, $result);

        $result = $em->getRepository("AppBundle:Applicants")->getApplicantsByClientWithStatusNew($user->getId(),2,$request->query->all());
        $applicantShortlist = $this->countApplicants($em, $result);

        $result = $em->getRepository("AppBundle:Applicants")->getApplicantsByClientWithStatusNew($user->getId(),3,$request->query->all());
        $applicantApprove = $this->countApplicants($em, $result, true);

        $result = $em->getRepository("AppBundle:Applicants")->getApplicantsByClientWithStatusNew($user->getId(),4,$request->query->all());
        $applicantDecline = $this->countApplicants($em, $result);

        $jobAwaiting = $em->getRepository("AppBundle:Job")->count(['user'=>$user,'status'=>true,'approve'=>null]);
        $jobApproved = $em->getRepository("AppBundle:Job")->count(['user'=>$user,'status'=>true,'approve'=>true]);
        $jobOld = $em->getRepository("AppBundle:Job")->count(['user'=>$user,'status'=>false]);

        $view = $this->view([
            'applicantAll' => $applicantAwaiting + $applicantShortlist + $applicantApprove + $applicantDecline,
            'applicantAwaiting' => $applicantAwaiting,
            'applicantShortlist' => $applicantShortlist,
            'applicantApprove' => $applicantApprove,
            'applicantDecline' => $applicantDecline,
            'jobAwaiting' => $jobAwaiting,
            'jobApproved' => $jobApproved,
            'jobOld' => $jobOld,
        ], Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/settings")
     * @SWG\Get(path="/api/business/settings",
     *   tags={"Business Main"},
     *   security={true},
     *   summary="Get All Settings",
     *   description="The method for getting all Settings for business",
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

    /**
     * @param EntityManager $em
     * @param array $result
     * @param bool $check
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function countApplicants(EntityManager $em, array $result, $check=false){
        $applicants = 0;
        $candidateIds = [];
        $settings = $em->getRepository('AppBundle:Settings')->findOneBy([]);
        if(!$settings instanceof Settings){
            $settings = new Settings(false);
            $em->persist($settings);
            $em->flush();
        }
        if(!empty($result)){
            foreach ($result as $key=>$applicant){
                $cvFiles = [];
                if(!empty($applicant['copyOfID'])){
                    foreach ($applicant['copyOfID'] as $cvFile){
                        if(isset($cvFile['approved']) && $cvFile['approved'] == true){
                            $cvFiles[] = $cvFile;
                        }
                    }
                }
                if(!empty($cvFiles)){
                    if($check == true){
                        if(isset($applicant['candidateID']) && !in_array($applicant['candidateID'],$candidateIds)){
                            $applicants++;
                            $candidateIds[] = $applicant['candidateID'];
                        }
                    }
                    else{
                        $applicants++;
                    }
                }
            }
        }
        return $applicants;
    }
}
