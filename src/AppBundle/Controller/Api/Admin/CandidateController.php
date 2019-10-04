<?php
/**
 * Created by PhpStorm.
 * Date: 20.04.18
 * Time: 17:09
 */

namespace AppBundle\Controller\Api\Admin;


use AppBundle\Entity\Job;
use AppBundle\Entity\Logging;
use AppBundle\Entity\NotificationCandidate;
use AppBundle\Entity\ProfileDetails;
use AppBundle\Entity\User;
use AppBundle\Helper\HelpersClass;
use AppBundle\Helper\SendSMSApp;
use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class CandidateController
 * @package AppBundle\Controller\Api\Admin
 *
 * @Rest\Route("candidate")
 * @Security("has_role('ROLE_ADMIN')")
 */
class CandidateController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     * @throws
     *
     * @Rest\Get("/")
     * @SWG\Get(path="/api/admin/candidate/",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="Get All Candidate",
     *   description="The method for getting all Candidate for admin",
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
     *      description="find by firstName or lastName or email or phone"
     *   ),
     *   @SWG\Parameter(
     *      name="orderBy",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="Name, Email, Phone"
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
	 *      name="eligibility",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="",
	 *      description="Sort by eligibility.All or applicable"
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
	 *      name="gender",
	 *      in="query",
	 *      required=false,
	 *      type="string",
	 *      default="All",
	 *      description="Sort by gender.All or Male or Female"
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
	 *      name="yearsOfWorkExperience",
	 *      in="query",
	 *      required=false,
	 *      type="integer",
	 *      default="",
	 *      description="Sort by yearsOfWorkExperience. 0 = All, 1 = 0, 2 = 0-1, 3 = 1-2, 4 = 3-5, 5 = 5+"
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
     *      name="enabled",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="search by status"
     *   ),
     *   @SWG\Parameter(
     *      name="profileComplete",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="search by profileComplete"
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
     *                      property="profile",
     *                      type="object",
     *                      @SWG\Property(
     *                          property="id",
     *                          type="integer"
     *                      ),
     *                      @SWG\Property(
     *                          property="firstName",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="lastName",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="phone",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="agentName",
     *                          type="string"
     *                      ),
     *                      @SWG\Property(
     *                          property="enabled",
     *                          type="boolean"
     *                      ),
     *                      @SWG\Property(
     *                          property="approved",
     *                          type="boolean"
     *                      ),
     *                      @SWG\Property(
     *                          property="percentage",
     *                          type="integer",
     *                          description="if > 50 than Yes else NO"
     *                      ),
     *                      @SWG\Property(
     *                          property="cvFiles",
     *                          type="array",
     *                          @SWG\Items(
     *                              type="object",
     *                              @SWG\Property(
     *                                  property="url",
     *                                  type="string"
     *                              ),
     *                              @SWG\Property(
     *                                  property="name",
     *                                  type="string"
     *                              ),
     *                              @SWG\Property(
     *                                  property="size",
     *                                  type="integer"
     *                              ),
     *                              @SWG\Property(
     *                                  property="approved",
     *                                  type="boolean"
     *                              ),
     *                          )
     *                      ),
     *                      @SWG\Property(
     *                          property="video",
     *                          type="object",
     *                          @SWG\Property(
     *                              property="url",
     *                              type="string"
     *                          ),
     *                          @SWG\Property(
     *                              property="name",
     *                              type="string"
     *                          ),
     *                          @SWG\Property(
     *                              property="approved",
     *                              type="boolean"
     *                          ),
     *                      ),
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
    public function getAllCandidateAction(Request $request){
		$params = $request->query->all();
        $em = $this->getDoctrine()->getManager();
        $candidates = $em->getRepository("AppBundle:User")->getAllCandidateNew($params);
		$candidates = $this->filtering_candidates($em, $candidates, $params);
        if($request->query->getBoolean('csv', false) == false){
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $candidates,
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
            if(!empty($candidates)){
                foreach ($candidates as $candidate){

                    $name = '';
                    if(isset($candidate['firstName'])){
                        $name .= $candidate['firstName'].' ';
                    }
                    if(isset($client['lastName'])){
                        $name .= $candidate['lastName'];
                    }
                    $percentage = 'No';
                    if(isset($candidate['percentage']) && $candidate['percentage'] > 50
                        && isset($candidate['video']) && !empty($candidate['video'])
                        && isset($candidate['copyOfID']) && !empty($candidate['copyOfID'])
                    ){
                        $percentage = 'Yes';
                    }
                    $candidateUser = $em->getRepository("AppBundle:User")->find($candidate['id']);
                    $sms = 'No';
                    if($candidateUser instanceof User){
                        $candidateNotification = $em->getRepository("AppBundle:NotificationCandidate")->findOneBy(['user'=>$candidateUser]);
                        if($candidateNotification instanceof  NotificationCandidate){
                            if($candidateNotification->getNotifySMS() == true){
                                $sms = 'Yes';
                            }
                        }
                    }
                    $result[] = [
                        'name' => $name,
                        'email' => (isset($candidate['email'])) ? $candidate['email'] : '',
                        'phone' => (isset($candidate['phone'])) ? $candidate['phone'] : '',
                        'percentage' => $percentage,
                        'sms' => $sms,
                        'agentName' => (isset($candidate['agentName'])) ? $candidate['agentName'] : '',
                        'active' => (isset($candidate['enabled'])) ? $candidate['enabled'] : '',
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
     * @throws
     *
     * @Rest\Get("/count")
     * @SWG\Get(path="/api/admin/candidate/count",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="Get All count Candidate",
     *   description="The method for getting all count Candidate for admin",
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
     *      description="find by firstName or lastName or email or phone"
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
     *      name="ethnicity",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by ethnicity. All OR Black OR White Or Coloured Or Indian Or Oriental"
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
     *      name="location",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by location."
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
     *      name="yearsOfWorkExperience",
     *      in="query",
     *      required=false,
     *      type="integer",
     *      default="",
     *      description="Sort by yearsOfWorkExperience. 0 = All, 1 = 0, 2 = 0-1, 3 = 1-2, 4 = 3-5, 5 = 5+"
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
     *      name="enabled",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="search by status"
     *   ),
     *   @SWG\Parameter(
     *      name="profileComplete",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="search by profileComplete"
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              type="integer",
     *              property="candidateCount"
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
    public function getAllCountCandidateAction(Request $request){
        $params = $request->query->all();
        $em = $this->getDoctrine()->getManager();
        $candidates = $em->getRepository("AppBundle:User")->getAllCandidateNew($params);
        $candidates = $this->filtering_candidates($em, $candidates, $params);

        $view = $this->view([
            'candidateCount' => count($candidates)
        ], Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Post("/")
     * @SWG\Post(path="/api/admin/candidate/",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="Create Candidate for Admin",
     *   description="The method for CREATE Candidate for Admin",
     *   produces={"application/json"},
     *   consumes={"application/json"},
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
     *      default="multipart/form-data",
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
     *                  property="phone",
     *                  type="string",
     *                  example="123213123",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="email",
     *                  type="string",
     *                  example="email@gmail.com",
     *                  description="required"
     *              ),
     *          ),
     *          @SWG\Property(
     *              property="profile",
     *              type="object",
     *              @SWG\Property(
	 *                  property="idNumber",
	 *                  type="string",
	 *                  example="idNumber",
	 *              ),
	 *              @SWG\Property(
	 *                  property="ethnicity",
	 *                  type="string",
	 *                  example="ethnicity",
	 *              ),
     *              @SWG\Property(
     *                  property="beeCheck",
     *                  type="datetime",
     *                  example="2018-09-09"
     *              ),
	 *              @SWG\Property(
	 *                  property="gender",
	 *                  type="string",
	 *                  example="gender",
	 *              ),
	 *              @SWG\Property(
	 *                  property="dateOfBirth",
	 *                  type="date",
	 *                  example="2018-05-16",
	 *              ),
	 *              @SWG\Property(
	 *                  property="criminal",
	 *                  type="boolean",
	 *                  example="false",
	 *              ),
	 *              @SWG\Property(
	 *                  property="criminalDescription",
	 *                  type="string",
	 *                  example="criminalDescription"
	 *              ),
	 *              @SWG\Property(
	 *                  property="homeAddress",
	 *                  type="string",
	 *                  example="homeAddress",
	 *              ),
     *              @SWG\Property(
     *                  property="driverLicense",
     *                  type="boolean",
     *              ),
     *              @SWG\Property(
     *                  property="driverNumber",
     *                  type="string",
     *              ),
     *              @SWG\Property(
     *                  property="englishProficiency",
     *                  type="integer",
     *                  description="1 = Below Average 2 = Average 3 = Good 4 = Exceptional"
     *              ),
	 *              @SWG\Property(
	 *                  property="availability",
	 *                  type="boolean",
	 *                  example="false",
	 *              ),
	 *              @SWG\Property(
	 *                  property="dateAvailability",
	 *                  type="date",
	 *                  example="2018-05-16",
	 *                  description="Required if availability=true"
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
	 *                  property="citiesWorking",
	 *                  type="string",
	 *                  example="city1,city2"
	 *              ),
     *              @SWG\Property(
     *                  property="copyOfID",
     *                  type="array",
     *                  @SWG\Items(type="file"),
     *                  example={"file1","file2"}
     *              ),
     *              @SWG\Property(
     *                  property="cv",
     *                  type="array",
     *                  @SWG\Items(type="file"),
     *                  example={"file1","file2"}
     *              ),
     *              @SWG\Property(
     *                  property="matricCertificate",
     *                  type="array",
     *                  @SWG\Items(type="file"),
     *                  example={"file1","file2"}
     *              ),
     *              @SWG\Property(
     *                  property="matricTranscript",
     *                  type="array",
     *                  @SWG\Items(type="file"),
     *                  example={"file1","file2"}
     *              ),
     *              @SWG\Property(
     *                  property="certificateOfQualification",
     *                  type="array",
     *                  @SWG\Items(type="file"),
     *                  example={"file1","file2"}
     *              ),
     *              @SWG\Property(
     *                  property="academicTranscript",
     *                  type="array",
     *                  @SWG\Items(type="file"),
     *                  example={"file1","file2"}
     *              ),
     *              @SWG\Property(
     *                  property="payslip",
     *                  type="array",
     *                  @SWG\Items(type="file"),
     *                  example={"file1","file2"}
     *              ),
     *              @SWG\Property(
     *                  property="picture",
     *                  type="file",
     *                  example="picture1.jpg"
     *              ),
     *              @SWG\Property(
     *                  property="video",
     *                  type="file",
     *                  example="file.mp4"
     *              ),
     *          ),
     *      ),
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success. Candidate Create"
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
     *   )
     * )
     */
    public function createCandidateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        if($request->request->has('user') && !empty(($request->request->get('user')))){
            $userData = $request->request->get('user');
            if(isset($userData['firstName']) && isset($userData['lastName']) && isset($userData['email']) && isset($userData['phone'])){
                $user = new User();
                $password = substr(md5(time()),0,6);
                $user->setRegisterDetails("ROLE_CANDIDATE", $userData['firstName'], $userData['lastName'], $userData['email'], $userData['phone'], $password);
                $user->setEnabled(true);
                $user->setApproved(true);
                $errors = $this->get('validator')->validate($user, null, array('registerCandidate'));
                if(count($errors) === 0){
                    $em->persist($user);
                    if($request->request->has('profile') && !empty($request->request->get('profile'))){
                        $dataProfile = $request->request->get('profile');
                        if(isset($dataProfile['idNumber']) && !empty($dataProfile['idNumber'])) {
                            $profileDetails = new ProfileDetails($user, $dataProfile['idNumber']);
                            $infoIdNumber = HelpersClass::isValidIDNumber($profileDetails->getIdNumber());
                            if($infoIdNumber){
                                if(array_key_exists('nationality', $infoIdNumber) && in_array($infoIdNumber['nationality'], [1,2])){
                                    $profileDetails->setNationality($infoIdNumber['nationality']);
                                }
                                $profileDetails->updateForm($dataProfile);
                                $errorsDetails = $this->get('validator')->validate($profileDetails, null, array('updateDetails'));
                                if(count($errorsDetails) === 0){
                                    $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                                    //$profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                                    $em->persist($profileDetails);
                                    $em->flush();
                                    $notificationCandidate = new NotificationCandidate($user);
                                    $em->persist($notificationCandidate);
                                    $em->flush();
                                    $message = (new \Swift_Message('Welcome to Yes2Work!'))
                                        ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                                        ->setTo($user->getEmail())
                                        ->setBody(
                                            $this->renderView('emails/user/candidate_registered.html.twig', [
                                                'user' => $user,
                                                'password' => $password,
                                                'link' => $request->getSchemeAndHttpHost()
                                            ]),
                                            'text/html'
                                        );
                                    try{
                                        $this->get('mailer')->send($message);
                                    }catch(\Swift_TransportException $e){}

                                    $files = [];
                                    if(!empty($request->files->all())){
                                        foreach ($request->files->all() as $key=>$fileArray){
                                            $methodName = 'set'.ucfirst($key);
                                            $methodNameGet = 'get'.ucfirst($key);
                                            if(property_exists(ProfileDetails::class,$key) && method_exists(ProfileDetails::class,$methodName) && method_exists(ProfileDetails::class,$methodNameGet)){
                                                $files[$key] = [];
                                                if($key == 'video'){
                                                    if($fileArray instanceof UploadedFile){
                                                        $fileName = $user->getFirstName()."_".$user->getId().".".$fileArray->getClientOriginalExtension();
                                                        if($fileArray->move("uploads/candidate/".$user->getId(),$fileName)){

                                                            $credentials = new Credentials($this->container->getParameter('aws_key'), $this->container->getParameter('aws_secret'));
                                                            $s3Client = new S3Client([
                                                                'version'     => 'latest',
                                                                'region'      => $this->container->getParameter('aws_region'),
                                                                'credentials' => $credentials
                                                            ]);

                                                            try {
                                                                $resultAws = $s3Client->putObject(array(
                                                                    'Bucket' => $this->container->getParameter('aws_bucket'),
                                                                    'Key'    => $fileName,
                                                                    'SourceFile' => "uploads/candidate/".$user->getId()."/".$fileName,
                                                                    'ACL' => 'public-read'
                                                                ));
                                                            } catch (\Exception $e) {

                                                            }
                                                            if(isset($resultAws) && isset($resultAws['ObjectURL'])){
                                                                $filePath = $resultAws['ObjectURL'];
                                                                $fileSystem = new Filesystem();
                                                                if($fileSystem->exists("uploads/candidate/".$user->getId()."/".$fileName)){
                                                                    try{
                                                                        $fileSystem->remove("uploads/candidate/".$user->getId()."/".$fileName);
                                                                    }
                                                                    catch (\Exception $e){}
                                                                }

                                                            }
                                                            else{
                                                                $filePath = $request->getSchemeAndHttpHost()."/uploads/candidate/".$user->getId()."/".$fileName;
                                                            }

                                                            $files[$key] = [
                                                                'url'=>$filePath,
                                                                'adminUrl'=>$filePath,
                                                                'name'=>$fileName,
                                                                'approved'=>true
                                                            ];
                                                        }
                                                    }
                                                }
                                                elseif(is_array($fileArray)){
                                                    foreach ($fileArray as $fileUpload){
                                                        if($fileUpload instanceof UploadedFile){
                                                            $fileName = md5(uniqid()).'.'.$fileUpload->getClientOriginalExtension();
                                                            if($fileUpload->move("uploads/candidate/".$user->getId(),$fileName)){
                                                                $files[$key][] = [
                                                                    'url'=>$request->getSchemeAndHttpHost()."/uploads/candidate/".$user->getId()."/".$fileName,
                                                                    'adminUrl'=>$request->getSchemeAndHttpHost()."/uploads/candidate/".$user->getId()."/".$fileName,
                                                                    'name'=>$fileUpload->getClientOriginalName(),
                                                                    'size'=>$fileUpload->getClientSize(),
                                                                    'approved'=>true
                                                                ];
                                                            }
                                                        }
                                                    }
                                                }
                                                else{
                                                    if($fileArray instanceof UploadedFile){
                                                        $fileName = md5(uniqid()).'.'.$fileArray->getClientOriginalExtension();
                                                        if($fileArray->move("uploads/candidate/".$user->getId(),$fileName)){
                                                            $files[$key][] = [
                                                                'url'=>$request->getSchemeAndHttpHost()."/uploads/candidate/".$user->getId()."/".$fileName,
                                                                'adminUrl'=>$request->getSchemeAndHttpHost()."/uploads/candidate/".$user->getId()."/".$fileName,
                                                                'name'=>$fileArray->getClientOriginalName(),
                                                                'size'=>$fileArray->getClientSize(),
                                                                'approved'=>true
                                                            ];
                                                        }
                                                    }
                                                }

                                                $profileDetails->$methodName($files[$key]);
                                            }
                                            else{
                                                $view = $this->view(['error'=>'field '.$key.' not found'], Response::HTTP_BAD_REQUEST);
                                                return $this->handleView($view);
                                            }

                                        }
                                        $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                                        //$profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                                        $em->persist($profileDetails);
                                        $em->flush();
                                    }
                                    $logging = new Logging($this->getUser(),1, $user->getFirstName()." ".$user->getLastName());
                                    $em->persist($logging);
                                    $em->flush();
                                    $view = $this->view([], Response::HTTP_NO_CONTENT);
                                    return $this->handleView($view);
                                }
                                else {
                                    $error_description = [];
                                    foreach ($errorsDetails as $er) {
                                        $error_description[] = $er->getMessage();
                                    }
                                    $view = $this->view(['error'=>$error_description], Response::HTTP_BAD_REQUEST);
                                    return $this->handleView($view);
                                }
                            }
                            else{
                                $view = $this->view(['error'=>['SA ID Number is invalid']], Response::HTTP_BAD_REQUEST);
                                return $this->handleView($view);
                            }
                        }
                        else{
                            $view = $this->view(['error'=>['SA ID Number is required']], Response::HTTP_BAD_REQUEST);
                            return $this->handleView($view);
                        }
                    }
                    else{
                        $view = $this->view(['error'=>['profile is required']], Response::HTTP_BAD_REQUEST);
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
                $view = $this->view(['error'=>['all user field required']], Response::HTTP_BAD_REQUEST);
                return $this->handleView($view);
            }
        }
        else{
            $view = $this->view(['error'=>['user field required']], Response::HTTP_BAD_REQUEST);
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
     * @SWG\Get(path="/api/admin/candidate/{id}",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="Get Candidate Details",
     *   description="The method for getting Candidate details for admin",
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
     *      default="candidateId",
     *      description="Candidate ID"
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
     *                  property="id",
     *                  type="string"
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
     *                  property="phone",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="email",
     *                  type="string"
     *              ),
     *          ),
     *          @SWG\Property(
     *              property="profile",
     *              type="object",
     *              @SWG\Property(
     *                  property="idNumber",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="nationality",
     *                  type="integer",
     *                  description="1=South African, 2=Non South African"
     *              ),
     *              @SWG\Property(
     *                  property="ethnicity",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="beeCheck",
     *                  type="datetime",
     *                  example="2018-09-09"
     *              ),
     *              @SWG\Property(
     *                  property="mostRole",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="mostEmployer",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="specialization",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="gender",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="dateOfBirth",
     *                  type="date"
     *              ),
     *              @SWG\Property(
     *                  property="mostSalary",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="salaryPeriod",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="criminal",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="criminalDescription",
     *                  type="string",
     *                  description="show when criminal true"
     *              ),
     *              @SWG\Property(
     *                  property="credit",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="creditDescription",
     *                  type="string",
     *                  description="show when credit true"
     *              ),
     *              @SWG\Property(
     *                  property="homeAddress",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="driverLicense",
     *                  type="boolean",
     *              ),
     *              @SWG\Property(
     *                  property="driverNumber",
     *                  type="string",
     *              ),
     *              @SWG\Property(
     *                  property="englishProficiency",
     *                  type="integer",
     *                  description="1 = Below Average 2 = Average 3 = Good 4 = Exceptional"
     *              ),
     *              @SWG\Property(
     *                  property="employed",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="employedDate",
     *                  type="date"
     *              ),
     *              @SWG\Property(
     *                  property="availability",
     *                  type="boolean"
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
     *                  type="date"
     *              ),
     *              @SWG\Property(
     *                  property="citiesWorking",
     *                  type="array",
     *                  @SWG\Items(type="string"),
     *              ),
     *              @SWG\Property(
     *                  property="copyOfID",
     *                  type="array",
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
     *                          type="boolean"
     *                      ),
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="cv",
     *                  type="array",
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
     *                          type="boolean"
     *                      ),
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="universityExemption",
     *                  type="boolean",
     *                  example=false
     *              ),
     *              @SWG\Property(
     *                  property="matricCertificate",
     *                  type="array",
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
     *                          type="boolean"
     *                      ),
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="matricTranscript",
     *                  type="array",
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
     *                          type="boolean"
     *                      ),
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="certificateOfQualification",
     *                  type="array",
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
     *                          type="boolean"
     *                      ),
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="academicTranscript",
     *                  type="array",
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
     *                          type="boolean"
     *                      ),
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="creditCheck",
     *                  type="array",
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
     *                          type="boolean"
     *                      ),
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="payslip",
     *                  type="array",
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
     *                          type="boolean"
     *                      ),
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="picture",
     *                  type="array",
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
     *                          type="boolean"
     *                      ),
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="video",
     *                  type="object",
     *                  @SWG\Property(
     *                      property="url",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="name",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="approved",
     *                      type="boolean"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="percentage",
     *                  type="integer",
     *                  example=50
     *              ),
     *              @SWG\Property(
     *                  property="looking",
     *                  type="boolean",
     *                  example=false
     *              ),
     *              @SWG\Property(
     *                  property="firstJob",
     *                  type="boolean",
     *                  example=false
     *              )
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
    public function getCandidateDetailsByIdAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);
        if($user instanceof User and $user->hasRole('ROLE_CANDIDATE')){
            $userDetails = $em->getRepository("AppBundle:User")->getCandidateProfile($user->getId());
            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->getCandidateDetails($user->getId());

            $view = $this->view(['user'=>$userDetails,'profile'=>$profileDetails], Response::HTTP_OK);
        }
        else{
            $view = $this->view(['error'=>'Candidate Not found or user not has ROLE_CANDIDATE'], Response::HTTP_NOT_FOUND);
        }
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Put("/{id}",requirements={"id"="\d+"})
     * @SWG\Put(path="/api/admin/candidate/{id}",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="Edit Candidate Profile Details For Admin",
     *   description="The method for Edit Candidate profile details for Admin",
     *   produces={"application/json"},
     *   consumes={"application/json"},
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
     *      default="candidateId",
     *      description="candidateId"
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
     *                  property="phone",
     *                  type="string",
     *                  example="123213123",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="email",
     *                  type="string",
     *                  example="email@gmail.com",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="agentName",
     *                  type="string",
     *                  example="agentName"
     *              )
     *          ),
     *          @SWG\Property(
     *              property="profile",
     *              type="object",
     *              @SWG\Property(
     *                  property="idNumber",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="nationality",
     *                  type="integer",
     *                  description="1=South African, 2=Non South African"
     *              ),
     *              @SWG\Property(
     *                  property="ethnicity",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="beeCheck",
     *                  type="datetime",
     *                  example="2018-09-09"
     *              ),
     *              @SWG\Property(
     *                  property="mostRole",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="mostEmployer",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="specialization",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="gender",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="dateOfBirth",
     *                  type="date"
     *              ),
     *              @SWG\Property(
     *                  property="mostSalary",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="salaryPeriod",
     *                  type="string",
     *                  description="monthly/annual"
     *              ),
     *              @SWG\Property(
     *                  property="criminal",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="criminalDescription",
     *                  type="string",
     *                  description="show when criminal true"
     *              ),
     *              @SWG\Property(
     *                  property="credit",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="creditDescription",
     *                  type="string",
     *                  description="show when credit true"
     *              ),
     *              @SWG\Property(
     *                  property="homeAddress",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="driverLicense",
     *                  type="boolean",
     *              ),
     *              @SWG\Property(
     *                  property="driverNumber",
     *                  type="string",
     *              ),
     *              @SWG\Property(
     *                  property="englishProficiency",
     *                  type="integer",
     *                  description="1 = Below Average 2 = Average 3 = Good 4 = Exceptional"
     *              ),
     *              @SWG\Property(
     *                  property="employed",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="employedDate",
     *                  type="date"
     *              ),
     *              @SWG\Property(
     *                  property="availability",
     *                  type="boolean"
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
     *                  type="date"
     *              ),
     *              @SWG\Property(
     *                  property="citiesWorking",
     *                  type="array",
     *                  @SWG\Items(type="string"),
     *              ),
     *              @SWG\Property(
     *                  property="firstJob",
     *                  type="boolean",
     *              ),
     *          ),
     *      ),
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success. Updated.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="percentage",
     *              type="integer",
     *              example=50
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
    public function editCandidateDetailsByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);

        if($user instanceof User && $user->hasRole("ROLE_CANDIDATE")){
            if($request->request->has('user') && !empty(($request->request->get('user')))){
                $userData = $request->request->get('user');

                if(isset($userData['firstName']) && isset($userData['lastName']) && isset($userData['email']) && isset($userData['phone'])){
                    $user->setFirstName($userData['firstName']);
                    $user->setLastName($userData['lastName']);
                    $user->setEmail($userData['email']);
                    $user->setUsername($userData['email']);
                    $user->setPhone($userData['phone']);
                    $user->setAgentName((isset($userData['agentName'])) ? $userData['agentName'] : null);
                    $errors = $this->get('validator')->validate($user, null, array('updateCandidate'));
                    if(count($errors) === 0){
                        $em->persist($user);
                        if($request->request->has('profile') && !empty($request->request->get('profile'))){
                            $dataProfile = $request->request->get('profile');
                            if(isset($dataProfile['idNumber']) && !empty($dataProfile['idNumber'])){
                                $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
                                if(!$profileDetails instanceof ProfileDetails){
                                    $profileDetails = new ProfileDetails($user, $dataProfile['idNumber']);
                                }
                                $profileDetails->update($dataProfile);
                                $errorsDetails = $this->get('validator')->validate($profileDetails, null, array('updateDetails'));
                                if(count($errorsDetails) === 0){
                                    if(!HelpersClass::isValidIDNumber($profileDetails->getIdNumber())){
                                        $view = $this->view(['error'=>['SA ID Number is invalid']], Response::HTTP_BAD_REQUEST);
                                        return $this->handleView($view);
                                    }
                                }
                                else{
                                    $error_description = [];
                                    foreach ($errorsDetails as $er) {
                                        $error_description[] = $er->getMessage();
                                    }
                                    $view = $this->view(['error'=>$error_description], Response::HTTP_BAD_REQUEST);
                                    return $this->handleView($view);
                                }

                                $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                                $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                                $em->persist($profileDetails);
                                $em->flush();

                                $logging = new Logging($this->getUser(),2, $user->getFirstName()." ".$user->getLastName(), $user->getId());
                                $em->persist($logging);
                                $em->flush();
                                $view = $this->view(['percentage'=>$profileDetails->getPercentage()], Response::HTTP_OK);
                            }
                            else{
                                $view = $this->view(['error'=>['idNumber is required']], Response::HTTP_BAD_REQUEST);
                            }
                        }
                        else{
                            $view = $this->view(['error'=>['profile us required']], Response::HTTP_BAD_REQUEST);
                        }
                    }
                    else {
                        $error_description = [];
                        foreach ($errors as $er) {
                            $error_description[] = $er->getMessage();
                        }
                        $view = $this->view(['error'=>$error_description], Response::HTTP_BAD_REQUEST);
                    }
                }
                else{
                    $view = $this->view(['error'=>['all user field required']], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>['user field required']], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>['User NOT Found OR User not has ROLE_CANDIDATE']], Response::HTTP_NOT_FOUND);
        }


        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     *
     * @Rest\Patch("/{id}",requirements={"id"="\d+"})
     * @SWG\Patch(path="/api/admin/candidate/{id}",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="Update Candidate Status",
     *   description="The method for updating Candidate status for admin",
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
     *      default="candidateId",
     *      description="Candidate ID"
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
    public function editCandidateStatusByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);
        if($user instanceof User and $user->hasRole('ROLE_CANDIDATE')){
            if($request->request->has('enabled') && is_bool($request->request->get('enabled'))){
                $user->setEnabled($request->request->get('enabled'));
                $user->setApproved($request->request->get('enabled'));
                $em->persist($user);
                $em->flush();
                if($request->request->get('enabled')){
                    $logging = new Logging($this->getUser(),3, $user->getFirstName()." ".$user->getLastName(), $user->getId());
                    $em->persist($logging);
                    $em->flush();
                }
                else{
                    $logging = new Logging($this->getUser(),4, $user->getFirstName()." ".$user->getLastName(), $user->getId());
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
            $view = $this->view(['error'=>'Candidate Not found or user not has ROLE_CANDIDATE'], Response::HTTP_NOT_FOUND);
        }
        return $this->handleView($view);

    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     *
     * @Rest\Delete("/{id}",requirements={"id"="\d+"})
     * @SWG\Delete(path="/api/admin/candidate/{id}",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="Delete Candidate Profile",
     *   description="The method for Delete Candidate for admin",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      type="integer",
     *      default="candidateID",
     *      description="Candidate ID"
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
     *      description="Success. Candidate"
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
    public function removeCandidateByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);
        if($user instanceof User && $user->hasRole("ROLE_CANDIDATE")){
            $em->remove($user);
            $em->flush();
            $logging = new Logging($this->getUser(),5, $user->getFirstName()." ".$user->getLastName());
            $em->persist($logging);
            $em->flush();
            $view = $this->view([], Response::HTTP_NO_CONTENT);
        }
        else{
            $view = $this->view(['error'=>'Candidate Not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Post("/{id}/file",requirements={"id"="\d+"})
     * @SWG\Post(path="/api/admin/candidate/{id}/file",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="Upload Candidate File",
     *   description="The method for Upload candidate File for admin",
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
     *      default="multipart/form-data",
     *      description="Content Type"
     *   ),
     *   @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      type="integer",
     *      default="candidateId",
     *      description="Candidate ID"
     *   ),
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              type="array",
     *              property="fieldName",
     *              @SWG\Items(
     *                  type="string",
     *              ),
     *              example={"file1","file2"}
     *          ),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *           @SWG\Property(
     *              property="percentage",
     *              type="integer",
     *              example=50
     *          ),
     *          @SWG\Property(
     *              property="files",
     *              type="object",
     *              @SWG\Property(
     *                  property="fieldName",
     *                  type="array",
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
     *                          type="boolean"
     *                      ),
     *                  )
     *              )
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
    public function uploadCandidateFileByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);
        if($user instanceof User && $user->hasRole("ROLE_CANDIDATE")){
            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
            if($profileDetails instanceof ProfileDetails){
                $files = [];
                if(!empty($request->files->all())){
                    foreach ($request->files->all() as $key=>$fileArray){
                        $methodName = 'set'.ucfirst($key);
                        $methodNameGet = 'get'.ucfirst($key);
                        if(property_exists(ProfileDetails::class,$key) && method_exists(ProfileDetails::class,$methodName) && method_exists(ProfileDetails::class,$methodNameGet)){
                            $files[$key] = [];
                            if(is_array($fileArray)){
                                foreach ($fileArray as $fileUpload){
                                    if($fileUpload instanceof UploadedFile){
                                        $fileName = md5(uniqid()).'.'.$fileUpload->getClientOriginalExtension();
                                        if($fileUpload->move("uploads/candidate/".$user->getId(),$fileName)){
                                            $files[$key][] = [
                                                'url'=>$request->getSchemeAndHttpHost()."/uploads/candidate/".$user->getId()."/".$fileName,
                                                'adminUrl'=>$request->getSchemeAndHttpHost()."/uploads/candidate/".$user->getId()."/".$fileName,
                                                'name'=>$fileUpload->getClientOriginalName(),
                                                'size'=>$fileUpload->getClientSize(),
                                                'approved'=>true
                                            ];
                                        }
                                    }
                                }
                            }
                            else{
                                if($fileArray instanceof UploadedFile){
                                    $fileName = md5(uniqid()).'.'.$fileArray->getClientOriginalExtension();
                                    if($fileArray->move("uploads/candidate/".$user->getId(),$fileName)){
                                        $files[$key][] = [
                                            'url'=>$request->getSchemeAndHttpHost()."/uploads/candidate/".$user->getId()."/".$fileName,
                                            'adminUrl'=>$request->getSchemeAndHttpHost()."/uploads/candidate/".$user->getId()."/".$fileName,
                                            'name'=>$fileArray->getClientOriginalName(),
                                            'size'=>$fileArray->getClientSize(),
                                            'approved'=>true
                                        ];
                                    }
                                }
                            }
                            $issetFiles = $profileDetails->$methodNameGet();
                            if(!empty($issetFiles) && $key != 'picture' && $key != 'copyOfID' && $key != 'cv' && $key != 'creditCheck' && $key != 'payslip'){
                                $files[$key] = array_merge($issetFiles, $files[$key]);
                            }
                            $profileDetails->$methodName($files[$key]);
                        }
                        else{
                            $view = $this->view(['error'=>'field '.$key.' not found'], Response::HTTP_BAD_REQUEST);
                            return $this->handleView($view);
                        }

                    }
                    $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                    $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                    $em->persist($profileDetails);
                    $em->flush();
                    $view = $this->view(['percentage'=>$profileDetails->getPercentage(), 'files'=>$files], Response::HTTP_OK);
                }
                else{
                    $view = $this->view(['error'=>'Files is empty'], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>'Profile Not found'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'Candidate Not Found OR user not has ROLE_CANDIDATE'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Patch("/{id}/file",requirements={"id"="\d+"})
     * @SWG\Patch(path="/api/admin/candidate/{id}/file",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="Remove Candidate File",
     *   description="The method for remove candidate File for admin",
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
     *      default="candidateId",
     *      description="Candidate ID"
     *   ),
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              type="object",
     *              property="fieldName",
     *              @SWG\Property(
     *                  type="string",
     *                  property="url",
     *                  example="fileURL",
     *                  description="required"
     *              )
     *          ),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="fieldName",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="url",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="name",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="size",
     *                      type="integer"
     *                  ),
     *                  @SWG\Property(
     *                      property="approved",
     *                      type="boolean"
     *                  ),
     *              )
     *          )
     *     )
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
    public function removeCandidateFileByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);
        if($user instanceof User && $user->hasRole("ROLE_CANDIDATE")){
            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
            if($profileDetails instanceof ProfileDetails){
                $data = $request->request->all();
                if(!empty($data)){
                    foreach ($data as $key=>$item){
                        if(isset($item['url']) && !empty($item['url'])){
                            $methodNameSet = 'set'.ucfirst($key);
                            $methodNameGet = 'get'.ucfirst($key);
                            if(property_exists(ProfileDetails::class,$key) && method_exists(ProfileDetails::class,$methodNameSet) && method_exists(ProfileDetails::class,$methodNameGet)){
                                $files = $profileDetails->$methodNameGet();
                                $fileSystem = new Filesystem();
                                $checkFile = false;
                                foreach ($files as $k=>$file){
                                    if(isset($file['url']) && $file['url'] == $item['url']){
                                        $parse = parse_url($file['url']);
                                        if(isset($parse['path']) && !empty($parse['path'])){
                                            $parse['path'] = ltrim($parse['path'], '/');
                                            if($fileSystem->exists($parse['path'])){
                                                $fileSystem->remove($parse['path']);
                                            }
                                            unset($files[$k]);
                                            $checkFile = true;
                                        }
                                    }
                                }
                                if($checkFile == true){
                                    $newFiles = [];
                                    if(!empty($files)){
                                        foreach ($files as $f){
                                            $newFiles[] = $f;
                                        }
                                    }
                                    $profileDetails->$methodNameSet($newFiles);
                                    $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                                    $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                                    $em->persist($profileDetails);
                                    $em->flush();
                                    $view = $this->view([$key=>$profileDetails->$methodNameGet()], Response::HTTP_OK);
                                    return $this->handleView($view);
                                }
                                else{
                                    $view = $this->view(['error'=>'File Not found'], Response::HTTP_BAD_REQUEST);
                                    return $this->handleView($view);
                                }
                            }
                            else{
                                $view = $this->view(['error'=>'field '.$key.' not found'], Response::HTTP_BAD_REQUEST);
                                return $this->handleView($view);
                            }
                        }
                        else{
                            $view = $this->view(['error'=>'property url is required'], Response::HTTP_BAD_REQUEST);
                            return $this->handleView($view);
                        }
                    }
                }
                else{
                    $view = $this->view(['error'=>'body should be not empty'], Response::HTTP_BAD_REQUEST);
                    return $this->handleView($view);
                }
            }
            else{
                $view = $this->view(['error'=>'Profile Not Found'], Response::HTTP_NOT_FOUND);
                return $this->handleView($view);
            }
        }

        $view = $this->view(['error'=>'Candidate Not Found OR user not has ROLE_CANDIDATE'], Response::HTTP_NOT_FOUND);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Post("/{id}/video",requirements={"id"="\d+"})
     * @SWG\Post(path="/api/admin/candidate/{id}/video",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="Upload Candidate Video",
     *   description="The method for Upload candidate Video for admin",
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
     *      default="multipart/form-data",
     *      description="Content Type"
     *   ),
     *   @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      type="integer",
     *      default="candidateId",
     *      description="Candidate ID"
     *   ),
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="video",
     *              type="file",
     *              example="picture1.mp4"
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="percentage",
     *              type="integer",
     *              example=50
     *          ),
     *          @SWG\Property(
     *              property="video",
     *              type="object",
     *              @SWG\Property(
     *                  property="url",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="name",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="approved",
     *                  type="boolean"
     *              ),
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
    public function uploadCandidateVideoByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);
        if($user instanceof User && $user->hasRole("ROLE_CANDIDATE")){
            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
            if($profileDetails instanceof ProfileDetails){
                if(!empty($request->files->get('video'))){
                    $fileUpload = $request->files->get('video');
                    if($fileUpload instanceof UploadedFile){
                        $fileName = substr(md5(uniqid(mt_rand(), true)) , 0, 16)."_".$user->getFirstName()."_".$user->getId().".".$fileUpload->getClientOriginalExtension();
                        try {
                            $fileUpload->move("uploads/candidate/".$user->getId(),$fileName);
                        } catch (\Exception $e) {
                            $view = $this->view(['error'=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                            return $this->handleView($view);
                        }

                        $credentials = new Credentials($this->container->getParameter('aws_key'), $this->container->getParameter('aws_secret'));
                        $s3Client = new S3Client([
                            'version'     => 'latest',
                            'region'      => $this->container->getParameter('aws_region'),
                            'credentials' => $credentials
                        ]);

                        try {
                            $result = $s3Client->putObject(array(
                                'Bucket' => $this->container->getParameter('aws_bucket'),
                                'Key'    => $fileName,
                                'SourceFile' => "uploads/candidate/".$user->getId()."/".$fileName,
                                'ACL' => 'public-read'
                            ));
                        } catch (\Exception $e) {

                        }
                        if(isset($result) && isset($result['ObjectURL'])){
                            $filePath = $result['ObjectURL'];
                            $fileSystem = new Filesystem();
                            if($fileSystem->exists("uploads/candidate/".$user->getId()."/".$fileName)){
                                try{
                                    $fileSystem->remove("uploads/candidate/".$user->getId()."/".$fileName);
                                }
                                catch (\Exception $e){}
                            }
                            $oldVideo = $profileDetails->getVideo();
                            if(isset($oldVideo['name']) && !empty($oldVideo['name'])){
                                try{
                                    $result = $s3Client->deleteObject(array(
                                        'Bucket' => $this->container->getParameter('aws_bucket'),
                                        'Key'    => $oldVideo['name']
                                    ));
                                }
                                catch (\Exception $e){}
                            }
                        }
                        else{
                            $filePath = $request->getSchemeAndHttpHost()."/uploads/candidate/".$user->getId()."/".$fileName;
                        }
                        $video = [
                            'url'=>$filePath,
                            'adminUrl'=>$filePath,
                            'name'=>$fileName,
                            'approved'=>true
                        ];

                        $profileDetails->setVideo($video);
                        $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                        $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                        $em->persist($profileDetails);
                        $logging = new Logging($this->getUser(),29, $user->getFirstName()." ".$user->getLastName(), $user->getId());
                        $em->persist($logging);
                        $em->flush();
                        $view = $this->view(['percentage'=>$profileDetails->getPercentage(), 'video'=>$video], Response::HTTP_OK);
                    }
                    else{
                        $view = $this->view(['error'=>'Choose video'], Response::HTTP_BAD_REQUEST);
                    }
                }
                else{
                    $view = $this->view(['error'=>'Files is empty'], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>'Profile Not found'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'Candidate Not Found OR user not has ROLE_CANDIDATE'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Delete("/{id}/video",requirements={"id"="\d+"})
     * @SWG\Delete(path="/api/admin/candidate/{id}/video",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="Remove Candidate video",
     *   description="The method for remove candidate video for admin",
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
     *      default="candidateId",
     *      description="Candidate ID"
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="percentage",
     *              type="integer",
     *              example=50
     *          ),
     *     )
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
    public function removeCandidateVideoByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);
        if($user instanceof User && $user->hasRole("ROLE_CANDIDATE")){
            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
            if($profileDetails instanceof ProfileDetails){
                $video = $profileDetails->getVideo();

                if(isset($video['name'])){
                    $credentials = new Credentials($this->container->getParameter('aws_key'), $this->container->getParameter('aws_secret'));
                    $s3Client = new S3Client([
                        'version'     => 'latest',
                        'region'      => $this->container->getParameter('aws_region'),
                        'credentials' => $credentials
                    ]);

                    try {
                        $result = $s3Client->deleteObject(array(
                            'Bucket' => $this->container->getParameter('aws_bucket'),
                            'Key'    => $video['name']
                        ));
                        $fileSystem = new Filesystem();
                        if($fileSystem->exists("uploads/candidate/".$user->getId()."/".$video['name'])){
                            try{
                                $fileSystem->remove("uploads/candidate/".$user->getId()."/".$video['name']);
                            }
                            catch (\Exception $e){}
                        }
                    } catch (\Exception $e) {

                    }
                }
                $profileDetails->setVideo(NULL);

                $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                $em->persist($profileDetails);
                $logging = new Logging($this->getUser(),30, $user->getFirstName()." ".$user->getLastName(), $user->getId());
                $em->persist($logging);
                $em->flush();
                $view = $this->view(['percentage'=>$profileDetails->getPercentage()], Response::HTTP_OK);
                return $this->handleView($view);
            }
            else{
                $view = $this->view(['error'=>'Profile Not Found'], Response::HTTP_NOT_FOUND);
                return $this->handleView($view);
            }
        }

        $view = $this->view(['error'=>'Candidate Not Found OR user not has ROLE_CANDIDATE'], Response::HTTP_NOT_FOUND);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/file/approve")
     * @SWG\Get(path="/api/admin/candidate/file/approve",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="Get All Files Candidate when need approve",
     *   description="The method for getting all candidate files when need approve for admin",
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
     *      description="search by name or document"
     *   ),
     *   @SWG\Parameter(
     *      name="orderBy",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="Name, Document"
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
    public function getFileApproveAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $files = $em->getRepository("AppBundle:User")->getCandidateFilesApprove($request->query->all());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $files,
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
     * @param $userId
     * @return Response
     *
     * @Rest\Post("/file/{userId}/approve",requirements={"userId"="\d+"})
     * @SWG\Post(path="/api/admin/candidate/file/{userId}/approve",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="UPLOAD Approve Candidate File for Admin",
     *   description="The method for upload Approve Candidate File for Admin",
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
     *      name="userId",
     *      in="path",
     *      required=true,
     *      type="integer",
     *      default="candidateID",
     *      description="Candidate ID"
     *   ),
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="fieldName",
     *              type="string",
     *              example="fieldName",
     *              description="required. matricCertificate OR tertiaryCertificate OR universityManuscript OR creditCheck OR cvFiles"
     *          ),
     *          @SWG\Property(
     *              property="file",
     *              type="string",
     *              example="file.png",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="url",
     *              type="string",
     *              example="url",
     *              description="required"
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="adminUrl",
     *              type="string",
     *              example="adminUrl"
     *          )
     *      )
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
    public function candidateUploadFileApprovedAction(Request $request, $userId){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($userId);
        if($user instanceof User){
            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
            if($profileDetails instanceof ProfileDetails){
                if($request->request->has('fieldName') && !empty($request->request->get('fieldName'))){
                    if($request->files->has('file') && !empty($request->files->get('file'))){
                        $fileUpload = $request->files->get('file');
                        if($fileUpload instanceof UploadedFile){
                            if($request->request->has('url') && !empty($request->request->get('url'))){
                                $methodGet = 'get'.ucfirst($request->request->get('fieldName'));
                                $methodSet = 'set'.ucfirst($request->request->get('fieldName'));
                                if(property_exists(ProfileDetails::class,$request->request->get('fieldName')) && method_exists(ProfileDetails::class,$methodGet) && method_exists(ProfileDetails::class,$methodSet)) {
                                    $files = $profileDetails->$methodGet();
                                    if(!empty($files)) {
                                        foreach ($files as $key=>$file){
                                            if(isset($file['url']) && $file['url'] == $request->request->get('url')){
                                                $fileName = md5(uniqid()).'.'.$fileUpload->getClientOriginalExtension();
                                                try {
                                                    $fileUpload->move("uploads/candidate/".$user->getId(),$fileName);
                                                } catch (\Exception $e) {
                                                    $view = $this->view(['error'=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                                                    return $this->handleView($view);
                                                }
                                                $files[$key]['adminUrl'] = $request->getSchemeAndHttpHost()."/uploads/candidate/".$user->getId()."/".$fileName;
                                                $profileDetails->$methodSet($files);
                                                $em->persist($profileDetails);
                                                $em->flush();

                                                $view = $this->view([
                                                    'adminUrl'=>$request->getSchemeAndHttpHost()."/uploads/candidate/".$user->getId()."/".$fileName
                                                ], Response::HTTP_OK);
                                                return $this->handleView($view);
                                            }
                                        }
                                    }
                                    $view = $this->view(['error'=>'Candidate File NOT FOUND'], Response::HTTP_NOT_FOUND);
                                }
                                else{
                                    $view = $this->view(['error'=>'fieldName is invalid'], Response::HTTP_BAD_REQUEST);
                                }
                            }
                            else{
                                $view = $this->view(['error'=>'url is required and should be not empty'], Response::HTTP_BAD_REQUEST);
                            }
                        }
                        else{
                            $view = $this->view(['error'=>'Is not file'], Response::HTTP_BAD_REQUEST);
                        }
                    }
                    else{
                        $view = $this->view(['error'=>'file is required and should be not empty'], Response::HTTP_BAD_REQUEST);
                    }
                }
                else{
                    $view = $this->view(['error'=>'fieldName is required and should be not empty'], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>'Candidate Details Not found'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'Candidate Not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->handleView($view);

    }

    /**
     * @param Request $request
     * @param $userId
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     *
     * @Rest\Patch("/file/{userId}/approve",requirements={"userId"="\d+"})
     * @SWG\Patch(path="/api/admin/candidate/file/{userId}/approve",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="Approve Candidate File for Admin",
     *   description="The method for Approve Candidate File for Admin",
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
     *      name="userId",
     *      in="path",
     *      required=true,
     *      type="integer",
     *      default="candidateID",
     *      description="Candidate ID"
     *   ),
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="fieldName",
     *              type="string",
     *              example="fieldName",
     *              description="required. matricCertificate OR tertiaryCertificate OR universityManuscript OR creditCheck OR cvFiles"
     *          ),
     *          @SWG\Property(
     *              property="url",
     *              type="string",
     *              example="url",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="approved",
     *              type="boolean",
     *              example=true,
     *              description="required, true=approve,false=decline"
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success. Candidate File Approve or Decline",
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
    public function candidateFileApprovedAction(Request $request, $userId){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($userId);
        if($user instanceof User){
            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
            if($profileDetails instanceof ProfileDetails){
                if($request->request->has('fieldName') && $request->request->has('url') && $request->request->has('approved')){
                    if(is_bool($request->request->get('approved'))){
                        $methodGet = 'get'.ucfirst($request->request->get('fieldName'));
                        $methodSet = 'set'.ucfirst($request->request->get('fieldName'));
                        if(property_exists(ProfileDetails::class,$request->request->get('fieldName')) && method_exists(ProfileDetails::class,$methodGet) && method_exists(ProfileDetails::class,$methodSet)){
                            $files = $profileDetails->$methodGet();
                            if(!empty($files)){
                                $checkFile = false;
                                foreach ($files as $key=>$file){
                                    if($file['url'] == $request->request->get('url')){
                                        $checkFile = true;
                                        if($request->request->get('approved') == true){

                                            if(!isset($files[$key]['adminUrl']) || empty($files[$key]['adminUrl'])){
                                            	print_r($files[$key]);
												$files[$key]['adminUrl'] = $file['url'];
												$profileDetails->$methodSet($files);
												$em->persist($profileDetails);
												$em->flush();
												print_r($files[$key]);
											}
//                                            if(isset($files[$key]['adminUrl']) && !empty($files[$key]['adminUrl'])){
                                                $files[$key]['approved'] = true;
                                                $logging = new Logging($this->getUser(),6, $file['name']);
                                                $em->persist($logging);
                                                $em->flush();
                                                $notifyCandidate = $em->getRepository("AppBundle:NotificationCandidate")->findOneBy(['user'=>$user,'documentApproveStatus'=>true]);
                                                if($notifyCandidate instanceof NotificationCandidate){
                                                    if($notifyCandidate->getNotifyEmail() == true && $notifyCandidate->getDocumentApproveStatus() == true){
                                                        $message = (new \Swift_Message('Your document has been approved on Yes2Work'))
                                                            ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                                                            ->setTo($user->getEmail())
                                                            ->setBody(
                                                                $this->renderView('emails/candidate/document_approve.html.twig', [
                                                                    'candidate' => [
                                                                        'firstName'=>$user->getFirstName()
                                                                    ],
                                                                    'link' => $request->getSchemeAndHttpHost().'/candidate/view_cv'
                                                                ]),
                                                                'text/html'
                                                            );

                                                        try{
                                                            $this->get('mailer')->send($message);
                                                        }catch(\Swift_TransportException $e){

                                                        }
                                                    }
                                                    if($notifyCandidate->getNotifySMS() == true && $notifyCandidate->getDocumentApproveStatus() == true){
                                                        if(!empty($user->getPhone())){
                                                            if(substr($user->getPhone(), 0, 1) == '+'){
                                                                $number = substr($user->getPhone(), 1);
                                                            }
                                                            else{
                                                                $number = $user->getPhone();
                                                            }
                                                            $message = "Hi ".$user->getFirstName()."!\n\n";
                                                            $message .= "Your document that you recently uploaded on Yes2Work has been approved\n\n";
                                                            $message .= "Please login to Yes2Work to view your profile.\n\n";
                                                            $message .= $request->getSchemeAndHttpHost()." \n\n";
                                                            $message .= "The Yes2Work Team\n";
                                                            $message .= "support@yes2work.co.za";
                                                            if(isset($number) && !empty($number) && isset($message) && !empty($message)){
                                                                $api = new SendSMSApp();
                                                                $result = $api->message_send($number, $message, 'Yes2Work');
                                                            }
                                                        }
                                                    }
                                                }
//                                            }
//                                            else{
//                                                $view = $this->view(['error'=>'An Admin document version is required'], Response::HTTP_BAD_REQUEST);
//                                                return $this->handleView($view);
//                                            }
                                        }
                                        else{
                                            $fileSystem = new Filesystem();
                                            $parse = parse_url($file['url']);
                                            if(isset($parse['path']) && !empty($parse['path'])){
                                                $parse['path'] = ltrim($parse['path'], '/');
                                                if($fileSystem->exists($parse['path'])){
                                                    $fileSystem->remove($parse['path']);
                                                }
                                                unset($files[$key]);
                                                $logging = new Logging($this->getUser(),7, $file['name']);
                                                $em->persist($logging);
                                                $em->flush();
                                            }
                                        }
                                    }
                                }
                                if($checkFile === true){
                                    $newFiles = [];
                                    if(!empty($files)){
                                        foreach ($files as $f){
                                            $newFiles[] = $f;
                                        }
                                    }
                                    $profileDetails->$methodSet($newFiles);
                                    $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                                    $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                                    $em->persist($profileDetails);
                                    $em->flush();
                                    $view = $this->view([], Response::HTTP_NO_CONTENT);
                                }
                                else{
                                    $view = $this->view(['error'=>'file not found'], Response::HTTP_BAD_REQUEST);
                                }
                            }
                            else{
                                $view = $this->view(['error'=>'file not found'], Response::HTTP_BAD_REQUEST);
                            }
                        }
                        else{
                            $view = $this->view(['error'=>'fieldName field is invalid'], Response::HTTP_BAD_REQUEST);
                        }
                    }
                    else{
                        $view = $this->view(['error'=>'approved field should be boolean type'], Response::HTTP_BAD_REQUEST);
                    }
                }
                else{
                    $view = $this->view(['error'=>'all fields required'], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>'Candidate Details Not found'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'Candidate Not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->handleView($view);

    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/video/approve")
     * @SWG\Get(path="/api/admin/candidate/video/approve",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="Get All videos Candidate when need approve",
     *   description="The method for getting all candidate videos when need approve for admin",
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
    public function getVideoApproveAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository("AppBundle:User")->getCandidateVideosApprove();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $video,
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
     * @return Response
     *
     * @Rest\Get("/approve")
     * @SWG\Get(path="/api/admin/candidate/approve",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="Get Candidate when need Approve",
     *   description="The method for getting Candidate when need Approve",
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
    public function getCandidateApproveAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $candidateApprove = $em->getRepository("AppBundle:User")->getCandidateApprove($request->query->all());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $candidateApprove,
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
     * @SWG\Patch(path="/api/admin/candidate/{id}/approve",
     *   tags={"Admin Candidate"},
     *   security={true},
     *   summary="Approve Candidate Account for Admin",
     *   description="The method for Approve Candidate Account for Admin",
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
     *      default="candidateID",
     *      description="Candidate ID"
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
     *      description="Success. Candidate Approved or Decline",
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
    public function candidateApprovedAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $candidate = $em->getRepository("AppBundle:User")->find($id);
        if($candidate instanceof User){
            if($candidate->hasRole("ROLE_CANDIDATE")){
                if($request->request->has('approved') && is_bool($request->request->get('approved'))){
                    $candidate->setApproved($request->request->get('approved'));
                    $candidate->setEnabled($request->request->get('approved'));
                    $em->persist($candidate);
                    $em->flush();
                    if($candidate->getApproved() === true){
                        $message = (new \Swift_Message('Welcome to Yes2Work!'))
                            ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                            ->setTo($candidate->getEmail())
                            ->setBody(
                                $this->renderView('emails/candidate/candidate_approved.html.twig', [
                                    'user' => $candidate,
                                    'link' => $request->getSchemeAndHttpHost()
                                ]),
                                'text/html'
                            );
                        $logging = new Logging($this->getUser(),8, $candidate->getFirstName()." ".$candidate->getLastName(), $candidate->getId());
                        $em->persist($logging);
                        $em->flush();

                        try{
                            $this->get('mailer')->send($message);
                        }catch(\Swift_TransportException $e){

                        }

                        $view = $this->view([], Response::HTTP_NO_CONTENT);
                    }
                    else{
                        $message = (new \Swift_Message('Yes2Work Registration declined'))
                            ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                            ->setTo($candidate->getEmail())
                            ->setBody(
                                $this->renderView('emails/candidate/candidate_decline.html.twig', [
                                    'user' => $candidate
                                ]),
                                'text/html'
                            );
                        $logging = new Logging($this->getUser(),9, $candidate->getFirstName()." ".$candidate->getLastName(), $candidate->getId());
                        $em->persist($logging);
                        $em->flush();
                        try{
                            $this->get('mailer')->send($message);
                        }catch(\Swift_TransportException $e){

                        }

                        $em->remove($candidate);
                        $em->flush();

                        $view = $this->view([], Response::HTTP_NO_CONTENT);
                    }

                }
                else{
                    $view = $this->view(['error'=>'field approved is required or npt boolean'], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>'user NOT ROLE_CANDIDATE'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'User not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->handleView($view);
    }

    /**
     * @param $em
     * @param $result
     * @param $params
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function filtering_candidates($em, $result, $params)
    {
        $applicants = [];
        if (!empty($result)) {

            if (isset($params['highestQualification']) && $params['highestQualification'] != 'null' && $params['highestQualification'] != NULL && $params['highestQualification'] != 'All') {
                if (is_array($params['highestQualification'])) {
                    $highestQualification = $params['highestQualification'];
                } else {
                    $highestQualification = explode(',', $params['highestQualification']);
                }
                if (in_array('NQF 4 - Matric', $highestQualification)) {
                    $highestQualification = array('NQF 4 - Matric');
                }
            }
            else {
                $highestQualification = array();
            }
            if (isset($params['yearsOfWorkExperience']) && $params['yearsOfWorkExperience'] != 'null' && $params['yearsOfWorkExperience'] != NULL && $params['yearsOfWorkExperience'] != 0) {
                if (is_array($params['yearsOfWorkExperience'])) {
                    $yearsOfWorkExperience = $params['yearsOfWorkExperience'];
                } else {
                    $yearsOfWorkExperience = explode(',', $params['yearsOfWorkExperience']);
                }
            }
            else {
                $yearsOfWorkExperience = array();
            }

            $applicants_ids = [];
            foreach ($result as $key => $applicant) {
                if (isset($applicants_ids[$applicant['id']])) {
                    continue;
                }
                $applicants_ids[$applicant['id']] = $applicant['id'];
                $applicant['yearsOfWorkExperience'] = HelpersClass::getYearsOfWorkExperienceForCandidate($em, $applicant['id']);
                $applicant['highestQualification'] = HelpersClass::getHighestQualificationForCandidate($em, $applicant['id']);

                $filter_ok = true;
                if (!empty($highestQualification)) {
                    if (!in_array($applicant['highestQualification'], $highestQualification)) {
                        $filter_ok = false;
                    }
                }
                if (!empty($yearsOfWorkExperience) && $filter_ok == true) {
                    $filter_ok = false;
                    /* $yearsOfWorkExperience
                    0 All
                    1 0
                    2 0-1
                    3 1-2
                    4 3-5
                    5 5+
                    */
                    if (in_array(1, $yearsOfWorkExperience) && $applicant['yearsOfWorkExperience'] == 0) {
                        $filter_ok = true;
                    } elseif (in_array(2, $yearsOfWorkExperience) && ($applicant['yearsOfWorkExperience'] == 0 || $applicant['yearsOfWorkExperience'] == 1)) {
                        $filter_ok = true;
                    } elseif (in_array(3, $yearsOfWorkExperience) && ($applicant['yearsOfWorkExperience'] == 1 || $applicant['yearsOfWorkExperience'] == 2)) {
                        $filter_ok = true;
                    } elseif (in_array(4, $yearsOfWorkExperience) && ($applicant['yearsOfWorkExperience'] > 2 && $applicant['yearsOfWorkExperience'] < 6)) {
                        $filter_ok = true;
                    } elseif (in_array(5, $yearsOfWorkExperience) && $applicant['yearsOfWorkExperience'] > 5) {
                        $filter_ok = true;
                    }
                }
                if ($filter_ok === true) {
                    $applicants[] = $applicant;
                }
            }

            //Sorting
            if(isset($params['orderBy']) && !empty($params['orderBy']) && in_array($params['orderBy'], ['Name', 'Email', 'Phone'])){
                if(isset($params['orderSort']) && !empty($params['orderSort']) && in_array($params['orderSort'], ['asc', 'desc'])){
                    if($params['orderBy'] == 'Name'){
                        if($params['orderSort'] == 'asc'){
                            usort($applicants, function($a, $b) {
                                return $a['firstName'] > $b['firstName'];
                            });
                        }
                        else{
                            usort($applicants, function($a, $b) {
                                return $a['firstName'] < $b['firstName'];
                            });
                        }
                    }
                    elseif ($params['orderBy'] == 'Email'){
                        if($params['orderSort'] == 'asc'){
                            usort($applicants, function($a, $b) {
                                return $a['email'] > $b['email'];
                            });
                        }
                        else{
                            usort($applicants, function($a, $b) {
                                return $a['email'] < $b['email'];
                            });
                        }
                    }
                    elseif ($params['orderBy'] == 'Phone'){
                        if($params['orderSort'] == 'asc'){
                            usort($applicants, function($a, $b) {
                                return $a['phone'] > $b['phone'];
                            });
                        }
                        else{
                            usort($applicants, function($a, $b) {
                                return $a['phone'] < $b['phone'];
                            });
                        }
                    }
                }
            }
        }
        return $applicants;
    }
}
