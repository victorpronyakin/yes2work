<?php
/**
 * Created by PhpStorm.
 * Date: 26.04.18
 * Time: 15:31
 */

namespace AppBundle\Controller\Api\Business;

use AppBundle\Entity\Applicants;
use AppBundle\Entity\CandidateReferences;
use AppBundle\Entity\Job;
use AppBundle\Entity\Opportunities;
use AppBundle\Entity\ProfileDetails;
use AppBundle\Entity\Settings;
use AppBundle\Entity\User;
use AppBundle\Entity\ViewUniqueProfile;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class CandidateController
 * @package AppBundle\Controller\Api\Business
 *
 * @Rest\Route("candidate")
 * @Security("has_role('ROLE_CLIENT')")
 */
class CandidateController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Get("/")
     * @SWG\Get(path="/api/business/candidate/",
     *   tags={"Business Candidate"},
     *   security={true},
     *   summary="Get Candidate Who satisfy the criteria",
     *   description="The method for getting Candidate Who satisfy the criteria for business",
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
     *      default=10,
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
     *      name="articlesFirm",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by articlesFirm."
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
     *      name="nationality",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="Sort by nationality. All, 1 = South African Citizens, 2=Other"
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
     *      name="qualification",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="All",
     *      description="All, 1 = Fully Qualified CAs, 2 = Part Qualified CAs"
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
     *      name="articlesCompletedStart",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="sort by dateArticlesCompleted"
     *   ),
     *   @SWG\Parameter(
     *      name="articlesCompletedEnd",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="sort by dateArticlesCompleted"
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              type="array",
     *              property="items",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                      example=0
     *                  ),
     *                  @SWG\Property(
     *                      property="firstName",
     *                      type="string",
     *                      example="firstName"
     *                  ),
     *                  @SWG\Property(
     *                      property="lastName",
     *                      type="string",
     *                      example="lastName"
     *                  ),
     *                  @SWG\Property(
     *                      property="articlesFirm",
     *                      type="string",
     *                      example="articlesFirm"
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
     *                      description="if false see availabilityPeriod, else immediately",
     *                  ),
     *                  @SWG\Property(
     *                      property="availabilityPeriod",
     *                      type="integer",
     *                      description="
     *                          1=30 Day notice period
     *                          2=60 Day notice period
     *                          3=90 Day notice period
     *                          4=I can provide a specific date (see dateAvailability)"
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
     *                      property="employer",
     *                      type="string",
     *                      example="employer",
     *                  ),
     *                  @SWG\Property(
     *                      property="role",
     *                      type="string",
     *                      example="role",
     *                  ),
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
    public function getCandidateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $candidates = $em->getRepository("AppBundle:ProfileDetails")->getCandidateWithCriteriaWithVisibleNew($request->query->all());
        $items = $this->generateCandidatesData($em, $candidates);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $items,
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Get("/count")
     * @SWG\Get(path="/api/business/candidate/count",
     *   tags={"Business Candidate"},
     *   security={true},
     *   summary="Get Count Candidate Who satisfy the criteria",
     *   description="The method for getting Count Candidate Who satisfy the criteria for business",
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
	 *      description="Sort by yearsOfWorkExperience. 1 = 0, 2 = 0-1, 3 = 1-2, 4 = 3-5, 5 = 5+"
	 *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="array",
     *          @SWG\Items(
     *              type="object",
     *              @SWG\Property(
     *                  property="countCandidate",
     *                  type="string",
     *                  example="5"
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
    public function getCountCandidateAction(Request $request){
		$params = $request->query->all();
    	$em = $this->getDoctrine()->getManager();
        $candidates = $em->getRepository("AppBundle:ProfileDetails")->getCountCandidateWithCriteria($request->query->all(),true);
		$candidates_n = 0;
		$candidates_ids = [];
		if(!empty($candidates)) {
			//	highestQualification - filter
			if (isset($params['highestQualification']) && $params['highestQualification'] != 'null' && $params['highestQualification'] != NULL && $params['highestQualification'] != 'All') {
				if (is_array($params['highestQualification'])) {
					$highestQualification = $params['highestQualification'];
				} else {
					$highestQualification = explode(',', $params['highestQualification']);
				}
				if (in_array('NQF 4 - Matric', $highestQualification)) {
					$highestQualification = array('NQF 4 - Matric');
				}
			} else {
				$highestQualification = array();
			}
			// yearsOfWorkExperience - filter
			if (isset($params['yearsOfWorkExperience']) && $params['yearsOfWorkExperience'] != 'null' && $params['yearsOfWorkExperience'] != NULL && $params['yearsOfWorkExperience'] != 'All' ) {
				/* $yearsOfWorkExperience
				1 0
				2 0-1
				3 1-2
				4 3-5
				5 5+
				*/
				if (is_array($params['yearsOfWorkExperience'])) {
					$yearsOfWorkExperience = $params['yearsOfWorkExperience'];
				} else {
					$yearsOfWorkExperience = explode(',', $params['yearsOfWorkExperience']);
				}
			} else {
				$yearsOfWorkExperience = array();
			}
			foreach ($candidates as $key => $candidate) {
				if(in_array($candidate['candidateID'], $candidates_ids)){
					continue;
				}
				$candidates_ids[] = $candidate['candidateID'];

				$candidate['yearsOfWorkExperience'] = $this->getYearsOfWorkExperienceForCandidate($em,$candidate['candidateID']);
				$candidate['highestQualification'] = $this->getHighestQualificationForCandidate($em,$candidate['candidateID']);

				$filter_ok = true;
				if (!empty($highestQualification)) {
					if (!in_array($candidate['highestQualification'], $highestQualification)) {
						$filter_ok = false;
					}
				}
				if (!empty($yearsOfWorkExperience) && $filter_ok == true) {
                    $filter_ok = false;
                    /* $yearsOfWorkExperience
                    1 0
                    2 0-1
                    3 1-2
                    4 3-5
                    5 5+
                    */
					if (in_array(1, $yearsOfWorkExperience) && $candidate['yearsOfWorkExperience'] == 0) {
						$filter_ok = true;
					} elseif (in_array(2, $yearsOfWorkExperience) && ($candidate['yearsOfWorkExperience'] == 0 || $candidate['yearsOfWorkExperience'] == 1)) {
						$filter_ok = true;
					} elseif (in_array(3, $yearsOfWorkExperience) && ($candidate['yearsOfWorkExperience'] == 1 || $candidate['yearsOfWorkExperience'] == 2)) {
						$filter_ok = true;
					} elseif (in_array(4, $yearsOfWorkExperience) && ($candidate['yearsOfWorkExperience'] > 2 && $candidate['yearsOfWorkExperience'] < 6)) {
						$filter_ok = true;
					} elseif (in_array(5, $yearsOfWorkExperience) && $candidate['yearsOfWorkExperience'] > 5) {
						$filter_ok = true;
					}
				}

                if ($filter_ok == true) {
					$candidates_n++;
				}
			}
		}
		$view = $this->view(['countCandidate'=>$candidates_n], Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Rest\Get("/{id}", requirements={"id"="\d+"})
     * @SWG\Get(path="/api/business/candidate/{id}",
     *   tags={"Business Candidate"},
     *   security={true},
     *   summary="Get Candidate By Id",
     *   description="The method for getting Candidate by Id for business",
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
     *      description="candidate ID"
     *   ),
     *   @SWG\Parameter(
     *      name="jobID",
     *      in="query",
     *      required=false,
     *      type="integer",
     *      description="if have jobID send jobID"
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
	 *			@SWG\Property(
	 *				property="applicable",
	 *              type="bool",
	 *              example="true or false"
	 *          ),
     *          @SWG\Property(
     *              type="object",
     *              property="details",
     *              @SWG\Property(
     *                  property="id",
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
     *                  property="articlesFirmName",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="nationality",
     *                  type="integer",
     *                  description="1=South African, 2=Other",
     *                  example=1,
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
     *                  property="criminalDescription",
     *                  type="string",
     *                  description="for criminal out more"
     *              ),
     *              @SWG\Property(
     *                  property="credit",
     *                  type="boolean",
     *                  description="true=Yes, false=No"
     *              ),
     *              @SWG\Property(
     *                  property="creditDescription",
     *                  type="string",
     *                  description="for credit out more"
     *              ),
	 *				@SWG\Property(
	 *					property="driverLicense",
	 *          	    type="bool",
	 *          	    example="true or false"
	 *          	),
	 *              @SWG\Property(
	 *                  property="englishProficiency",
	 *                  type="integer",
	 *                  description="1 = Below Average 2 = Average 3 = Good 4 = Exceptional"
	 *              ),
	 *				@SWG\Property(
	 *					property="mostSalary",
	 *          	    type="integer",
	 *          	    example="123123"
	 *          	),
	 *				@SWG\Property(
	 *					property="salaryPeriod",
	 *          	    type="bool",
	 *          	    example="monthly or annual"
	 *          	),
     *              @SWG\Property(
     *                  property="mostRole",
     *                  type="string",
     *              ),
     *              @SWG\Property(
     *                  property="mostEmployer",
     *                  type="string",
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
	 *     			@SWG\Property(
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
     *                  ),
     *              ),
     *              @SWG\Property(
     *                  property="picture",
     *                  type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(
     *                          property="url",
     *                          type="string",
     *                          example="url file"
     *                      ),
     *                      @SWG\Property(
     *                          property="name",
     *                          type="string",
     *                          example="name file"
     *                      ),
     *                      @SWG\Property(
     *                          property="size",
     *                          type="integer",
     *                          example=1111
     *                      ),
     *                      @SWG\Property(
     *                          property="approved",
     *                          type="boolean",
     *                          example=false
     *                      ),
     *                  )
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
     *          ),
     *          @SWG\Property(
     *              property="applicant",
     *              type="integer",
     *              description="WHEN TO SHOW WHICH BUTTON:
     *                              0=New (Only Button 'ShortList', 'Set Up Interview', NOT Message);
     *                              1=Awaiting approve (Only Button 'ShortList', 'Set Up Interview', 'Decline', NOT Message);
     *                              2=Already added ShortList (Button Set Up Interview, Decline)
     *                              3=Already interview set up (Only Message NOT Button)
     *                              4=Candidate was declined (Button 'ShortList', 'Set Up Interview' and Message )
     *                              5=Already added ShortList (Only Button Set Up Interview, Not Message)
     *                              6=Interview awaits the candidate's approval (Button 'Cancel' and Message)
     *                              7=Interview was declined by the candidate (Only Message, Not Button)
     *              "
     *          ),
     *          @SWG\Property(
     *              property="jobID",
     *              type="integer",
     *          )
     *      ),
	 * 		@SWG\Property(
	 *      	property="qualifications",
	 *          type="array",
	 *          @SWG\Items(type="string")
	 *		),
	 * 		@SWG\Property(
	 *      	property="employments",
	 *          type="array",
	 *          @SWG\Items(type="string")
	 *		),
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
    public function getCandidateByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $candidate = $em->getRepository("AppBundle:User")->find($id);
        if($candidate instanceof User){
            $candidateDetails = $em->getRepository("AppBundle:ProfileDetails")->getCandidateByIdForBusiness($id);
            $achievements = $em->getRepository("AppBundle:CandidateAchievements")->getAchievementsCandidate($id);
            $references = $em->getRepository("AppBundle:CandidateReferences")->getReferencesCandidate($id, false);
			$candidateQualifications = $em->getRepository("AppBundle:CandidateQualifications")->findBy(["user" => $candidate]);


			$employments = [];//[Jan 2012 - Mar 2012]: [Company Name] - [Role]
			for($i=0;$i<sizeof($references);$i++){
				$employments[] = "{$references[$i]['startDate']->format('M Y')} - {$references[$i]['endDate']->format('M Y')}: {$references[$i]['company']} - {$references[$i]['role']}";
			}

			$has_q_type_1 = false;
			$qualifications = [];
			for($i=0;$i<sizeof($candidateQualifications);$i++){
				if($i > 4){break;} //max 5 elements
				if($candidateQualifications[$i]->getType() === 3){
					$univer_name = $candidateQualifications[$i]->getTertiaryInstitution();
					$univer_name = (isset($univer_name) && !empty($univer_name) && $univer_name != 'Other') ? $univer_name : $candidateQualifications[$i]->getTertiaryInstitutionCustom();

					$specific_q = $candidateQualifications[$i]->getSpecificQ();
					$specific_q = (isset($specific_q) && !empty($specific_q) && $specific_q != 'Other') ? $specific_q : $candidateQualifications[$i]->getSpecificQCustom();
					$education = $candidateQualifications[$i]->getEducation();
					$start_date_tmp = $candidateQualifications[$i]->getStartYear()->format('M Y');
					$end_date_tmp = $candidateQualifications[$i]->getEndYear()->format('M Y');
					$qualifications[] = "{$start_date_tmp} - {$end_date_tmp}: {$univer_name} - {$specific_q} ({$education})";
				}elseif ($candidateQualifications[$i]->getType() === 1){
					$school_name_tmp = $candidateQualifications[$i]->getSchoolName();
					$year_tmp = $candidateQualifications[$i]->getMatriculatedYear();
					$has_q_type_1 = true;
					$qualifications[] = "{$year_tmp}: Matric - {$school_name_tmp}";
				}elseif ($candidateQualifications[$i]->getType() === 2){
					$qualifications[] = 'Gr.8';
				}
			}
			if($has_q_type_1 === true){
				for($i=0;$i<sizeof($qualifications);$i++){if($qualifications[$i] === 'Gr.8'){unset($qualifications[$i]);}}
			}


            //New (Only Button "ShortList", "Set Up Interview", NOT Message)
            $status = 0;
            $jobID = null;
            if($request->query->has('jobID') && $request->query->get('jobID') > 0){
                $job = $em->getRepository('AppBundle:Job')->find($request->query->get('jobID'));
                if($job instanceof Job){
                    $applicantCheck = $em->getRepository("AppBundle:Applicants")->findOneBy(['client'=>$this->getUser(),'candidate'=>$candidate, 'job'=>$job]);
                    $opportunityCheck = $em->getRepository("AppBundle:Opportunities")->findOneBy(['client'=>$this->getUser(), 'candidate'=>$candidate, 'job'=>$job]);
                }
                else{
                    $view = $this->view(['error'=>'Job Not Found'], Response::HTTP_NOT_FOUND);
                    return $this->handleView($view);
                }
            }
            else{
                $applicantCheck = $em->getRepository("AppBundle:Applicants")->findOneBy(['client'=>$this->getUser(),'candidate'=>$candidate, 'job'=>$jobID]);
                $opportunityCheck = $em->getRepository("AppBundle:Opportunities")->findOneBy(['client'=>$this->getUser(), 'candidate'=>$candidate, 'job'=>$jobID]);
            }

            if($applicantCheck instanceof Applicants && $opportunityCheck instanceof Opportunities){
                if($opportunityCheck->getStatus() == 1){
                    //Interview awaits the candidate's approval (Button "Cancel" and Message)
                    $status = 6;
                    $jobID = ($opportunityCheck->getJob() instanceof Job) ? $opportunityCheck->getJob()->getId() : null;
                }
                elseif ($opportunityCheck->getStatus() == 2){
                    //Interview was declined by the candidate (Only Message, Not Button)
                    $status = 7;
                    $jobID = ($opportunityCheck->getJob() instanceof Job) ? $opportunityCheck->getJob()->getId() : null;
                }
                elseif($applicantCheck->getStatus() == 1){
                    //Awaiting approve (Only Button "ShortList", "Set Up Interview", "Decline", NOT Message)
                    $status = 1;
                    $jobID = ($applicantCheck->getJob() instanceof Job) ? $applicantCheck->getJob()->getId() : null;
                }
                elseif($applicantCheck->getStatus() == 2){
                    if($applicantCheck->getCheck() == true){
                        //Already added ShortList (Button Set Up Interview, Decline, Shortlist checked)
                        $status = 2;
                        $jobID = ($applicantCheck->getJob() instanceof Job) ? $applicantCheck->getJob()->getId() : null;
                    }
                    else{
                        //Already added ShortList (Only Button Set Up Interview, Remove from ShortList, Not Message)
                        $status = 5;
                        $jobID = ($applicantCheck->getJob() instanceof Job) ? $applicantCheck->getJob()->getId() : null;
                    }
                }
                elseif($applicantCheck->getStatus() == 3){
                    //Already interview set up (Only Message NOT Button)
                    $status = 3;
                    $jobID = ($applicantCheck->getJob() instanceof Job) ? $applicantCheck->getJob()->getId() : null;
                }
                elseif ($applicantCheck->getStatus() == 4){
                    //Candidate was declined (Button "ShortList", "Set Up Interview" and Message )
                    $status = 4;
                    $jobID = ($applicantCheck->getJob() instanceof Job) ? $applicantCheck->getJob()->getId() : null;
                }
            }
            elseif ($applicantCheck instanceof Applicants){
                if($applicantCheck->getStatus() == 1){
                    //Awaiting approve (Only Button "ShortList", "Set Up Interview", "Decline", NOT Message)
                    $status = 1;
                    $jobID = ($applicantCheck->getJob() instanceof Job) ? $applicantCheck->getJob()->getId() : null;
                }
                elseif($applicantCheck->getStatus() == 2){
                    if($applicantCheck->getCheck() == true){
                        //Already added ShortList (Button Set Up Interview, Decline, Shortlist checked)
                        $status = 2;
                        $jobID = ($applicantCheck->getJob() instanceof Job) ? $applicantCheck->getJob()->getId() : null;
                    }
                    else{
                        //Already added ShortList (Only Button Set Up Interview, Remove from ShortList, Not Message)
                        $status = 5;
                        $jobID = ($applicantCheck->getJob() instanceof Job) ? $applicantCheck->getJob()->getId() : null;
                    }
                }
                elseif($applicantCheck->getStatus() == 3){
                    //Already interview set up (Only Message NOT Button)
                    $status = 3;
                    $jobID = ($applicantCheck->getJob() instanceof Job) ? $applicantCheck->getJob()->getId() : null;
                }
                elseif ($applicantCheck->getStatus() == 4){
                    //Candidate was declined (Button "ShortList", "Set Up Interview" and Message )
                    $status = 4;
                    $jobID = ($applicantCheck->getJob() instanceof Job) ? $applicantCheck->getJob()->getId() : null;
                }
            }
            elseif ($opportunityCheck instanceof Opportunities){
                if($opportunityCheck->getStatus() == 1){
                    //Interview awaits the candidate's approval (Button "Cancel" and Message)
                    $status = 6;
                    $jobID = ($opportunityCheck->getJob() instanceof Job) ? $opportunityCheck->getJob()->getId() : null;
                }
                elseif ($opportunityCheck->getStatus() == 2){
                    //Interview was declined by the candidate (Only Message, Not Button)
                    $status = 7;
                    $jobID = ($opportunityCheck->getJob() instanceof Job) ? $opportunityCheck->getJob()->getId() : null;
                }
            }

			$applicable = (isset($candidateDetails['ethnicity']) && $candidateDetails['ethnicity'] !== 'White' && $candidateDetails['ethnicity'] !== 'Foreign National')? true : false;

			$view = $this->view(
                [
                    'applicable'=>$applicable,
                    'details'=>$candidateDetails,
                    'achievements'=>$achievements,
                    'references'=>$references,
                    'applicant'=>$status,
                    'qualifications'=>array_values($qualifications),
                    'employments'=>$employments,
                    'jobID'=>$jobID
                ],
                Response::HTTP_OK
            );
        }
        else{
            $view = $this->view(['error'=>'Candidate Not Found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/browse")
     * @SWG\Get(path="/api/business/candidate/browse",
     *   tags={"Business Candidate"},
     *   security={true},
     *   summary="Get All Info need to browse candidate",
     *   description="The method for getting All Info need to browse candidate for business",
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
     *              property="candidates",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="id",
     *                      type="integer",
     *                      example=0
     *                  ),
     *                  @SWG\Property(
     *                      property="firstName",
     *                      type="string",
     *                      example="firstName"
     *                  ),
     *                  @SWG\Property(
     *                      property="lastName",
     *                      type="string",
     *                      example="lastName"
     *                  ),
     *                  @SWG\Property(
     *                      property="articlesFirm",
     *                      type="string",
     *                      example="articlesFirm"
     *                  ),
     *                  @SWG\Property(
     *                      property="boards",
     *                      type="integer",
     *                      example=1,
     *                      description="1 = Passed Both Board Exams First Time, 2 = Passed Both Board Exams, 3 = ITC passed, APC Outstanding, 4 = ITC Outstanding"
     *                  ),
     *                  @SWG\Property(
     *                      property="picture",
     *                      type="array",
     *                      @SWG\Items(
     *                          type="object",
     *                          @SWG\Property(
     *                              property="url",
     *                              type="string",
     *                              example="url file"
     *                          ),
     *                          @SWG\Property(
     *                              property="name",
     *                              type="string",
     *                              example="name file"
     *                          ),
     *                          @SWG\Property(
     *                              property="size",
     *                              type="integer",
     *                              example=1111
     *                          ),
     *                          @SWG\Property(
     *                              property="approved",
     *                              type="boolean",
     *                              example=false
     *                          ),
     *                      )
     *                  ),
     *                  @SWG\Property(
     *                      property="availability",
     *                      type="boolean",
     *                      example=false,
     *                      description="true=immediate, false=see dateAvailability"
     *                  ),
     *                  @SWG\Property(
     *                      property="dateAvailability",
     *                      type="date",
     *                      example="2018-09-09"
     *                  ),
     *              ),
     *          ),
     *          @SWG\Property(
     *              property="jobs",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(
     *                      property="id",
     *                      type="integer"
     *                  ),
     *                  @SWG\Property(
     *                      property="jobTitle",
     *                      type="string",
     *                      example="jobTitle"
     *                  ),
     *                  @SWG\Property(
     *                      property="articlesFirm",
     *                      type="array",
     *                      @SWG\Items(type="string"),
     *                      example={"BDO","Deloitte"},
     *                      description="required."
     *                  ),
     *                  @SWG\Property(
     *                      property="gender",
     *                      type="string",
     *                      example="All",
     *                      description="required. All OR Male OR Female"
     *                  ),
     *                  @SWG\Property(
     *                      property="ethnicity",
     *                      type="string",
     *                      example="None",
     *                      description="required. None OR Black OR White Or Coloured Or Indian Or Oriental"
     *                  ),
     *                  @SWG\Property(
     *                      property="qualification",
     *                      type="integer",
     *                      example=0,
     *                      description="required. 0 = All, 1 = Fully Qualified CAs, 2 = Part Qualified CAs"
     *                  ),
     *                  @SWG\Property(
     *                      property="nationality",
     *                      type="integer",
     *                      example=0,
     *                      description="required. 0 = All, 1 = South African Citizens"
     *                  ),
     *                  @SWG\Property(
     *                      property="video",
     *                      type="integer",
     *                      example=0,
     *                      description="required. 0 = All, 1 = With Video"
     *                  ),
     *                  @SWG\Property(
     *                      property="availability",
     *                      type="integer",
     *                      example=0,
     *                      description="required. 0 = All, 1 = Immediately, 2 = Within 1 month,3 = Within 3 month"
     *                  ),
     *                  @SWG\Property(
     *                      property="postArticles",
     *                      type="integer",
     *                      example=0,
     *                      description="NOT required. 0 = All, 1 = Newly qualified, 2 = 1-3 years,3 = > 3 years"
     *                  ),
     *                  @SWG\Property(
     *                      property="salaryRange",
     *                      type="integer",
     *                      example=0,
     *                      description="NOT required. 0 = None, 1 = 700K, 2 = 700K-1 million,3 = >1 million"
     *                  ),
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
    public function browseCandidateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $candidates = $em->getRepository("AppBundle:ProfileDetails")->getCandidateWithCriteriaWithVisible();
        $jobs = $em->getRepository("AppBundle:Job")->getClientJobsWithCriteria($user->getId());

        $view = $this->view(['candidates'=>$candidates, 'jobs'=>$jobs], Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     *
     * @Rest\Patch("/{id}/stats", requirements={"id"="\d+"})
     * @SWG\Patch(path="/api/business/candidate/{id}/stats",
     *   tags={"Business Candidate"},
     *   security={true},
     *   summary="Set Stats Candidate Profile",
     *   description="The method for Set Stats Candidate Profile for business",
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
     *              property="action",
     *              type="string",
     *              description="required, only view or play"
     *          ),
     *     )
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success.",
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
     *      description="NOT found",
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
    public function setStatsCandidateAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $candidate = $em->getRepository("AppBundle:User")->find($id);
        if($candidate instanceof User){
            $profileDetais = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$candidate]);
            if($profileDetais instanceof ProfileDetails){
                if($request->request->has('action') && !empty($request->request->get('action'))){
                    if($request->request->get('action') == 'view'){
                        $viewUnique = $em->getRepository("AppBundle:ViewUniqueProfile")->findOneBy(['client'=>$user,'candidate'=>$candidate]);
                        if(!$viewUnique instanceof ViewUniqueProfile){
                            $viewUnique = new ViewUniqueProfile($user, $candidate);
                            $em->persist($viewUnique);
                        }
                        $profileDetais->setView($profileDetais->getView()+1);
                        $em->persist($profileDetais);
                        $em->flush();
                        $view = $this->view([], Response::HTTP_NO_CONTENT);
                    }
                    elseif ($request->request->get('action') == 'play'){
                        $profileDetais->setPlay($profileDetais->getPlay()+1);
                        $em->persist($profileDetais);
                        $em->flush();
                        $view = $this->view([], Response::HTTP_NO_CONTENT);
                    }
                    else{
                        $view = $this->view(['error'=>'action should be view or play'], Response::HTTP_BAD_REQUEST);
                    }
                }
                else{
                    $view = $this->view(['error'=>'action is required'], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>'candidate not found'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'candidate not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * @param EntityManager $em
     * @param array $candidates
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function generateCandidatesData(EntityManager $em, array $candidates){
        $result = [];
        if(!empty($candidates)){
            $settings = $em->getRepository('AppBundle:Settings')->findOneBy([]);
            if(!$settings instanceof Settings){
                $settings = new Settings(false);
                $em->persist($settings);
                $em->flush();
            }
            foreach ($candidates as $key=>$candidate){
                if($candidate instanceof ProfileDetails){
//                    if($settings->getAllowVideo() == true || (isset($candidate->getVideo()['approved']) && $candidate->getVideo()['approved'] == true)){
                        /*$cvFiles = [];
                        if(!empty($candidate->getCvFiles())){
                            foreach ($candidate->getCvFiles() as $cvFile){
                                if(isset($cvFile['approved']) && $cvFile['approved'] == true){
                                    $cvFiles[] = $cvFile;
                                }
                            }
                        }*/
                        //if(!empty($cvFiles)){
                            $result[$key] = [
                                'id' => $candidate->getUser()->getId(),
                                'firstName' => $candidate->getUser()->getFirstName(),
                                'lastName' => $candidate->getUser()->getLastName(),
                                'articlesFirm' => $candidate->getArticlesFirm(),
                                'articlesFirmName' => $candidate->getArticlesFirmName(),
                                'dateArticlesCompleted' => $candidate->getDateArticlesCompleted(),
                                'availability' => $candidate->getAvailability(),
                                'availabilityPeriod' => $candidate->getAvailabilityPeriod(),
                                'dateAvailability' => $candidate->getDateAvailability(),
                                'picture' => (isset($candidate->getPicture()[0]['url']) && !empty($candidate->getPicture()[0]['url'])) ? $candidate->getPicture()[0]['url'] : NULL,
                                'video' => (isset($candidate->getVideo()['adminUrl']) && !empty($candidate->getVideo()['adminUrl']) && isset($candidate->getVideo()['approved']) && $candidate->getVideo()['approved'] == true) ? $candidate->getVideo()['adminUrl'] : NULL,
                                'cvFiles' => [],
                                'employer' => $candidate->getMostEmployer(),
                                'role' => $candidate->getMostRole(),
                            ];
                        //}
//                    }
                }
            }
        }
        return $result;
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

}
