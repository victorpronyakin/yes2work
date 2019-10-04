<?php
/**
 * Created by PhpStorm.
 * Date: 02.05.18
 * Time: 16:04
 */

namespace AppBundle\Controller\Api\Business;


use AppBundle\Entity\Applicants;
use AppBundle\Entity\CompanyDetails;
use AppBundle\Entity\Interviews;
use AppBundle\Entity\Job;
use AppBundle\Entity\Opportunities;
use AppBundle\Entity\ProfileDetails;
use AppBundle\Entity\Settings;
use AppBundle\Entity\User;
use AppBundle\Helper\SendEmail;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class ApplicantsController
 * @package AppBundle\Controller\Api\Business
 *
 * @Rest\Route("applicants")
 * @Security("has_role('ROLE_CLIENT')")
 */
class ApplicantsController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Get("/")
     * @SWG\Get(path="/api/business/applicants/",
     *   tags={"Business Applicants"},
     *   security={true},
     *   summary="Get Applicants",
     *   description="The method for Getting Applicants for business",
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
     *      name="jobID",
     *      in="query",
     *      required=false,
     *      type="integer",
     *      description="jobID"
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="awaiting",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="shortList",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="approve",
     *              type="integer",
     *          ),
     *          @SWG\Property(
     *              property="decline",
     *              type="integer",
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
    public function getApplicantsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        /*$job = null;
        if($request->query->has('jobID') && $request->query->getInt('jobID',0) > 0){
            $job = $em->getRepository("AppBundle:Job")->find($request->query->getInt('jobID',0));
        }*/

        //$resultAwaiting = $em->getRepository("AppBundle:Applicants")->count(($job instanceof Job) ? ['client' => $user, 'status' => 1, 'job'=>$job] : ['client' => $user, 'status' => 1 ]);
        //$resultShortList = $em->getRepository("AppBundle:Applicants")->count(($job instanceof Job) ? ['client' => $user, 'status' => 2, 'job'=>$job] : ['client' => $user, 'status' => 2 ]);
        //$resultApproved = $em->getRepository("AppBundle:Applicants")->count(($job instanceof Job) ? ['client' => $user, 'status' => 3, 'job'=>$job] : ['client' => $user, 'status' => 3 ]);
        //$resultDeclined = $em->getRepository("AppBundle:Applicants")->count(($job instanceof Job) ? ['client' => $user, 'status' => 4, 'job'=>$job] : ['client' => $user, 'status' => 4 ]);

        $result = $em->getRepository("AppBundle:Applicants")->getApplicantsByClientWithStatus($user->getId(),1,$request->query->all());
        $resultAwaiting = $this->generateApplicantsData($em, $result,$request->query->all());

        $result = $em->getRepository("AppBundle:Applicants")->getApplicantsByClientWithStatus($user->getId(),2,$request->query->all());
        $resultShortList = $this->generateApplicantsData($em, $result,$request->query->all());

        $result = $em->getRepository("AppBundle:Applicants")->getApplicantsByClientWithStatus($user->getId(),3,$request->query->all());
        $resultApproved = $this->generateApplicantsData($em, $result,$request->query->all());
        $itemsApproved = [];
        if(!empty($resultApproved)){
            $candidateIds = [];
            foreach ($resultApproved as $item){
                if(isset($item['candidateID']) && !in_array($item['candidateID'],$candidateIds)){
                    $itemsApproved[] = $item;
                    $candidateIds[] = $item['candidateID'];
                }
            }
        }
        $result = $em->getRepository("AppBundle:Applicants")->getApplicantsByClientWithStatus($user->getId(),4,$request->query->all());
        $resultDeclined = $this->generateApplicantsData($em, $result,$request->query->all());

        $view = $this->view([
            'awaiting'=>count($resultAwaiting),
            'shortList'=>count($resultShortList),
            'approve'=>count($itemsApproved),
            'decline'=>count($resultDeclined),
        ], Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     *
     * @Rest\Get("/{id}", requirements={"id"="\d+"})
     * @SWG\Get(path="/api/business/applicants/{id}",
     *   tags={"Business Applicants"},
     *   security={true},
     *   summary="Get Applicant details",
     *   description="The method for Getting Applicant details for business",
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
     *              type="object",
     *              property="details",
     *              @SWG\Property(
     *                  property="applicationID",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="jobID",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="candidateID",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="firstName",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="lastName",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="articlesFirm",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="articlesFirmName",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="boards",
     *                  type="integer",
     *                  example=1,
     *                  description="1 = Passed Both Board Exams First Time, 2 = Passed Both Board Exams, 3 = ITC passed, APC Outstanding, 4 = ITC Outstanding"
     *              ),
     *              @SWG\Property(
     *                  property="nationality",
     *                  type="integer",
     *                  description="1=South African, 2=Other",
     *                  example=1,
     *              ),
     *              @SWG\Property(
     *                  property="ethnicity",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="availability",
     *                  type="boolean",
     *                  description="true = immediately, false = see dateAvailability"
     *              ),
     *              @SWG\Property(
     *                  property="availabilityPeriod",
     *                  type="integer",
     *                  description="
     *                      1=30 Day notice period
     *                      2=60 Day notice period
     *                      3=90 Day notice period
     *                      4=I can provide a specific date"
     *              ),
     *              @SWG\Property(
     *                  property="dateAvailability",
     *                  type="datetime",
     *                  example="2018-09-09"
     *              ),
     *              @SWG\Property(
     *                  property="citiesWorking",
     *                  type="array",
     *                  @SWG\Items(type="string")
     *              ),
     *              @SWG\Property(
     *                  property="otherQualifications",
     *                  type="string",
     *              ),
     *              @SWG\Property(
     *                  property="criminal",
     *                  type="boolean",
     *                  description="true=Yes, false=No"
     *              ),
     *              @SWG\Property(
     *                  property="credit",
     *                  type="boolean",
     *                  description="true=Yes, false=No"
     *              ),
     *              @SWG\Property(
     *                  property="matricCertificate",
     *                  type="array",
     *                  description="use in Academic Certificates and Degrees",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(
     *                          property="url",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="size",
     *                          type="integer"
     *                      ),
     *                      @SWG\Property(
     *                          property="approved",
     *                          type="boolean",
     *                          description="if false not show"
     *                      ),
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="tertiaryCertificate",
     *                  type="array",
     *                  description="use in Academic Certificates and Degrees",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(
     *                          property="url",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="size",
     *                          type="integer"
     *                      ),
     *                      @SWG\Property(
     *                          property="approved",
     *                          type="boolean",
     *                          description="if false not show"
     *                      ),
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="universityManuscript",
     *                  type="array",
     *                  description="use in Academic Transcripts",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(
     *                          property="url",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="size",
     *                          type="integer"
     *                      ),
     *                      @SWG\Property(
     *                          property="approved",
     *                          type="boolean",
     *                          description="if false not show"
     *                      ),
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="creditCheck",
     *                  type="array",
     *                  description="use in Credit and Criminal Check",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(
     *                          property="url",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="size",
     *                          type="integer"
     *                      ),
     *                      @SWG\Property(
     *                          property="approved",
     *                          type="boolean",
     *                          description="if false not show"
     *                      ),
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="cvFiles",
     *                  type="array",
     *                  description="use for download CV",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(
     *                          property="url",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="size",
     *                          type="integer"
     *                      ),
     *                      @SWG\Property(
     *                          property="approved",
     *                          type="boolean",
     *                          description="if false not show"
     *                      ),
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="video",
     *                  @SWG\Property(
     *                      type="string",
     *                      property="url",
     *                  ),
     *                  @SWG\Property(
     *                      type="string",
     *                      property="name",
     *                  ),
     *                  @SWG\Property(
     *                      type="boolean",
     *                      property="approved",
     *                      example=true,
     *                      description="if approved == true show video else not show video"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="mostRole",
     *                  type="string",
     *              ),
     *              @SWG\Property(
     *                  property="mostEmployer",
     *                  type="string",
     *              ),
     *              @SWG\Property(
     *                  property="status",
     *                  type="integer",
     *                  description="
     *                              1 = Awaiting
     *                              2 = Shortlist
     *                              3 = Approved
     *                              4 = Decline"
     *              ),
     *              @SWG\Property(
     *                  property="check",
     *                  type="boolean",
     *                  description="check only short list button"
     *              ),
     *          ),
     *          @SWG\Property(
     *              property="achievements",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="id",
     *                      type="integer"
     *                  ),
     *                  @SWG\Property(
     *                      property="description",
     *                      type="string"
     *                  ),
     *              ),
     *          ),
     *          @SWG\Property(
     *              property="references",
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
     *                      property="company",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="role",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="email",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="comment",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="permission",
     *                      type="boolean",
     *                      description="if = false not show email and lastName"
     *                  ),
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
    public function getApplicantDetailsAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $applicant = $em->getRepository("AppBundle:Applicants")->find($id);
        if($applicant instanceof Applicants){
            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$applicant->getCandidate()]);
            if($profileDetails instanceof ProfileDetails){
                $achievements = $em->getRepository("CandidateQualifications")->getAchievementsCandidate($applicant->getCandidate()->getId());
                $references = $em->getRepository("AppBundle:CandidateReferences")->getReferencesCandidate($applicant->getCandidate()->getId());
                $candidateDetails = [
                    'applicationID' => $applicant->getId(),
                    'jobID' => ($applicant->getJob() instanceof Job) ? $applicant->getJob()->getId() : $applicant->getJob(),
                    'candidateID' => $applicant->getCandidate()->getId(),
                    'firstName' => $applicant->getCandidate()->getFirstName(),
                    'lastName' => $applicant->getCandidate()->getLastName(),
                    'articlesFirm' => $profileDetails->getArticlesFirm(),
                    'articlesFirmName' => $profileDetails->getArticlesFirmName(),
                    'boards' => $profileDetails->getBoards(),
                    'nationality' => $profileDetails->getNationality(),
                    'ethnicity' => $profileDetails->getEthnicity(),
                    'availability' => $profileDetails->getAvailability(),
                    'availabilityPeriod' => $profileDetails->getAvailabilityPeriod(),
                    'dateAvailability' => $profileDetails->getDateAvailability(),
                    'citiesWorking' => $profileDetails->getCitiesWorking(),
                    'otherQualifications' => $profileDetails->getOtherQualifications(),
                    'criminal' => $profileDetails->getCriminal(),
                    'credit' => $profileDetails->getCredit(),
                    'matricCertificate' => $profileDetails->getMatricCertificate(),
                    'tertiaryCertificate' => $profileDetails->getTertiaryCertificate(),
                    'universityManuscript' => $profileDetails->getUniversityManuscript(),
                    'creditCheck' => $profileDetails->getCreditCheck(),
                    'cvFiles' => $profileDetails->getCvFiles(),
                    'video' => $profileDetails->getVideo(),
                    'mostRole' => $profileDetails->getMostRole(),
                    'mostEmployer' => $profileDetails->getMostEmployer(),
                    'status' => $applicant->getStatus(),
                    'check' => $applicant->getCheck()
                ];

                $view = $this->view(
                    [
                        'details'=>$candidateDetails,
                        'achievements'=>$achievements,
                        'references'=>$references
                    ],
                    Response::HTTP_OK
                );
            }
            else{
                $view = $this->view(['error'=>'ProfileDetails not found'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'Applicant not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Get("/count")
     * @SWG\Get(path="/api/business/applicants/count",
     *   tags={"Business Applicants"},
     *   security={true},
     *   summary="Get Count Applicants",
     *   description="The method for Getting Count Applicants for business",
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
	 *      name="jobID",
	 *      in="query",
	 *      required=false,
	 *      type="integer",
	 *      description="jobID"
	 *   ),
     *   @SWG\Parameter(
     *      name="status",
     *      in="query",
     *      required=true,
     *      type="integer",
     *      description="1 = Awaiting, 2 = ShortList, 3 = Approve, 4 = Declined"
     *   ),
     *   @SWG\Parameter(
     *      name="search",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="search be name"
     *   ),
     *   @SWG\Parameter(
     *      name="gender",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by gender.All or Male or Female"
     *   ),
     *   @SWG\Parameter(
     *      name="ethnicity",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by ethnicity. All OR Black OR White Or Coloured Or Indian Or Oriental"
     *   ),
     *   @SWG\Parameter(
     *      name="location",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by location."
     *   ),
     *   @SWG\Parameter(
     *      name="availability",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by availability. All, 1 = Immediately, 2 = Within 1 month,3 = Within 3 month"
     *   ),
	 *   @SWG\Parameter(
	 *      name="video",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="All",
	 *      description="Sort by video.All or Yes or No"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="highestQualification",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="All",
	 *      description="Sort by highestQualification."
	 *   ),
	 *   @SWG\Parameter(
	 *      name="field",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="All",
	 *      description="Sort by field."
	 *   ),
	 *   @SWG\Parameter(
	 *      name="monthSalaryFrom",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="Sort by min salary, 0 or 3500"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="monthSalaryTo",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="Sort by max salary. 3500"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="eligibility",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="Sort by eligibility.All or applicable"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="yearsOfWorkExperience",
	 *      in="query",
	 *      required=false,
	 *      type="integer",
	 *      default="",
	 *      description="Sort by yearsOfWorkExperience. 0 - All, 1 = 0, 2 = 0-1, 3 = 1-2, 4 = 3-5, 5 = 5+"
	 *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          @SWG\Property(
     *              property="countApplicants",
     *              type="integer"
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
    public function getCountApplicantAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($request->query->has('status') && in_array($request->query->get('status'),[1,2,3,4])){
        	$params = $request->query->all();
            //$resultAwaiting = $em->getRepository("AppBundle:Applicants")->getCountApplicantsByClientWithStatus($user->getId(),$request->query->get('status'),$request->query->all());
            $result= $em->getRepository("AppBundle:Applicants")->getApplicantsByClientWithStatusNew($user->getId(),$request->query->get('status'),$params);
            $resultItems = $this->generateApplicantsData($em, $result,$params);

            if($request->query->get('status') == 3){
                $items = [];
                if(!empty($resultItems)){
                    $candidateIds = [];
                    foreach ($resultItems as $item){
                        if(isset($item['candidateID']) && !in_array($item['candidateID'],$candidateIds)){
                            $items[] = $item;
                            $candidateIds[] = $item['candidateID'];
                        }
                    }
                }
            }
            else{
                $items = $resultItems;
            }
            $view = $this->view(['countApplicants'=>count($items)], Response::HTTP_OK);
        }
        else{
            $view = $this->view(['error'=>'status is required'], Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Get("/awaiting")
     * @SWG\Get(path="/api/business/applicants/awaiting",
     *   tags={"Business Applicants"},
     *   security={true},
     *   summary="Get Awaiting Approve Candidates",
     *   description="The method for Getting Awaiting Approve Candidates for business",
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
     *      name="jobID",
     *      in="query",
     *      required=false,
     *      type="integer",
     *      description="jobID"
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
     *      description="search be name"
     *   ),
     *   @SWG\Parameter(
     *      name="gender",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by gender.All or Male or Female"
     *   ),
     *   @SWG\Parameter(
     *      name="ethnicity",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by ethnicity. All OR Black OR White Or Coloured Or Indian Or Oriental"
     *   ),
     *   @SWG\Parameter(
     *      name="location",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by location."
     *   ),
     *   @SWG\Parameter(
     *      name="availability",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by availability. All, 1 = Immediately, 2 = Within 1 month,3 = Within 3 month"
     *   ),
	 *   @SWG\Parameter(
	 *      name="video",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="All",
	 *      description="Sort by video.All or Yes or No"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="highestQualification",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="All",
	 *      description="Sort by highestQualification."
	 *   ),
	 *   @SWG\Parameter(
	 *      name="field",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="All",
	 *      description="Sort by field."
	 *   ),
	 *   @SWG\Parameter(
	 *      name="monthSalaryFrom",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="Sort by min salary, 0 or 3500"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="monthSalaryTo",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="Sort by max salary. 3500"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="orderBy",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="candidateID / highestQualification / jobID / firstName / lastName / role / employer / availability / picture / video / mostSalary / salaryPeriod / created / yearsOfWorkExperience / field"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="orderSort",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="ASC / DESC"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="eligibility",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="Sort by eligibility.All or applicable"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="yearsOfWorkExperience",
	 *      in="query",
	 *      required=false,
	 *      type="integer",
	 *      default="",
	 *      description="Sort by yearsOfWorkExperience. 0 = All, 1 = 0, 2 = 0-1, 3 = 1-2, 4 = 3-5, 5 = 5+"
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
     *                      property="candidateID",
     *                      type="integer",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="firstName",
     *                      type="string",
     *                      example="firstName",
     *                  ),
     *                  @SWG\Property(
     *                      property="lastName",
     *                      type="string",
     *                      example="firstName",
     *                  ),
     *                  @SWG\Property(
     *                      property="articlesFirm",
     *                      type="string",
     *                      example="articlesFirm",
     *                  ),
     *                  @SWG\Property(
     *                      property="articlesFirmName",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="dateArticlesCompleted",
     *                      type="datetime"
     *                  ),
     *                  @SWG\Property(
     *                      property="availability",
     *                      type="boolean",
     *                      description="if false see dateAvailability, else immediately",
     *                  ),
     *                  @SWG\Property(
     *                      property="availabilityPeriod",
     *                      type="integer",
     *                      description="
     *                          1=30 Day notice period
     *                          2=60 Day notice period
     *                          3=90 Day notice period
     *                          4=I can provide a specific date"
     *                  ),
     *                  @SWG\Property(
     *                      property="dateAvailability",
     *                      type="date",
     *                      example="2018-09-09",
     *                      description="need check if dateAvailability < now than show immediately",
     *                  ),
     *                  @SWG\Property(
     *                      property="picture",
     *                      type="string",
     *                      description="url on file or NULL"
     *                  ),
     *                  @SWG\Property(
     *                      property="video",
     *                      type="string",
     *                      description="url on file or NULL"
     *                  ),
     *                  @SWG\Property(
     *                      property="cvFiles",
     *                      type="string",
     *                      description="url on file or NULL"
     *                  ),
     *                  @SWG\Property(
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  ),
     *                  @SWG\Property(
     *                      property="employer",
     *                      type="string",
     *                      example="employer",
     *                  ),
     *                  @SWG\Property(
     *                      property="role",
     *                      type="string",
     *                      example="role",
     *                  ),
	 *              	@SWG\Property(
	 *              	    property="mostSalary",
	 *              	    type="integer",
	 *                      example="123123"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="salaryPeriod",
	 *              	    type="string",
	 *                      example="monthly or annual"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="highestQualification",
	 *              	    type="string",
	 *                      example="NQF 8 - Honours"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="yearsOfWorkExperience",
	 *              	    type="integer",
	 *                      example="1"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="field",
	 *              	    type="string",
	 *                      example="Accounting and Finance",
	 *                      description="specialization or NULL"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="applicable",
	 *              	    type="string",
	 *                      example="true or false"
	 *              	)
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
    public function getAwaitingApproveCandidateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
		$params = $request->query->all();

        $resultAwaiting = $em->getRepository("AppBundle:Applicants")->getApplicantsByClientWithStatusNew($user->getId(),1,$params);
        $items = $this->generateApplicantsData($em, $resultAwaiting,$params);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $items,
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
    }// /api/business/applicants/awaiting

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Get("/shortList")
     * @SWG\Get(path="/api/business/applicants/shortList",
     *   tags={"Business Applicants"},
     *   security={true},
     *   summary="Get Short List Candidates",
     *   description="The method for Getting Short List Candidates for business",
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
     *      name="jobID",
     *      in="query",
     *      required=false,
     *      type="integer",
     *      description="jobID"
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
     *      description="search be name"
     *   ),
     *   @SWG\Parameter(
     *      name="gender",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by gender.All or Male or Female"
     *   ),
     *   @SWG\Parameter(
     *      name="ethnicity",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by ethnicity. All OR Black OR White Or Coloured Or Indian Or Oriental"
     *   ),
     *   @SWG\Parameter(
     *      name="location",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by location."
     *   ),
     *   @SWG\Parameter(
     *      name="availability",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by availability. All, 1 = Immediately, 2 = Within 1 month,3 = Within 3 month"
     *   ),
     *   @SWG\Parameter(
     *      name="video",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
	 *      description="Sort by video.All or Yes or No"
     *   ),
	 *   @SWG\Parameter(
	 *      name="highestQualification",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="All",
	 *      description="Sort by highestQualification."
	 *   ),
	 *   @SWG\Parameter(
	 *      name="field",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="All",
	 *      description="Sort by field."
	 *   ),
	 *   @SWG\Parameter(
	 *      name="monthSalaryFrom",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="Sort by min salary, 0 or 3500"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="monthSalaryTo",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="Sort by max salary. 3500"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="orderBy",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="candidateID / highestQualification / jobID / firstName / lastName / role / employer / availability / picture / video / mostSalary / salaryPeriod / created / yearsOfWorkExperience / field"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="orderSort",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="ASC / DESC"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="eligibility",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="Sort by eligibility.All or applicable"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="yearsOfWorkExperience",
	 *      in="query",
	 *      required=false,
	 *      type="integer",
	 *      default="",
	 *      description="Sort by yearsOfWorkExperience. 0 = All, 1 = 0, 2 = 0-1, 3 = 1-2, 4 = 3-5, 5 = 5+"
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
     *                      property="candidateID",
     *                      type="integer",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="firstName",
     *                      type="string",
     *                      example="firstName",
     *                  ),
     *                  @SWG\Property(
     *                      property="lastName",
     *                      type="string",
     *                      example="firstName",
     *                  ),
     *                  @SWG\Property(
     *                      property="articlesFirm",
     *                      type="string",
     *                      example="articlesFirm",
     *                  ),
     *                  @SWG\Property(
     *                      property="articlesFirmName",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="dateArticlesCompleted",
     *                      type="datetime"
     *                  ),
     *                  @SWG\Property(
     *                      property="availability",
     *                      type="boolean",
     *                      description="if false see dateAvailability, else immediately",
     *                  ),
     *                  @SWG\Property(
     *                      property="availabilityPeriod",
     *                      type="integer",
     *                      description="
     *                          1=30 Day notice period
     *                          2=60 Day notice period
     *                          3=90 Day notice period
     *                          4=I can provide a specific date"
     *                  ),
     *                  @SWG\Property(
     *                      property="dateAvailability",
     *                      type="date",
     *                      example="2018-09-09",
     *                      description="need check if dateAvailability < now than show immediately",
     *                  ),
     *                  @SWG\Property(
     *                      property="picture",
     *                      type="string",
     *                      description="url on file or NULL"
     *                  ),
     *                  @SWG\Property(
     *                      property="video",
     *                      type="string",
     *                      description="url on file or NULL"
     *                  ),
     *                  @SWG\Property(
     *                      property="cvFiles",
     *                      type="string",
     *                      description="url on file or NULL"
     *                  ),
     *                  @SWG\Property(
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  ),
     *                  @SWG\Property(
     *                      property="employer",
     *                      type="string",
     *                      example="employer",
     *                  ),
     *                  @SWG\Property(
     *                      property="role",
     *                      type="string",
     *                      example="role",
     *                  ),
	 *              	@SWG\Property(
	 *              	    property="mostSalary",
	 *              	    type="integer",
	 *                      example="123123"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="salaryPeriod",
	 *              	    type="string",
	 *                      example="monthly or annual"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="highestQualification",
	 *              	    type="string",
	 *                      example="NQF 8 - Honours"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="yearsOfWorkExperience",
	 *              	    type="integer",
	 *                      example="1"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="field",
	 *              	    type="string",
	 *                      example="Accounting and Finance",
	 *                      description="specialization or NULL"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="applicable",
	 *              	    type="string",
	 *                      example="true or false"
	 *              	)
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
    public function getShortListCandidateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
		$params = $request->query->all();

        $resultShortList = $em->getRepository("AppBundle:Applicants")->getApplicantsByClientWithStatusNew($user->getId(),2,$params);
        $items = $this->generateApplicantsData($em, $resultShortList,$params);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $items,
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
    }// /api/business/applicants/shortList

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Post("/shortList")
     * @SWG\Post(path="/api/business/applicants/shortList",
     *   tags={"Business Applicants"},
     *   security={true},
     *   summary="Added Candidate to Short List",
     *   description="The method for adding Candidate to Short List for business",
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
     *              property="candidateID",
     *              type="integer",
     *              example=1,
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="jobID",
     *              type="integer",
     *              example=1,
     *              description="NOT required"
     *          ),
     *      ),
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success.",
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
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
    public function addShortListCandidateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($request->request->has('candidateID') && !empty($request->request->get('candidateID'))){
            $candidate = $em->getRepository("AppBundle:User")->find($request->request->get('candidateID'));
            if($candidate instanceof User && $candidate->hasRole("ROLE_CANDIDATE") && $user instanceof User){
                if($request->request->has('jobID') && !empty($request->request->get('jobID'))){
                    $job = $em->getRepository("AppBundle:Job")->findOneBy(['user'=>$user,'id'=>$request->request->get('jobID')]);
                    if(!$job instanceof Job){
                        $view = $this->view(['error'=>'job not found'], Response::HTTP_NOT_FOUND);
                        return $this->handleView($view);
                    }
                }
                else{
                    $job = NULL;
                }
                $applicants = $em->getRepository("AppBundle:Applicants")->findOneBy(['client'=>$user, 'candidate'=>$candidate, 'job'=>$job]);
                if(!$applicants instanceof Applicants){
                    $shortListCandidate = new Applicants($user, $candidate, 2, false, $job);
                    $em->persist($shortListCandidate);
                    $em->flush();
                    $view = $this->view([], Response::HTTP_NO_CONTENT);
                }
                else{
                    if($applicants->getStatus() == 1){
                        $applicants->setStatus(2);
                        $em->persist($applicants);
                        $em->flush();
                        $view = $this->view([], Response::HTTP_NO_CONTENT);
                    }
                    elseif($applicants->getStatus() == 2) {
                        $view = $this->view(['error' => 'Candidate already added to ShortList'], Response::HTTP_BAD_REQUEST);
                    }
                    elseif ($applicants->getStatus() == 3){
                        //$view = $this->view(['error' => 'Candidate already approved to interview. You cannot added him/her to ShortList'], Response::HTTP_BAD_REQUEST);
                        $shortListCandidate = new Applicants($user, $candidate, 2, false, null);
                        $em->persist($shortListCandidate);
                        $em->flush();
                        $view = $this->view([], Response::HTTP_NO_CONTENT);
                    }
                    elseif ($applicants->getStatus() == 4){
                        $applicants->setStatus(2);
                        $em->persist($applicants);
                        $em->flush();
                        $view = $this->view([], Response::HTTP_NO_CONTENT);
                    }
                    else{
                        $view = $this->view(['error'=>'Applicants not found'], Response::HTTP_NOT_FOUND);
                    }
                }
            }
            else{
                $view = $this->view(['error'=>'Candidate not found or user not has ROLE_CANDIDATE'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'candidateID is required'], Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Post("/shortList/remove")
     * @SWG\Post(path="/api/business/applicants/shortList/remove",
     *   tags={"Business Applicants"},
     *   security={true},
     *   summary="Remove Candidate to Short List",
     *   description="The method for removing Candidate to Short List for business",
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
     *              property="candidateID",
     *              type="integer",
     *              example=1,
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="jobID",
     *              type="integer",
     *              example=1,
     *              description="NOT required"
     *          ),
     *      ),
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success.",
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
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
    public function removeShortListCandidateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($request->request->has('candidateID') && !empty($request->request->get('candidateID'))){
            $candidate = $em->getRepository("AppBundle:User")->find($request->request->get('candidateID'));
            if($candidate instanceof User && $candidate->hasRole("ROLE_CANDIDATE") && $user instanceof User){
                if($request->request->has('jobID') && !empty($request->request->get('jobID'))){
                    $job = $em->getRepository("AppBundle:Job")->findOneBy(['user'=>$user,'id'=>$request->request->get('jobID')]);
                    if(!$job instanceof Job){
                        $view = $this->view(['error'=>'job not found'], Response::HTTP_NOT_FOUND);
                        return $this->handleView($view);
                    }
                }
                else{
                    $job = NULL;
                }
                $applicants = $em->getRepository("AppBundle:Applicants")->findOneBy(['client'=>$user, 'candidate'=>$candidate, 'job'=>$job, 'status'=>2, 'check'=>false]);
                if($applicants instanceof Applicants){
                    $em->remove($applicants);
                    $em->flush();
                    $view = $this->view([], Response::HTTP_NO_CONTENT);
                }
                else{
                    $view = $this->view(['error'=>'Candidate not added to the ShortList'], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>'Candidate not found or user not has ROLE_CANDIDATE'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'candidateID is required'], Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Get("/approve")
     * @SWG\Get(path="/api/business/applicants/approve",
     *   tags={"Business Applicants"},
     *   security={true},
     *   summary="Get approved Candidates",
     *   description="The method for Getting approved Candidates for business",
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
     *      name="jobID",
     *      in="query",
     *      required=false,
     *      type="integer",
     *      description="jobID"
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
     *      description="search be name"
     *   ),
     *   @SWG\Parameter(
     *      name="gender",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by gender.All or Male or Female"
     *   ),
     *   @SWG\Parameter(
     *      name="ethnicity",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by ethnicity. All OR Black OR White Or Coloured Or Indian Or Oriental"
     *   ),
     *   @SWG\Parameter(
     *      name="location",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by location."
     *   ),
     *   @SWG\Parameter(
     *      name="availability",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by availability. All, 1 = Immediately, 2 = Within 1 month,3 = Within 3 month"
     *   ),
	 *   @SWG\Parameter(
	 *      name="video",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="All",
	 *      description="Sort by video.All or Yes or No"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="highestQualification",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="All",
	 *      description="Sort by highestQualification."
	 *   ),
	 *   @SWG\Parameter(
	 *      name="field",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="All",
	 *      description="Sort by field."
	 *   ),
	 *   @SWG\Parameter(
	 *      name="monthSalaryFrom",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="Sort by min salary, 0 or 3500"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="monthSalaryTo",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="Sort by max salary. 3500"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="orderBy",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="candidateID / highestQualification / jobID / firstName / lastName / role / employer / availability / picture / video / mostSalary / salaryPeriod / created / yearsOfWorkExperience / field"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="orderSort",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="ASC / DESC"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="eligibility",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="Sort by eligibility.All or applicable"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="yearsOfWorkExperience",
	 *      in="query",
	 *      required=false,
	 *      type="integer",
	 *      default="",
	 *      description="Sort by yearsOfWorkExperience.0 = All, 1 = 0, 2 = 0-1, 3 = 1-2, 4 = 3-5, 5 = 5+"
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
     *                      property="candidateID",
     *                      type="integer",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="firstName",
     *                      type="string",
     *                      example="firstName",
     *                  ),
     *                  @SWG\Property(
     *                      property="lastName",
     *                      type="string",
     *                      example="firstName",
     *                  ),
     *                  @SWG\Property(
     *                      property="articlesFirm",
     *                      type="string",
     *                      example="articlesFirm",
     *                  ),
     *                  @SWG\Property(
     *                      property="articlesFirmName",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="dateArticlesCompleted",
     *                      type="datetime"
     *                  ),
     *                  @SWG\Property(
     *                      property="availability",
     *                      type="boolean",
     *                      description="if false see dateAvailability, else immediately",
     *                  ),
     *                  @SWG\Property(
     *                      property="availabilityPeriod",
     *                      type="integer",
     *                      description="
     *                          1=30 Day notice period
     *                          2=60 Day notice period
     *                          3=90 Day notice period
     *                          4=I can provide a specific date"
     *                  ),
     *                  @SWG\Property(
     *                      property="dateAvailability",
     *                      type="date",
     *                      example="2018-09-09",
     *                      description="need check if dateAvailability < now than show immediately",
     *                  ),
     *                  @SWG\Property(
     *                      property="picture",
     *                      type="string",
     *                      description="url on file or NULL"
     *                  ),
     *                  @SWG\Property(
     *                      property="video",
     *                      type="string",
     *                      description="url on file or NULL"
     *                  ),
     *                  @SWG\Property(
     *                      property="cvFiles",
     *                      type="string",
     *                      description="url on file or NULL"
     *                  ),
     *                  @SWG\Property(
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  ),
     *                  @SWG\Property(
     *                      property="employer",
     *                      type="string",
     *                      example="employer",
     *                  ),
     *                  @SWG\Property(
     *                      property="role",
     *                      type="string",
     *                      example="role",
     *                  ),
	 *              	@SWG\Property(
	 *              	    property="mostSalary",
	 *              	    type="integer",
	 *                      example="123123"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="salaryPeriod",
	 *              	    type="string",
	 *                      example="monthly or annual"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="highestQualification",
	 *              	    type="string",
	 *                      example="NQF 8 - Honours"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="yearsOfWorkExperience",
	 *              	    type="integer",
	 *                      example="1"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="field",
	 *              	    type="string",
	 *                      example="Accounting and Finance",
	 *                      description="specialization or NULL"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="applicable",
	 *              	    type="string",
	 *                      example="true or false"
	 *              	)
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
    public function getApprovedCandidateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
		$params = $request->query->all();

        $resultApproved = $em->getRepository("AppBundle:Applicants")->getApplicantsByClientWithStatusNew($user->getId(),3,$params);
        $resultItems = $this->generateApplicantsData($em, $resultApproved,$params);
        $items = [];
        if(!empty($resultItems)){
            $candidateIds = [];
            foreach ($resultItems as $item){
                if(isset($item['candidateID']) && !in_array($item['candidateID'],$candidateIds)){
                    $items[] = $item;
                    $candidateIds[] = $item['candidateID'];
                }
            }
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $items,
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
    }// /api/business/applicants/approve

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     *
     * @Rest\Post("/approve")
     * @SWG\Post(path="/api/business/applicants/approve",
     *   tags={"Business Applicants"},
     *   security={true},
     *   summary="Approve Candidate (Set up Interview)",
     *   description="The method for Approve Candidate for business",
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
     *              property="candidateID",
     *              type="integer",
     *              example=1,
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="jobID",
     *              type="integer",
     *              example=1,
     *              description="NOT required"
     *          ),
     *      ),
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="check",
     *              type="integer",
     *              example=0,
     *              description="1 = add to all and setUp, 2 = add to setUp, 3 = not add"
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
     *          ),
     *          @SWG\Property(
     *              property="error_error_description",
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
    public function approveCandidateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $companyDetails = $em->getRepository("AppBundle:CompanyDetails")->findOneBy(['user'=>$user]);
        if($request->request->has('candidateID') && !empty($request->request->get('candidateID'))){
            $candidate = $em->getRepository("AppBundle:User")->find($request->request->get('candidateID'));
            if($candidate instanceof User && $candidate->hasRole("ROLE_CANDIDATE") && $user instanceof User){
                if($request->request->has('jobID') && !empty($request->request->get('jobID'))){
                    $job = $em->getRepository("AppBundle:Job")->findOneBy(['user'=>$user,'id'=>$request->request->get('jobID')]);
                    if(!$job instanceof Job){
                        $view = $this->view(['error'=>'job not found'], Response::HTTP_NOT_FOUND);
                        return $this->handleView($view);
                    }
                }
                else{
                    $job = NULL;
                }
                $applicants = $em->getRepository("AppBundle:Applicants")->findOneBy(['client'=>$user, 'candidate'=>$candidate, 'job'=>$job]);
                if($applicants instanceof Applicants){
                    if($applicants->getStatus() == 1){
                        $checkSetUpResult = 2;
                        $checkSetUp = $em->getRepository('AppBundle:Applicants')->findOneBy(['client'=>$user,'candidate'=>$candidate,'status'=>3]);
                        if($checkSetUp instanceof Applicants){
                            $checkSetUpResult = 3;
                        }
                        $applicants->setStatus(3);
                        $em->persist($applicants);
                        $em->flush();

                        $checkInterviews = $em->getRepository("AppBundle:Interviews")->findOneBy(['client'=>$user,'candidate'=>$candidate,'job'=>$job]);
                        if(!$checkInterviews instanceof Interviews){
                            $interviews = new Interviews($user, $candidate, 1, true, $job);
                            $em->persist($interviews);
                            $em->flush();

                            $emailData = array(
                                'client' => [
                                    'firstName'=>$applicants->getClient()->getFirstName(),
                                    'lastName'=>$applicants->getClient()->getLastName(),
                                    'email'=>$applicants->getClient()->getEmail(),
                                    'phone'=>$applicants->getClient()->getPhone()
                                ],
                                'candidate' => [
                                    'firstName'=>$applicants->getCandidate()->getFirstName(),
                                    'lastName'=>$applicants->getCandidate()->getLastName(),
                                    'email'=>$applicants->getCandidate()->getEmail(),
                                    'phone'=>$applicants->getCandidate()->getPhone()
                                ],
                                'companyName' => ($companyDetails instanceof CompanyDetails) ? $companyDetails->getName() : '',
                                'jobTitle'=>($job instanceof Job) ? $job->getJobTitle() : '',
                                'link' => $request->getSchemeAndHttpHost().'/admin/all_applicants'
                            );

                            $message = (new \Swift_Message('A client has just requested an interview'))
                                ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                                ->setBody(
                                    $this->renderView('emails/admin/set_up_interview.html.twig',
                                        $emailData
                                    ),
                                    'text/html'
                                );
                            SendEmail::sendEmailForAdmin($em, $message, $this->get('mailer'), $emailData, SendEmail::TYPE_ADMIN_INTERVIEW_SET_UP);
                        }

                        $view = $this->view(['check'=>$checkSetUpResult], Response::HTTP_OK);
                    }
                    elseif($applicants->getStatus() == 3){
                        $view = $this->view(['error' => 'Candidate already approved to interview.'], Response::HTTP_BAD_REQUEST);
                    }
                    else{
                        if($applicants->getCheck() == true){
                            $checkSetUpResult = 2;
                            $checkSetUp = $em->getRepository('AppBundle:Applicants')->findOneBy(['client'=>$user,'candidate'=>$candidate,'status'=>3]);
                            if($checkSetUp instanceof Applicants){
                                $checkSetUpResult = 3;
                            }
                            $applicants->setStatus(3);
                            $em->persist($applicants);
                            $em->flush();
                            $checkInterviews = $em->getRepository("AppBundle:Interviews")->findOneBy(['client'=>$user,'candidate'=>$candidate,'job'=>$job]);
                            if(!$checkInterviews instanceof Interviews){
                                $interviews = new Interviews($user, $candidate, 1, true, $job);
                                $em->persist($interviews);
                                $em->flush();

                                $emailData = array(
                                    'client' => [
                                        'firstName'=>$applicants->getClient()->getFirstName(),
                                        'lastName'=>$applicants->getClient()->getLastName(),
                                        'email'=>$applicants->getClient()->getEmail(),
                                        'phone'=>$applicants->getClient()->getPhone()
                                    ],
                                    'candidate' => [
                                        'firstName'=>$applicants->getCandidate()->getFirstName(),
                                        'lastName'=>$applicants->getCandidate()->getLastName(),
                                        'email'=>$applicants->getCandidate()->getEmail(),
                                        'phone'=>$applicants->getCandidate()->getPhone()
                                    ],
                                    'companyName' => ($companyDetails instanceof CompanyDetails) ? $companyDetails->getName() : '',
                                    'jobTitle'=>($job instanceof Job) ? $job->getJobTitle() : '',
                                    'link' => $request->getSchemeAndHttpHost().'/admin/all_applicants'
                                );

                                $message = (new \Swift_Message('A client has just requested an interview'))
                                    ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                                    ->setBody(
                                        $this->renderView('emails/admin/set_up_interview.html.twig',
                                            $emailData
                                        ),
                                        'text/html'
                                    );
                                SendEmail::sendEmailForAdmin($em, $message, $this->get('mailer'), $emailData, SendEmail::TYPE_ADMIN_INTERVIEW_SET_UP);
                            }

                            $view = $this->view(['check'=>$checkSetUpResult], Response::HTTP_OK);
                        }
                        else{
                            $checkSetUpResult = 2;
                            $checkSetUp = $em->getRepository('AppBundle:Applicants')->findOneBy(['client'=>$user,'candidate'=>$candidate,'status'=>3]);
                            if($checkSetUp instanceof Applicants){
                                $checkSetUpResult = 3;
                            }
                            $applicants->setStatus(3);
                            $em->persist($applicants);
                            $em->flush();
                            $checkInterviews = $em->getRepository("AppBundle:Interviews")->findOneBy(['client'=>$user,'candidate'=>$candidate,'job'=>$job]);
                            if(!$checkInterviews instanceof Interviews){
                                $interviews = new Interviews($user, $candidate, 1, false, $job);
                                $em->persist($interviews);
                                $em->flush();

                                $industry = '';
                                if($job instanceof Job && is_array($job->getIndustry())){
                                    $industry = implode(', ',$job->getIndustry());
                                }
                                elseif ($companyDetails instanceof CompanyDetails && is_array($companyDetails->getIndustry())){
                                    $industry = implode(', ',$companyDetails->getIndustry());
                                }
                                $city = '';
                                if($job instanceof Job && !empty($job->getAddressCity())){
                                    $city = $job->getAddressCity();
                                }
                                elseif ($companyDetails instanceof CompanyDetails && !empty($companyDetails->getAddressCity())){
                                    $city = $companyDetails->getAddressCity();
                                }
                                $emailData = [
                                    'user' => ['firstName'=>$candidate->getFirstName()],
                                    'jse' => ($companyDetails instanceof CompanyDetails && is_bool($companyDetails->getJse())) ? $companyDetails->getJse() : false,
                                    'industry' => $industry,
                                    'jobTitle' => ($job instanceof Job) ? $job->getJobTitle() : "",
                                    'city' => $city
                                ];
                                $message = (new \Swift_Message('Great news – Yes2Work has got you an Interview Request!'))
                                    ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                                    ->setTo($candidate->getEmail())
                                    ->setBody(
                                        $this->renderView('emails/candidate/interview_request.html.twig',
                                            $emailData
                                        ),
                                        'text/html'
                                    );
                                SendEmail::sendEmailForCandidate($candidate, $em, $message, $this->get('mailer'), $emailData, SendEmail::TYPE_CANDIDATE_INTERVIEW_REQUEST);

                                $emailData = array(
                                    'client' => [
                                        'firstName'=>$applicants->getClient()->getFirstName(),
                                        'lastName'=>$applicants->getClient()->getLastName(),
                                        'email'=>$applicants->getClient()->getEmail(),
                                        'phone'=>$applicants->getClient()->getPhone()
                                    ],
                                    'candidate' => [
                                        'firstName'=>$applicants->getCandidate()->getFirstName(),
                                        'lastName'=>$applicants->getCandidate()->getLastName(),
                                        'email'=>$applicants->getCandidate()->getEmail(),
                                        'phone'=>$applicants->getCandidate()->getPhone()
                                    ],
                                    'companyName' => ($companyDetails instanceof CompanyDetails) ? $companyDetails->getName() : '',
                                    'jobTitle'=>($job instanceof Job) ? $job->getJobTitle() : '',
                                    'link' => $request->getSchemeAndHttpHost().'/admin/all_applicants'
                                );

                                $message = (new \Swift_Message('A client has just requested an interview'))
                                    ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                                    ->setBody(
                                        $this->renderView('emails/admin/set_up_interview.html.twig',
                                            $emailData
                                        ),
                                        'text/html'
                                    );
                                SendEmail::sendEmailForAdmin($em, $message, $this->get('mailer'), $emailData, SendEmail::TYPE_ADMIN_INTERVIEW_SET_UP);
                            }

                            $view = $this->view(['check'=>$checkSetUpResult], Response::HTTP_OK);
                        }
                    }
                }
                else{
                    $checkSetUpResult = 1;
                    $checkAll = $em->getRepository('AppBundle:Applicants')->findOneBy(['client'=>$user,'candidate'=>$candidate]);
                    if($checkAll instanceof Applicants){
                        $checkSetUpResult = 2;
                    }
                    $checkSetUp = $em->getRepository('AppBundle:Applicants')->findOneBy(['client'=>$user,'candidate'=>$candidate,'status'=>3]);
                    if($checkSetUp instanceof Applicants){
                        $checkSetUpResult = 3;
                    }
                    $applicants = new Applicants($user, $candidate, 3, false, $job);
                    $em->persist($applicants);
                    $em->flush();

                    $checkInterviews = $em->getRepository("AppBundle:Interviews")->findOneBy(['client'=>$user,'candidate'=>$candidate,'job'=>$job]);
                    if(!$checkInterviews instanceof Interviews){
                        $interviews = new Interviews($user, $candidate, 1, false, $job);
                        $em->persist($interviews);
                        $em->flush();

                        $industry = '';
                        if($job instanceof Job && is_array($job->getIndustry())){
                            $industry = implode(', ',$job->getIndustry());
                        }
                        elseif ($companyDetails instanceof CompanyDetails && is_array($companyDetails->getIndustry())){
                            $industry = implode(', ',$companyDetails->getIndustry());
                        }
                        $city = '';
                        if($job instanceof Job && !empty($job->getAddressCity())){
                            $city = $job->getAddressCity();
                        }
                        elseif ($companyDetails instanceof CompanyDetails && !empty($companyDetails->getAddressCity())){
                            $city = $companyDetails->getAddressCity();
                        }
                        $emailData = [
                            'user' => ['firstName'=>$candidate->getFirstName()],
                            'jse' => ($companyDetails instanceof CompanyDetails && is_bool($companyDetails->getJse())) ? $companyDetails->getJse() : false,
                            'industry' => $industry,
                            'jobTitle' => ($job instanceof Job) ? $job->getJobTitle() : "",
                            'city' => $city
                        ];
                        $message = (new \Swift_Message('Great news – Yes2Work has got you an Interview Request!'))
                            ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                            ->setTo($candidate->getEmail())
                            ->setBody(
                                $this->renderView('emails/candidate/interview_request.html.twig',
                                    $emailData
                                ),
                                'text/html'
                            );
                        SendEmail::sendEmailForCandidate($candidate, $em, $message, $this->get('mailer'), $emailData, SendEmail::TYPE_CANDIDATE_INTERVIEW_REQUEST);

                        $emailData = array(
                            'client' => [
                                'firstName'=>$applicants->getClient()->getFirstName(),
                                'lastName'=>$applicants->getClient()->getLastName(),
                                'email'=>$applicants->getClient()->getEmail(),
                                'phone'=>$applicants->getClient()->getPhone()
                            ],
                            'candidate' => [
                                'firstName'=>$applicants->getCandidate()->getFirstName(),
                                'lastName'=>$applicants->getCandidate()->getLastName(),
                                'email'=>$applicants->getCandidate()->getEmail(),
                                'phone'=>$applicants->getCandidate()->getPhone()
                            ],
                            'companyName' => ($companyDetails instanceof CompanyDetails) ? $companyDetails->getName() : '',
                            'jobTitle'=>($job instanceof Job) ? $job->getJobTitle() : '',
                            'link' => $request->getSchemeAndHttpHost().'/admin/all_applicants'
                        );

                        $message = (new \Swift_Message('A client has just requested an interview'))
                            ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                            ->setBody(
                                $this->renderView('emails/admin/set_up_interview.html.twig',
                                    $emailData
                                ),
                                'text/html'
                            );
                        SendEmail::sendEmailForAdmin($em, $message, $this->get('mailer'), $emailData, SendEmail::TYPE_ADMIN_INTERVIEW_SET_UP);
                    }

                    $view = $this->view(['check'=>$checkSetUpResult], Response::HTTP_OK);
                }

            }
            else{
                $view = $this->view(['error'=>'Candidate not found or user not have ROLE_CANDIDATE'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'candidateID is required'], Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Get("/decline")
     * @SWG\Get(path="/api/business/applicants/decline",
     *   tags={"Business Applicants"},
     *   security={true},
     *   summary="Get declined Candidates",
     *   description="The method for Getting declined Candidates for business",
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
     *      name="jobID",
     *      in="query",
     *      required=false,
     *      type="integer",
     *      description="jobID"
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
     *      description="search be name"
     *   ),
     *   @SWG\Parameter(
     *      name="gender",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by gender.All or Male or Female"
     *   ),
     *   @SWG\Parameter(
     *      name="ethnicity",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by ethnicity. All OR Black OR White Or Coloured Or Indian Or Oriental"
     *   ),
     *   @SWG\Parameter(
     *      name="location",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by location."
     *   ),
     *   @SWG\Parameter(
     *      name="availability",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by availability. All, 1 = Immediately, 2 = Within 1 month,3 = Within 3 month"
     *   ),
	 *   @SWG\Parameter(
	 *      name="video",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="All",
	 *      description="Sort by video.All or Yes or No"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="highestQualification",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="All",
	 *      description="Sort by highestQualification."
	 *   ),
	 *   @SWG\Parameter(
	 *      name="field",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="All",
	 *      description="Sort by field."
	 *   ),
	 *   @SWG\Parameter(
	 *      name="monthSalaryFrom",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="Sort by min salary, 0 or 3500"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="monthSalaryTo",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="Sort by max salary. 3500"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="orderBy",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="candidateID / highestQualification / jobID / firstName / lastName / role / employer / availability / picture / video / mostSalary / salaryPeriod / created / yearsOfWorkExperience / field"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="orderSort",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="ASC / DESC"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="eligibility",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="Sort by eligibility.All or applicable"
	 *   ),
	 *   @SWG\Parameter(
	 *      name="yearsOfWorkExperience",
	 *      in="query",
	 *      required=false,
	 *      type="integer",
	 *      default="",
	 *      description="Sort by yearsOfWorkExperience. 0 = All, 1 = 0, 2 = 0-1, 3 = 1-2, 4 = 3-5, 5 = 5+"
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
     *                      property="candidateID",
     *                      type="integer",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobID",
     *                      type="integer",
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle",
     *                  ),
     *                  @SWG\Property(
     *                      property="firstName",
     *                      type="string",
     *                      example="firstName",
     *                  ),
     *                  @SWG\Property(
     *                      property="lastName",
     *                      type="string",
     *                      example="firstName",
     *                  ),
     *                  @SWG\Property(
     *                      property="articlesFirm",
     *                      type="string",
     *                      example="articlesFirm",
     *                  ),
     *                  @SWG\Property(
     *                      property="articlesFirmName",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="dateArticlesCompleted",
     *                      type="datetime"
     *                  ),
     *                  @SWG\Property(
     *                      property="availability",
     *                      type="boolean",
     *                      description="if false see dateAvailability, else immediately",
     *                  ),
     *                  @SWG\Property(
     *                      property="availabilityPeriod",
     *                      type="integer",
     *                      description="
     *                          1=30 Day notice period
     *                          2=60 Day notice period
     *                          3=90 Day notice period
     *                          4=I can provide a specific date"
     *                  ),
     *                  @SWG\Property(
     *                      property="dateAvailability",
     *                      type="date",
     *                      example="2018-09-09",
     *                      description="need check if dateAvailability < now than show immediately",
     *                  ),
     *                  @SWG\Property(
     *                      property="picture",
     *                      type="string",
     *                      description="url on file or NULL"
     *                  ),
     *                  @SWG\Property(
     *                      property="video",
     *                      type="string",
     *                      description="url on file or NULL"
     *                  ),
     *                  @SWG\Property(
     *                      property="cvFiles",
     *                      type="string",
     *                      description="url on file or NULL"
     *                  ),
     *                  @SWG\Property(
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09",
     *                  ),
     *                  @SWG\Property(
     *                      property="employer",
     *                      type="string",
     *                      example="employer",
     *                  ),
     *                  @SWG\Property(
     *                      property="role",
     *                      type="string",
     *                      example="role",
     *                  ),
	 *              	@SWG\Property(
	 *              	    property="mostSalary",
	 *              	    type="integer",
	 *                      example="123123"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="salaryPeriod",
	 *              	    type="string",
	 *                      example="monthly or annual"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="highestQualification",
	 *              	    type="string",
	 *                      example="NQF 8 - Honours"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="yearsOfWorkExperience",
	 *              	    type="integer",
	 *                      example="1"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="field",
	 *              	    type="string",
	 *                      example="Accounting and Finance",
	 *                      description="specialization or NULL"
	 *              	),
	 *              	@SWG\Property(
	 *              	    property="applicable",
	 *              	    type="string",
	 *                      example="true or false"
	 *              	)
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
    public function getDeclinedCandidateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $params = $request->query->all();

        $resultDeclined = $em->getRepository("AppBundle:Applicants")->getApplicantsByClientWithStatusNew($user->getId(),4,$params);
        $items = $this->generateApplicantsData($em, $resultDeclined,$params);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $items,
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
    }// /api/business/applicants/decline

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     *
     * @Rest\Post("/decline")
     * @SWG\Post(path="/api/business/applicants/decline",
     *   tags={"Business Applicants"},
     *   security={true},
     *   summary="Decline Candidate",
     *   description="The method for Decline Candidate  for business",
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
     *              property="candidateID",
     *              type="integer",
     *              example=1,
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="jobID",
     *              type="integer",
     *              example=1,
     *              description="NOT required"
     *          ),
     *      ),
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success.",
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
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
    public function declineCandidateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($request->request->has('candidateID') && !empty($request->request->get('candidateID'))){
            $candidate = $em->getRepository("AppBundle:User")->find($request->request->get('candidateID'));
            if($candidate instanceof User && $candidate->hasRole("ROLE_CANDIDATE") && $user instanceof User){
                if($request->request->has('jobID') && !empty($request->request->get('jobID'))){
                    $job = $em->getRepository("AppBundle:Job")->findOneBy(['user'=>$user,'id'=>$request->request->get('jobID')]);
                    if(!$job instanceof Job){
                        $view = $this->view(['error'=>'job not found'], Response::HTTP_NOT_FOUND);
                        return $this->handleView($view);
                    }
                }
                else{
                    $job = NULL;
                }
                $applicants = $em->getRepository("AppBundle:Applicants")->findOneBy(['client'=>$user, 'candidate'=>$candidate, 'job'=>$job]);
                if(!$applicants instanceof Applicants){
                    $declineCandidate = new Applicants($user, $candidate, 4, false, $job);
                    $em->persist($declineCandidate);
                    $em->flush();
                    $view = $this->view([], Response::HTTP_NO_CONTENT);
                }
                else{
                    if($applicants->getStatus() == 1 || $applicants->getStatus() == 2){
                        $applicants->setStatus(4);
                        $em->persist($applicants);
                        $em->flush();

                        if($applicants->getCheck() == true){
                            $companyDetails = $em->getRepository("AppBundle:CompanyDetails")->findOneBy(['user'=>$user]);
                            $emailData = [
                                'user' => ['firstName'=>$candidate->getFirstName()],
                                'jse' => ($companyDetails instanceof CompanyDetails && is_bool($companyDetails->getJse())) ? $companyDetails->getJse() : false,
                                'industry' => ($job instanceof Job && is_array($job->getIndustry())) ? implode(', ',$job->getIndustry()) : "",
                                'jobTitle'=>($job instanceof Job) ? $job->getJobTitle() : "",
                                'city'=>($job instanceof Job) ? $job->getAddressCity() : "",
                                'link'=>$request->getSchemeAndHttpHost().'/candidate/declined_applications'
                            ];
                            $message = (new \Swift_Message('An application on Yes2Work has been declined by the Employer'))
                                ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                                ->setTo($candidate->getEmail())
                                ->setBody(
                                    $this->renderView('emails/candidate/application_decline.html.twig',
                                        $emailData
                                    ),
                                    'text/html'
                                );

                            SendEmail::sendEmailForCandidate($candidate, $em, $message, $this->get('mailer'), $emailData, SendEmail::TYPE_CANDIDATE_APPLICATION_DECLINE);

                        }

                        $view = $this->view([], Response::HTTP_NO_CONTENT);
                    }
                    elseif ($applicants->getStatus() == 3){
                        $view = $this->view(['error' => 'Candidate already approved to interview. You cannot declined him/her'], Response::HTTP_BAD_REQUEST);
                    }
                    elseif ($applicants->getStatus() == 4){
                        $view = $this->view(['error' => 'Candidate already was declined'], Response::HTTP_BAD_REQUEST);
                    }
                    else{
                        $view = $this->view(['error'=>'Applicants not found'], Response::HTTP_NOT_FOUND);
                    }
                }
            }
            else{
                $view = $this->view(['error'=>'Candidate not found or user not has ROLE_CANDIDATE'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'candidateID is required'], Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Post("/cancel")
     * @SWG\Post(path="/api/business/applicants/cancel",
     *   tags={"Business Applicants"},
     *   security={true},
     *   summary="Cancel Application",
     *   description="The method for Cancel Application  for business",
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
     *              property="candidateID",
     *              type="integer",
     *              example=1,
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="jobID",
     *              type="integer",
     *              example=1,
     *              description="NOT required"
     *          ),
     *      ),
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success.",
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
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
    public function cancelApplicationCandidateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($request->request->has('candidateID') && !empty($request->request->get('candidateID'))){
            $candidate = $em->getRepository("AppBundle:User")->find($request->request->get('candidateID'));
            if($candidate instanceof User && $candidate->hasRole("ROLE_CANDIDATE") && $user instanceof User){
                if($request->request->has('jobID') && !empty($request->request->get('jobID'))){
                    $job = $em->getRepository("AppBundle:Job")->findOneBy(['user'=>$user,'id'=>$request->request->get('jobID')]);
                    if(!$job instanceof Job){
                        $view = $this->view(['error'=>'job not found'], Response::HTTP_NOT_FOUND);
                        return $this->handleView($view);
                    }
                }
                else{
                    $job = NULL;
                }
                $opportunities = $em->getRepository("AppBundle:Opportunities")->findOneBy(['client'=>$user, 'candidate'=>$candidate,'job'=>$job, 'status'=>1]);
                if($opportunities instanceof Opportunities){
                    $em->remove($opportunities);
                    $em->flush();
                    $view = $this->view([], Response::HTTP_NO_CONTENT);
                }
                else{
                    $view = $this->view(['error'=>'Interview not awaits the candidate’s approval'], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>'Candidate not found or user not has ROLE_CANDIDATE'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'candidateID is required'], Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * @param EntityManager $em
     * @param array $result
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function generateApplicantsData(EntityManager $em, array $result, array $params = array()){
        $applicants = [];
        $settings = $em->getRepository('AppBundle:Settings')->findOneBy([]);
        if(!$settings instanceof Settings){
            $settings = new Settings(false);
            $em->persist($settings);
            $em->flush();
        }
        if(!empty($result)){

			//	highestQualification - filter
			if(isset($params['highestQualification']) && $params['highestQualification'] != 'null' && $params['highestQualification'] != NULL && $params['highestQualification'] != 'All'){
				if(is_array($params['highestQualification'])){
					$highestQualification = $params['highestQualification'];
				}
				else{
					$highestQualification = explode(',',$params['highestQualification']);
				}
				if(in_array('NQF 4 - Matric',$highestQualification)){
					$highestQualification = array('NQF 4 - Matric');
				}
			}else{
				$highestQualification = array();
			}

			if(isset($params['yearsOfWorkExperience']) && $params['yearsOfWorkExperience'] != 'null' && $params['yearsOfWorkExperience'] != NULL && $params['yearsOfWorkExperience'] != 0){
				if(is_array($params['yearsOfWorkExperience'])){
					$yearsOfWorkExperience = $params['yearsOfWorkExperience'];
				}
				else{
					$yearsOfWorkExperience = explode(',',$params['yearsOfWorkExperience']);
				}
			}else{
				$yearsOfWorkExperience = array();
			}

            $applicants_ids = [];
            foreach ($result as $key=>$applicant){
                if(isset($applicant['jobID']) && !empty($applicant['jobID']) && array_key_exists($applicant['jobID'], $applicants_ids) && in_array($applicant['candidateID'],$applicants_ids[$applicant['jobID']])){
                    continue;
                }
                $applicants_ids[$applicant['jobID']][] = $applicant['candidateID'];
				$cvFiles = [];
                if(!empty($applicant['copyOfID'])){
                    foreach ($applicant['copyOfID'] as $cvFile){
                        if(isset($cvFile['approved']) && $cvFile['approved'] == true){
                            $cvFiles[] = $cvFile;
                        }
                    }
                }
                if(!empty($cvFiles)){
                    //$applicant['cvFiles'] = [];
                    $jobTitle = null;
                    if(!empty($applicant['jobID'])){
                        $job = $em->getRepository("AppBundle:Job")->find($applicant['jobID']);
                        if($job instanceof Job){
                            $jobTitle = $job->getJobTitle();
                        }
                    }
                    $applicant['jobTitle'] = $jobTitle;
                    $applicant['picture'] = (isset($applicant['picture'][0]['url']) && !empty($applicant['picture'][0]['url'])) ? $applicant['picture'][0]['url'] : NULL;
                    $applicant['video'] = (isset($applicant['video']['adminUrl']) && !empty($applicant['video']['adminUrl']) && isset($applicant['video']['approved']) && $applicant['video']['approved'] == true) ? $applicant['video']['adminUrl'] : NULL;
                    $applicant['applicable'] = (isset($applicant['ethnicity']) && $applicant['ethnicity'] !== 'White' && $applicant['ethnicity'] !== 'Foreign National')? true : false;

                    $applicant['yearsOfWorkExperience'] = $this->getYearsOfWorkExperienceForCandidate($em,$applicant['candidateID']);
                    $applicant['highestQualification'] = $this->getHighestQualificationForCandidate($em,$applicant['candidateID']);


                    $filter_ok = true;
                    if(!empty($highestQualification)){
                        if(!in_array($applicant['highestQualification'],$highestQualification)){
                            $filter_ok = false;
                        }
                    }
                    if(!empty($yearsOfWorkExperience) && $filter_ok == true){
                        $filter_ok = false;
                        /* $yearsOfWorkExperience
                        0 All
                        1 0
                        2 0-1
                        3 1-2
                        4 3-5
                        5 5+
                        */
                        if(in_array(1,$yearsOfWorkExperience) && $applicant['yearsOfWorkExperience'] == 0){
                            $filter_ok = true;
                        }elseif(in_array(2,$yearsOfWorkExperience) && ($applicant['yearsOfWorkExperience'] == 0 || $applicant['yearsOfWorkExperience'] == 1)){
                            $filter_ok = true;
                        }elseif(in_array(3,$yearsOfWorkExperience) && ($applicant['yearsOfWorkExperience'] == 1 || $applicant['yearsOfWorkExperience'] == 2)){
                            $filter_ok = true;
                        }elseif(in_array(4,$yearsOfWorkExperience) && ($applicant['yearsOfWorkExperience'] > 2 && $applicant['yearsOfWorkExperience'] < 6)){
                            $filter_ok = true;
                        }elseif(in_array(5,$yearsOfWorkExperience) && $applicant['yearsOfWorkExperience'] > 5){
                            $filter_ok = true;
                        }
                    }
                    if($filter_ok === true){
                        $applicants[] = $applicant;
                    }
                }

            }
			if(isset($params['orderBy']) && $params['orderBy'] == 'highestQualification' && count($applicants) > 1){
				if(isset($params['orderSort']) && $params['orderSort'] == 'DESC'){
					$applicants = $this->SortByArrayKey($applicants, 'highestQualification', SORT_DESC);
				}else{
					$applicants = $this->SortByArrayKey($applicants, 'highestQualification', SORT_ASC);
				}
			}
			if(isset($params['orderBy']) && $params['orderBy'] == 'yearsOfWorkExperience' && count($applicants) > 1){
				if(isset($params['orderSort']) && $params['orderSort'] == 'DESC'){
					$applicants = $this->SortByArrayKey($applicants,'yearsOfWorkExperience', SORT_DESC);
				}else{
					$applicants = $this->SortByArrayKey($applicants,'yearsOfWorkExperience', SORT_ASC);
				}
			}
			if(isset($params['orderBy']) && $params['orderBy'] == 'applicable' && count($applicants) > 1){
				if(isset($params['orderSort']) && $params['orderSort'] == 'DESC'){
					$applicants = $this->SortByArrayKey($applicants,'applicable', SORT_DESC);
				}else{
					$applicants = $this->SortByArrayKey($applicants,'applicable', SORT_ASC);
				}
			}

        }
        return $applicants;
    }

	/**
	 * @param EntityManager $em
	 * @param string $candidateID
	 * @return string
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	private function getHighestQualificationForCandidate(EntityManager $em, $candidateID){
		$highest_qualif = '';
		$highest_num = 0;
		$highest_type = 0;
		if (!empty($candidateID)) {
			$candidate_qualifications = $em->getRepository("AppBundle:CandidateQualifications")->getQualificationsCandidate($candidateID);
			if (!empty($candidate_qualifications)) {
				foreach ($candidate_qualifications as $qualif) {
					if (isset($qualif['levelQ']) && $qualif['type'] == 3 && $highest_type <= 3) {
						$qualif_lvl = (int)filter_var($qualif['levelQ'], FILTER_SANITIZE_NUMBER_INT);
						if($qualif_lvl > $highest_num) {
							$highest_num = $qualif_lvl;
							$highest_qualif = $qualif['levelQ'];
							$highest_type = 3;
						}
					}else if(isset($qualif['type']) && $qualif['type'] == 2 && $highest_type <= 2){
						$highest_qualif = 'NQF 2 - Grade 10';
						$highest_type = 2;
					}else if(isset($qualif['type']) && $qualif['type'] == 1 && $highest_type <= 1){
						$highest_qualif = 'NQF 4 - Matric';
						$highest_type = 1;
					}
				}
			}
		}
		return $highest_qualif;
	}

	/**
	 * @param EntityManager $em
	 * @param string $candidateID
	 * @return string
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	private function getYearsOfWorkExperienceForCandidate(EntityManager $em, $candidateID){
		$years = 0;
		$exp_in_sec = 0;
		if (!empty($candidateID)) {
			$candidate_qualifications = $em->getRepository("AppBundle:CandidateReferences")->findBy(["user" => $candidateID]);
			if(count($candidate_qualifications) > 0) {
				foreach ($candidate_qualifications as $post) {
					$start_date = $post->getStartDate()->format('Y-m-d H:i:s');
					$end_date = $post->getEndDate()->format('Y-m-d H:i:s');
					$unix_start_date = strtotime($start_date);
					$unix_end_date = strtotime($end_date);
					$exp_in_sec += $unix_end_date - $unix_start_date;
				}
				$years = round($exp_in_sec / (60 * 60 * 24 * 365));
			}
		}
		return $years;
	}

	/**
	 * @param array $records
	 * @param string $records
	 * @param bool $records
	 * @return array
	 */
	private function SortByArrayKey()
	{
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
	}

}
