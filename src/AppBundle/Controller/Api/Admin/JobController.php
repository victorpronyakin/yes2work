<?php
/**
 * Created by PhpStorm.
 * Date: 25.04.18
 * Time: 13:48
 */

namespace AppBundle\Controller\Api\Admin;


use AppBundle\Entity\Applicants;
use AppBundle\Entity\CompanyDetails;
use AppBundle\Entity\HideJob;
use AppBundle\Entity\Interviews;
use AppBundle\Entity\Job;
use AppBundle\Entity\Logging;
use AppBundle\Entity\Opportunities;
use AppBundle\Entity\ProfileDetails;
use AppBundle\Entity\Settings;
use AppBundle\Entity\User;
use AppBundle\Helper\SendEmail;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class JobController
 * @package AppBundle\Controller\Api\Admin
 *
 * @Rest\Route("job")
 * @Security("has_role('ROLE_ADMIN')")
 */
class JobController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     * @throws
     *
     * @Rest\Get("/")
     * @SWG\Get(path="/api/admin/job/",
     *   tags={"Admin Job"},
     *   security={true},
     *   summary="Get All Jobs ",
     *   description="The method for getting all Jobs for admin",
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
     *      default="",
     *      description="search by firstName OR lastName OR email OR phone OR companyName OR jobTitle"
     *   ),
     *   @SWG\Parameter(
     *      name="orderBy",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="DaysToGo, Company, JobTitle, Contact, Email, Phone"
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
     *      name="status",
     *      in="query",
     *      required=false,
     *      type="boolean",
     *      default=true,
     *      description="search by status. true = open , false = close"
     *   ),
     *   @SWG\Parameter(
     *      name="dateStart",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="search by jobDate >= dateStart"
     *   ),
     *   @SWG\Parameter(
     *      name="dateEnd",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="search by jobDate <= dateEnd"
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
     *                  ),
     *                  @SWG\Property(
     *                      property="approve",
     *                      type="boolean"
     *                  ),
     *                  @SWG\Property(
     *                      property="status",
     *                      type="boolean"
     *                  ),
	 *      		   @SWG\Property(
	 *      		       property="closureDate",
	 *      		       type="date"
	 *      		   ),
	 *      		   @SWG\Property(
	 *      		       property="clientID",
	 *      		       type="string"
	 *      		   )
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
    public function getAllJobAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $jobs = $em->getRepository("AppBundle:Job")->getAllJob($request->query->all());

        if($request->query->getBoolean('csv', false) == false){
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $jobs,
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
            if(!empty($jobs)){
                foreach ($jobs as $job){
                    $jobDate = '';
                    if(isset($job['jobDate']) && !empty($job['jobDate'])){
                        if($job['jobDate'] instanceof \DateTime){
                            $jobDate = $job['jobDate']->format('M d');
                        }
                        else{
                            $newJobDate = new \DateTime($job['jobDate']);
                            $jobDate = $newJobDate->format('M d');
                        }
                    }
                    $contact = '';
                    if(isset($job['firstName'])){
                        $contact .= $job['firstName'].' ';
                    }
                    if(isset($job['lastName'])){
                        $contact .= $job['lastName'];
                    }
                    $result[] = [
                        'date' => $jobDate,
                        'contact' => $contact,
                        'email' => (isset($job['email'])) ? $job['email'] : '',
                        'phone' => (isset($job['phone'])) ? $job['phone'] : '',
                        'company' => (isset($job['companyName'])) ? $job['companyName'] : '',
                        'jobTitle' => (isset($job['jobTitle'])) ? $job['jobTitle'] : '',
                        'active' => (isset($job['status'])) ? $job['status'] : '',
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     *
     * @Rest\Post("/")
     * @SWG\Post(path="/api/admin/job/",
     *   tags={"Admin Job"},
     *   security={true},
     *   summary="Create Job",
     *   description="The method for creating new job for Admin",
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
     *              property="jobTitle",
     *              type="string",
     *              example="jobTitle",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="industry",
     *              type="array",
     *              @SWG\Items(type="string")
     *          ),
	 *          @SWG\Property(
	 *              property="industrySecondary",
	 *              type="array",
	 *              @SWG\Items(type="string"),
	 *              description="required."
	 *          ),
     *          @SWG\Property(
     *              property="companyName",
     *              type="string",
     *              example="companyName",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="companyAddress",
     *              type="string",
     *              example="companyAddress",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressCountry",
     *              type="string",
     *              example="addressCountry",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressState",
     *              type="string",
     *              example="addressState",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressZipCode",
     *              type="string",
     *              example="addressZipCode",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressCity",
     *              type="string",
     *              example="addressCity",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressSuburb",
     *              type="string",
     *              example="addressSuburb",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressStreet",
     *              type="string",
     *              example="addressStreet",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressStreetNumber",
     *              type="string",
     *              example="addressStreetNumber",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressBuildName",
     *              type="string",
     *              example="addressBuildName",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressUnit",
     *              type="string",
     *              example="addressUnit",
     *              description="required"
     *          ),
	 *          @SWG\Property(
	 *              property="companyDescription",
	 *              type="string",
	 *              example="companyDescription",
	 *              description="required. Max=300"
	 *          ),
     *          @SWG\Property(
     *              property="roleDescription",
     *              type="string",
     *              example="roleDescription",
     *              description="required. Max=400"
     *          ),
     *          @SWG\Property(
     *              property="closureDate",
     *              type="date",
     *              example="2018-05-10",
     *              description="required. 1 month maximum"
     *          ),
     *          @SWG\Property(
     *              property="jobClosureDate",
     *              type="date",
     *              example="2018-05-10",
     *              description="required."
     *          ),
     *          @SWG\Property(
     *              property="gender",
     *              type="string",
     *              example="All",
     *              description="required. All OR Male OR Female"
     *          ),
     *          @SWG\Property(
     *              property="ethnicity",
     *              type="string",
     *              example="None",
     *              description="required. All OR Black OR White Or Coloured Or Indian Or Oriental"
     *          ),
     *          @SWG\Property(
     *              property="availability",
     *              type="integer",
     *              example=0,
     *              description="required. 0 = All, 1 = Immediately, 2 = Within 1 month,3 = Within 3 month"
     *          ),
     *          @SWG\Property(
     *              property="location",
     *              type="string",
     *              example="location",
     *              description="required. Only All Gauteng or Western Cape or Eastern Cape or KZN"
     *          ),
     *          @SWG\Property(
     *              property="salaryRange",
     *              type="integer",
     *              example=0,
     *              description="NOT SEND. NOT required. 0 = None, 1 = 700K, 2 = 700K-1 million,3 = >1 million"
     *          ),
     *          @SWG\Property(
     *              property="started",
     *              type="date",
     *              example="2018-05-10",
     *              description="required"
     *          ),
	 *          @SWG\Property(
	 *              property="spec",
	 *              type="string",
	 *              example="file.pdf"
	 *          ),
	 *          @SWG\Property(
	 *              property="jobReference",
	 *              type="string",
	 *              example="jobReference",
	 *              description="required. Max=10"
	 *          ),
	 *          @SWG\Property(
	 *              property="typeOfEmployment",
	 *              type="string",
	 *              example="Contract",
	 *              description="required. Only Contract or Permanent or Temporary",
	 *          ),
	 *          @SWG\Property(
	 *              property="timePeriod",
	 *              type="string",
	 *              example="Full Time",
	 *              description="required. Only Full Time or Part Time",
	 *          ),
	 *          @SWG\Property(
	 *              property="salaryFrom",
	 *              type="integer",
	 *              example=0,
	 *          ),
	 *          @SWG\Property(
	 *              property="salaryTo",
	 *              type="integer",
	 *              example=0,
	 *          ),
	 *          @SWG\Property(
	 *              property="video",
	 *              type="integer",
	 *              example=0,
	 *              description="required. 0 = All, 1 = With Video, 2 = Without Video"
	 *          ),
	 *          @SWG\Property(
	 *              property="field",
	 *              type="array",
	 *              @SWG\Items(type="string"),
	 *              description="required."
	 *          ),
	 *   		@SWG\Property(
	 *   		   property="monthSalaryFrom",
	 *   		   type="integer",
	 *             example=0,
	 *   		   description="required. 0 or 3500"
	 *   		),
	 *   		@SWG\Property(
	 *   		   property="monthSalaryTo",
	 *   		   type="integer",
	 *             example=3500,
	 *   		   description="required. 3500"
	 *   		),
	 *   		@SWG\Property(
	 *   		   property="highestQualification",
	 *   		   type="string",
	 *             example="NQF 8 - Honours.",
	 *   		   description="required. NQF 8 - Honours."
	 *   		),
	 *  		@SWG\Property(
	 *  		   property="eligibility",
	 *  		   type="string",
	 *             example="applicable",
	 *  		   description="required. All or applicable"
	 *  		),
	 *  		@SWG\Property(
	 *  		   property="yearsOfWorkExperience",
	 *              type="array",
	 *              @SWG\Items(type="string"),
	 *  		   description="required. 1 = 0, 2 = 0-1, 3 = 1-2, 4 = 3-5, 5 = 5+"
	 *  		),
	 *          @SWG\Property(
	 *  		   property="assessment",
	 *  		   type="string",
	 *             example="applicable",
	 *  		   description="required. All - 1 Yes - 2 No - 3"
	 *  		),
     *          @SWG\Property(
     *              property="filled",
     *              type="date",
     *              example="2018-05-10",
     *              description="not required"
     *          ),
     *      ),
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success. Job Created"
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
    public function createJobAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        if(!empty($request->request->all())){
            if($request->request->has('clientID') && !empty($request->request->get('clientID'))){
                $user = $em->getRepository("AppBundle:User")->find($request->request->get('clientID'));
                if($user instanceof User && $user->hasRole("ROLE_CLIENT")){
                    $job = new Job($user, $request->request->all());

                    $errors = $this->get('validator')->validate($job, null, array('Jobs'));
                    if(count($errors) === 0){
                        if($request->files->has('spec')){
                            $fileUpload = $request->files->get('spec');
                            if($fileUpload instanceof UploadedFile){
                                $fileName = md5(uniqid()).'.'.$fileUpload->getClientOriginalExtension();
                                try {
                                    $fileUpload->move("uploads/client/".$user->getId()."/job",$fileName);
                                    $file = [
                                        'url' => $request->getSchemeAndHttpHost()."/uploads/client/".$user->getId()."/job/".$fileName,
                                        'adminUrl' => $request->getSchemeAndHttpHost()."/uploads/client/".$user->getId()."/job/".$fileName,
                                        'name'=>$fileUpload->getClientOriginalName(),
                                        'size'=>$fileUpload->getClientSize(),
                                        'time'=>time(),
                                        'approved'=>true
                                    ];

                                    $job->setSpec($file);
                                } catch (\Exception $e) {
                                    $view = $this->view(['error'=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                                    return $this->handleView($view);
                                }
                            }
                        }
                        $job->setCompanyDescriptionChange($job->getCompanyDescription());
                        $job->setRoleDescriptionChange($job->getRoleDescription());
                        $job->setApprove(true);
                        $em->persist($job);
                        $em->flush();
                        $logging = new Logging($this->getUser(),17, $job->getJobTitle(), $job->getId());
                        $em->persist($logging);
                        $em->flush();
                        $candidatesCriteria = $em->getRepository("AppBundle:ProfileDetails")->getCandidateWithCriteria([
                            'gender' => $job->getGender(),
                            'ethnicity' => $job->getEthnicity(),
                            'location' => $job->getLocation(),
                            'availability' => $job->getAvailability()
                        ]);
                        if(!empty($candidatesCriteria)){
                            $companyDetails = $em->getRepository("AppBundle:CompanyDetails")->findOneBy(['user'=>$job->getUser()]);
                            foreach ($candidatesCriteria as $candidatesCriterion){
                                if($candidatesCriterion instanceof ProfileDetails){
                                    if($candidatesCriterion->getPercentage() > 50
                                        && $candidatesCriterion->getLooking() == true
                                        && !empty($candidatesCriterion->getVideo())
                                        && isset($candidatesCriterion->getVideo()['approved'])
                                        && $candidatesCriterion->getVideo()['approved'] == true
                                    ){
                                        $emailData = [
                                            'user' => ['firstName'=>$candidatesCriterion->getUser()->getFirstName()],
                                            'jse' => ($companyDetails instanceof CompanyDetails && is_bool($companyDetails->getJse())) ? $companyDetails->getJse() : false,
                                            'industry' => implode(', ',$job->getIndustry()),
                                            'jobTitle'=>$job->getJobTitle(),
                                            'city'=>$job->getAddressCity(),
                                            'link'=>$request->getSchemeAndHttpHost().'/candidate/job_alerts_new',
                                            'closureDate'=>$job->getClosureDate()
                                        ];
                                        $message = (new \Swift_Message('A new Yes2Work job has just been opened – are you interested?'))
                                            ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                                            ->setTo($job->getUser()->getEmail())
                                            ->setBody(
                                                $this->renderView('emails/candidate/new_job_loaded.html.twig',
                                                    $emailData
                                                ),
                                                'text/html'
                                            );

                                        SendEmail::sendEmailForCandidate($candidatesCriterion->getUser(), $em, $message, $this->get('mailer'), $emailData, SendEmail::TYPE_CANDIDATE_NEW_JOB_LOADED);

                                    }
                                }
                            }
                        }
                        $view = $this->view([], Response::HTTP_NO_CONTENT);
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
                    $view = $this->view(['error'=>'Client not Found or User not have ROLE_CLIENT'], Response::HTTP_NOT_FOUND);
                }
            }
            else{
                $view = $this->view(['error'=>'clientID is required'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'All fields required'], Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     *
     * @Rest\Get("/{id}",requirements={"id"="\d+"})
     * @SWG\Get(path="/api/admin/job/{id}",
     *   tags={"Admin Job"},
     *   security={true},
     *   summary="Get Job By Id",
     *   description="The method for getting job by id for admin",
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
     *      description="Job ID"
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="id",
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="jobTitle",
     *              type="string",
     *              example="jobTitle",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="industry",
     *              type="array",
     *              @SWG\Items(type="string")
     *          ),
	 *          @SWG\Property(
	 *              property="industrySecondary",
	 *              type="array",
	 *              @SWG\Items(type="string"),
	 *              description="required."
	 *          ),
     *          @SWG\Property(
     *              property="companyName",
     *              type="string",
     *              example="companyName",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="companyAddress",
     *              type="string",
     *              example="companyAddress",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressCountry",
     *              type="string",
     *              example="addressCountry",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressState",
     *              type="string",
     *              example="addressState",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressZipCode",
     *              type="string",
     *              example="addressZipCode",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressCity",
     *              type="string",
     *              example="addressCity",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressSuburb",
     *              type="string",
     *              example="addressSuburb",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressStreet",
     *              type="string",
     *              example="addressStreet",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressStreetNumber",
     *              type="string",
     *              example="addressStreetNumber",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressBuildName",
     *              type="string",
     *              example="addressBuildName",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressUnit",
     *              type="string",
     *              example="addressUnit",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="companyDescription",
     *              type="string",
     *              example="companyDescription",
     *              description="required. Min=50,Max=200"
     *          ),
     *          @SWG\Property(
     *              property="companyDescriptionChange",
     *              type="string",
     *              example="companyDescriptionChange",
     *              description="if null use companyDescription"
     *          ),
     *          @SWG\Property(
     *              property="roleDescription",
     *              type="string",
     *              example="roleDescription",
     *              description="required. Max=400"
     *          ),
     *          @SWG\Property(
     *              property="roleDescriptionChange",
     *              type="string",
     *              example="roleDescriptionChange",
     *              description="if null use roleDescription"
     *          ),
     *          @SWG\Property(
     *              property="closureDate",
     *              type="date",
     *              example="2018-05-10",
     *              description="required. 1 month maximum"
     *          ),
     *          @SWG\Property(
     *              property="jobClosureDate",
     *              type="date",
     *              example="2018-05-10",
     *              description="required. 1 month maximum"
     *          ),
     *          @SWG\Property(
     *              property="gender",
     *              type="string",
     *              example="All",
     *              description="required. All OR Male OR Female"
     *          ),
     *          @SWG\Property(
     *              property="ethnicity",
     *              type="integer",
     *              example=0,
     *              description="required. All OR Black OR White Or Coloured Or Indian Or Oriental"
     *          ),
     *          @SWG\Property(
     *              property="availability",
     *              type="integer",
     *              example=0,
     *              description="required. 0 = All, 1 = Immediately, 2 = Within 1 month,3 = Within 3 month"
     *          ),
     *          @SWG\Property(
     *              property="location",
     *              type="string",
     *              example="location",
     *              description="required. Only All or Gauteng or Western Cape or Eastern Cape or KZN"
     *          ),
     *          @SWG\Property(
     *              property="salaryRange",
     *              type="integer",
     *              example=0,
     *              description="NOT required. 0 = None, 1 = 700K, 2 = 700K-1 million,3 = >1 million"
     *          ),
     *          @SWG\Property(
     *              property="approve",
     *              type="boolean",
     *              example=true,
     *              description="true=is approve, false=is decline. null=not considered"
     *          ),
     *          @SWG\Property(
     *              property="status",
     *              type="boolean",
     *              example=true,
     *              description="required.true=open,false=close"
     *          ),
     *          @SWG\Property(
     *              property="createdDate",
     *              type="date",
     *              example="2018-04-10",
     *              description="required."
     *          ),
     *          @SWG\Property(
     *              property="started",
     *              type="date",
     *              example="2018-04-10"
     *          ),
     *          @SWG\Property(
     *              property="filled",
     *              type="date",
     *              example="2018-04-10"
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
	 *          @SWG\Property(
	 *              property="video",
	 *              type="integer",
	 *              example=0,
	 *              description="required. 0 = All, 1 = With Video, 2 = Without Video"
	 *          ),
	 *          @SWG\Property(
	 *              property="field",
	 *              type="array",
	 *              @SWG\Items(type="string"),
	 *              description="required."
	 *          ),
	 *   		@SWG\Property(
	 *   		   property="monthSalaryFrom",
	 *   		   type="integer",
	 *             example=0,
	 *   		   description="required. 0 or 3500"
	 *   		),
	 *   		@SWG\Property(
	 *   		   property="monthSalaryTo",
	 *   		   type="integer",
	 *             example=3500,
	 *   		   description="required. 3500"
	 *   		),
	 *   		@SWG\Property(
	 *   		   property="highestQualification",
	 *   		   type="string",
	 *             example="NQF 8 - Honours.",
	 *   		   description="required. NQF 8 - Honours."
	 *   		),
	 *  		@SWG\Property(
	 *  		   property="eligibility",
	 *  		   type="string",
	 *             example="applicable",
	 *  		   description="required. All or applicable"
	 *  		),
	 *          @SWG\Property(
	 *  		   property="assessment",
	 *  		   type="string",
	 *             example="applicable",
	 *  		   description="required. All - 1 Yes - 2 No - 3"
	 *  		),
	 *  		@SWG\Property(
	 *  		   property="yearsOfWorkExperience",
	 *              type="array",
	 *              @SWG\Items(type="string"),
	 *  		   description="required. 1 = 0, 2 = 0-1, 3 = 1-2, 4 = 3-5, 5 = 5+"
	 *  		),
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
    public function getJobByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
		$job = $em->getRepository("AppBundle:Job")->getJobById($id);
		$job = $em->getRepository("AppBundle:Job")->findOneBy(['id'=>$id]);
        if($job instanceof Job){

            $params = [];
			$params['gender'] = $job->getGender();
			$params['ethnicity'] = $job->getEthnicity();
			$params['availability'] = $job->getAvailability();
			$params['location'] = $job->getLocation();

			$video = ['All','Yes','No'];
			if(empty($job->getVideo())){
                $params['video'] = 'All';
            }
			else{
                $params['video'] = $video[$job->getVideo()];
            }

            $params['field'] = (is_array($job->getField()))?$job->getField():[$job->getField()];
			$params['monthSalaryFrom'] = $job->getSalaryFrom();
			$params['monthSalaryTo'] = $job->getSalaryTo();
			$params['highestQualification'] = $job->getHighestQualification();
			$params['eligibility'] = $job->getEligibility();
			$params['yearsOfWorkExperience'] = $job->getYearsOfWorkExperience();

			$candidates = $em->getRepository("AppBundle:ProfileDetails")->getCountCandidateWithCriteria($params,true);
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
				if (isset($params['yearsOfWorkExperience']) && $params['yearsOfWorkExperience'] != 'null' && $params['yearsOfWorkExperience'] != NULL && $params['yearsOfWorkExperience'] != 'All' && $params['yearsOfWorkExperience'] != 0) {
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
					if(in_array(0, $params['yearsOfWorkExperience'])){
						$yearsOfWorkExperience = array();
					}
				} else {
					$yearsOfWorkExperience = array();
				}
				foreach ($candidates as $key => $candidate) {
					if(in_array($candidate['candidateID'],$candidates_ids)){
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
			$countCandidate = $candidates_n;

			$view = $this->view([
				'id' => $job->getId(),
				'jobTitle' => $job->getJobTitle(),
				'industry' => $job->getIndustry(),
				'industrySecondary' => $job->getIndustrySecondary(),
				'companyName' => $job->getCompanyName(),
				'companyAddress' => $job->getCompanyAddress(),
				'addressCountry' => $job->getAddressCountry(),
				'addressState' => $job->getAddressState(),
				'addressZipCode' => $job->getAddressZipCode(),
				'addressCity' => $job->getAddressCity(),
				'addressSuburb' => $job->getAddressSuburb(),
				'addressStreet' => $job->getAddressStreet(),
				'addressStreetNumber' => $job->getAddressStreetNumber(),
				'addressBuildName' => $job->getAddressBuildName(),
				'addressUnit' => $job->getAddressUnit(),
				'companyDescription' => $job->getCompanyDescription(),
				'companyDescriptionChange' => $job->getCompanyDescriptionChange(),
				'roleDescription' => $job->getRoleDescription(),
				'roleDescriptionChange' => $job->getRoleDescriptionChange(),
				'closureDate' => $job->getClosureDate(),
				'jobClosureDate' => $job->getJobClosureDate(),
				'gender' => $job->getGender(),
				'ethnicity' => $job->getEthnicity(),
				'video' => $job->getVideo(),
				'availability' => $job->getAvailability(),
				'location' => $job->getLocation(),
				'salaryRange' => $job->getSalaryRange(),
				'approve' => $job->getApprove(),
				'status' => $job->getStatus(),
				'createdDate' => $job->getCreated(),
				'started' => $job->getStarted(),
				'spec' => $job->getSpec(),

				'jobReference' => $job->getJobReference(),
				'typeOfEmployment' => $job->getTypeOfEmployment(),
				'timePeriod' => $job->getTimePeriod(),
				'salaryFrom' => $job->getSalaryFrom(),
				'salaryTo' => $job->getSalaryTo(),

				'field' => $job->getField(),
				'monthSalaryFrom' => $job->getMonthSalaryFrom(),
				'monthSalaryTo' => $job->getMonthSalaryTo(),
				'highestQualification' => $job->getHighestQualification(),
				'eligibility' => $job->getEligibility(),
				'yearsOfWorkExperience' => $job->getYearsOfWorkExperience(),
				'assessment' => $job->getAssessment()

			], Response::HTTP_OK);

        }
//        if(!empty($job)){
//            $view = $this->view($job, Response::HTTP_OK);
//        }
        else{
            $view = $this->view(['error'=>'Job Not Found'], Response::HTTP_NOT_FOUND);
        }
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @Rest\Get("/candidate/count")
     * @SWG\Get(path="/api/admin/job/candidate/count",
     *   tags={"Admin Job"},
     *   security={true},
     *   summary="Get Count Candidate Who satisfy the criteria",
     *   description="The method for getting Count Candidate Who satisfy the criteria for admin",
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
     *      default="None",
     *      description="Sort by ethnicity. All OR Black OR White Or Coloured Or Indian Or Oriental"
     *   ),
     *   @SWG\Parameter(
     *      name="location",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="Sort by location."
     *   ),
     *   @SWG\Parameter(
     *      name="availability",
     *      in="query",
     *      required=false,
     *      type="integer",
     *      default=0,
     *      description="Sort by availability. 0 = All, 1 = Immediately, 2 = Within 1 month,3 = Within 3 month"
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
    public function getCountCandidateFoJobAction(Request $request){
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
     * @throws \Exception
     *
     * @Rest\Put("/{id}",requirements={"id"="\d+"})
     * @SWG\Put(path="/api/admin/job/{id}",
     *   tags={"Admin Job"},
     *   security={true},
     *   summary="Edit Job",
     *   description="The method for editing job for Admin",
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
     *      default="jobID",
     *      description="jobID"
     *   ),
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="jobTitle",
     *              type="string",
     *              example="jobTitle",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="industry",
     *              type="array",
     *              @SWG\Items(type="string")
     *          ),
	 *          @SWG\Property(
	 *              property="industrySecondary",
	 *              type="array",
	 *              @SWG\Items(type="string"),
	 *              description="required."
	 *          ),
     *          @SWG\Property(
     *              property="companyName",
     *              type="string",
     *              example="companyName",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="companyAddress",
     *              type="string",
     *              example="companyAddress",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressCountry",
     *              type="string",
     *              example="addressCountry",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressState",
     *              type="string",
     *              example="addressState",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressZipCode",
     *              type="string",
     *              example="addressZipCode",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressCity",
     *              type="string",
     *              example="addressCity",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressSuburb",
     *              type="string",
     *              example="addressSuburb",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressStreet",
     *              type="string",
     *              example="addressStreet",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressStreetNumber",
     *              type="string",
     *              example="addressStreetNumber",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressBuildName",
     *              type="string",
     *              example="addressBuildName",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="addressUnit",
     *              type="string",
     *              example="addressUnit",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="companyDescription",
     *              type="string",
     *              example="companyDescription",
     *              description="required. Min=50,Max=200"
     *          ),
     *          @SWG\Property(
     *              property="companyDescriptionChange",
     *              type="string",
     *              example="companyDescriptionChange",
     *              description="required. Min=50,Max=200"
     *          ),
     *          @SWG\Property(
     *              property="roleDescription",
     *              type="string",
     *              example="roleDescription",
     *              description="required. Max=400"
     *          ),
     *          @SWG\Property(
     *              property="roleDescriptionChange",
     *              type="string",
     *              example="roleDescriptionChange",
     *              description="required. Max=400"
     *          ),
     *          @SWG\Property(
     *              property="closureDate",
     *              type="date",
     *              example="2018-05-10",
     *              description="required. 1 month maximum"
     *          ),
     *          @SWG\Property(
     *              property="jobClosureDate",
     *              type="date",
     *              example="2018-05-10",
     *              description="required."
     *          ),
     *          @SWG\Property(
     *              property="gender",
     *              type="string",
     *              example="All",
     *              description="required. All OR Male OR Female"
     *          ),
     *          @SWG\Property(
     *              property="ethnicity",
     *              type="string",
     *              example="None",
     *              description="required. All OR Black OR White Or Coloured Or Indian Or Oriental"
     *          ),
     *          @SWG\Property(
     *              property="availability",
     *              type="integer",
     *              example=0,
     *              description="required. 0 = All, 1 = Immediately, 2 = Within 1 month,3 = Within 3 month"
     *          ),
     *          @SWG\Property(
     *              property="location",
     *              type="string",
     *              example="location",
     *              description="required. Only All or Gauteng or Western Cape or Eastern Cape or KZNr"
     *          ),
     *          @SWG\Property(
     *              property="salaryRange",
     *              type="integer",
     *              example=0,
     *              description="NOT SEND. NOT required. 0 = None, 1 = 700K, 2 = 700K-1 million,3 = >1 million"
     *          ),
     *          @SWG\Property(
     *              property="started",
     *              type="date",
     *              example="2018-05-10",
     *              description="required."
     *          ),
	 *          @SWG\Property(
	 *              property="jobReference",
	 *              type="string",
	 *              example="jobReference",
	 *              description="required. Max=10"
	 *          ),
	 *          @SWG\Property(
	 *              property="typeOfEmployment",
	 *              type="string",
	 *              example="Contract",
	 *              description="required. Only Contract or Permanent or Temporary",
	 *          ),
	 *          @SWG\Property(
	 *              property="timePeriod",
	 *              type="string",
	 *              example="Full Time",
	 *              description="required. Only Full Time or Part Time",
	 *          ),
	 *          @SWG\Property(
	 *              property="salaryFrom",
	 *              type="integer",
	 *              example=0,
	 *          ),
	 *          @SWG\Property(
	 *              property="salaryTo",
	 *              type="integer",
	 *              example=0,
	 *          ),
	 *          @SWG\Property(
	 *              property="video",
	 *              type="integer",
	 *              example=0,
	 *              description="required. 0 = All, 1 = With Video, 2 = Without Video"
	 *          ),
	 *   		@SWG\Property(
	 *   		   property="monthSalaryFrom",
	 *   		   type="integer",
	 *             example=0,
	 *   		   description="required. 0 or 3500"
	 *   		),
	 *   		@SWG\Property(
	 *   		   property="monthSalaryTo",
	 *   		   type="integer",
	 *             example=3500,
	 *   		   description="required. 3500"
	 *   		),
	 *   		@SWG\Property(
	 *   		   property="highestQualification",
	 *   		   type="string",
	 *             example="NQF 8 - Honours.",
	 *   		   description="required. NQF 8 - Honours."
	 *   		),
	 *  		@SWG\Property(
	 *  		   property="eligibility",
	 *  		   type="string",
	 *             example="applicable",
	 *  		   description="required. All or applicable"
	 *  		),
	 *  		@SWG\Property(
	 *  		   property="yearsOfWorkExperience",
	 *             type="array",
	 *             @SWG\Items(type="string"),
	 *  		   description="required. 1 = 0, 2 = 0-1, 3 = 1-2, 4 = 3-5, 5 = 5+"
	 *  		),
	 *          @SWG\Property(
	 *  		   property="assessment",
	 *  		   type="string",
	 *             example="applicable",
	 *  		   description="required. All - 1 Yes - 2 No - 3"
	 *  		),
     *          @SWG\Property(
     *              property="filled",
     *              type="date",
     *              example="2018-05-10",
     *              description="not required."
     *          ),
     *      ),
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success. Job Edit"
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
    public function editJobAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository("AppBundle:Job")->find($id);
        $approve = $job->getApprove();
        if($job instanceof Job){
            $job->update($request->request->all());
            $errors = $this->get('validator')->validate($job, null, array('Jobs'));
            if(count($errors) === 0){
                $job->setApprove($approve);
                $em->persist($job);
                $em->flush();
                $logging = new Logging($this->getUser(),18, $job->getJobTitle(), $job->getId());
                $em->persist($logging);
                $em->flush();
                $view = $this->view([], Response::HTTP_NO_CONTENT);
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
            $view = $this->view(['error'=>'Job Not Found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Exception
     *
     * @Rest\Patch("/{id}",requirements={"id"="\d+"})
     * @SWG\Patch(path="/api/admin/job/{id}",
     *   tags={"Admin Job"},
     *   security={true},
     *   summary="Change Status Job By Id for Admin",
     *   description="The method for changing status job by id for Admin",
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
     *              property="status",
     *              type="boolean",
     *              example=false,
     *              description="required, true=open,false=close"
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success. Job Status Changed",
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
    public function setStatusJobByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository("AppBundle:Job")->find($id);
        if($job instanceof Job){
            if($request->request->has('status') && is_bool($request->request->get('status'))){
                $job->setStatus($request->request->get('status'));
                $em->persist($job);
                $em->flush();
                if($request->request->get('status') == false){
                    $job->setClosureDate(new \DateTime());
                    $em->persist($job);
                    $em->flush();

                    $applicants = $em->getRepository("AppBundle:Applicants")->findBy(['job'=>$job, 'status'=>1]);
                    foreach ($applicants as $applicant){
                        if($applicant instanceof Applicants){
                            $em->remove($applicant);
                            $em->flush();
                        }
                    }

                    $hideJobs = $em->getRepository("AppBundle:HideJob")->findBy(['job'=>$job]);
                    foreach ($hideJobs as $hideJob){
                        if($hideJob instanceof HideJob){
                            $em->remove($hideJob);
                            $em->flush();
                        }
                    }

                    $logging = new Logging($this->getUser(),20, $job->getJobTitle(), $job->getId());
                    $em->persist($logging);
                    $em->flush();
                }
                else{
                    $logging = new Logging($this->getUser(),19, $job->getJobTitle(), $job->getId());
                    $em->persist($logging);
                    $em->flush();
                }

                $view = $this->view([], Response::HTTP_NO_CONTENT);
            }
            else{
                $view = $this->view(['error'=>'field status is required and should be boolean type'], Response::HTTP_BAD_REQUEST);
            }
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
     * @Rest\Delete("/{id}",requirements={"id"="\d+"})
     * @SWG\Delete(path="/api/admin/job/{id}",
     *   tags={"Admin Job"},
     *   security={true},
     *   summary="Remove Job By Id",
     *   description="The method for removing job by id for admin",
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
     *      description="Job ID"
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success.Job has been removed",
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
    public function removeJobByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository("AppBundle:Job")->find($id);
        if($job instanceof Job){
            $em->remove($job);
            $em->flush();
            $logging = new Logging($this->getUser(),21, $job->getJobTitle());
            $em->persist($logging);
            $em->flush();
            $view = $this->view([], Response::HTTP_NO_CONTENT);
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
     * @Rest\Post("/{id}/spec",requirements={"id"="\d+"})
     * @SWG\Post(path="/api/admin/job/{id}/spec",
     *   tags={"Admin Job"},
     *   security={true},
     *   summary="Upload Job Spec",
     *   description="The method for upload job spec for Admin",
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
     *      default="jobID",
     *      description="jobID"
     *   ),
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="spec",
     *              type="string",
     *              example="file.pdf"
     *          )
     *      ),
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
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
    public function uploadSpecJobByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository("AppBundle:Job")->find($id);
        if($job instanceof Job){
            if($request->files->has('spec')){
                $fileUpload = $request->files->get('spec');
                if($fileUpload instanceof UploadedFile){
                    $fileName = md5(uniqid()).'.'.$fileUpload->getClientOriginalExtension();
                    try {
                        $fileUpload->move("uploads/client/".$job->getUser()->getId()."/job",$fileName);
                        if(!empty($job->getSpec())){
                            $file = $job->getSpec();
                            $file['adminUrl'] = $request->getSchemeAndHttpHost()."/uploads/client/".$job->getUser()->getId()."/job/".$fileName;
                            $file['approved'] = true;
                        }
                        else{
                            $file = [
                                'url' => $request->getSchemeAndHttpHost()."/uploads/client/".$job->getUser()->getId()."/job/".$fileName,
                                'adminUrl' => $request->getSchemeAndHttpHost()."/uploads/client/".$job->getUser()->getId()."/job/".$fileName,
                                'name'=>$fileUpload->getClientOriginalName(),
                                'size'=>$fileUpload->getClientSize(),
                                'time'=>time(),
                                'approved'=>true
                            ];
                        }

                        $job->setSpec($file);

                        $em->persist($job);
                        $em->flush();
                        $view = $this->view([
                            'spec' => $job->getSpec()
                        ], Response::HTTP_OK);

                    } catch (\Exception $e) {
                        $view = $this->view(['error'=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                        return $this->handleView($view);
                    }
                }
                else{
                    $view = $this->view(['error'=>'Job Spec is not file'], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>'Job Spec is required'], Response::HTTP_BAD_REQUEST);
            }

        }
        else{
            $view = $this->view(['error'=>'Job Not Found'], Response::HTTP_NOT_FOUND);
        }
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/approve")
     * @SWG\Get(path="/api/admin/job/approve",
     *   tags={"Admin Job"},
     *   security={true},
     *   summary="Get All Jobs when need approve",
     *   description="The method for getting all Jobs when need approve for admin",
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
     *      description="search by firstName OR lastName OR email OR phone OR companyName OR jobTitle"
     *   ),
     *   @SWG\Parameter(
     *      name="orderBy",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="DaysToGo, Company, JobTitle, Contact, Email, Phone"
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
     *                  ),
	 *      		   @SWG\Property(
	 *      		       property="closureDate",
	 *      		       type="date"
	 *      		   ),
	 *      		   @SWG\Property(
	 *      		       property="clientID",
	 *      		       type="string"
	 *      		   )
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
    public function getJobApproveAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $jobs = $em->getRepository("AppBundle:Job")->getJobApprove($request->query->all());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $jobs,
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     *
     * @Rest\Patch("/{id}/approve",requirements={"id"="\d+"})
     * @SWG\Patch(path="/api/admin/job/{id}/approve",
     *   tags={"Admin Job"},
     *   security={true},
     *   summary="Approve Job By Id for Admin",
     *   description="The method for Approve job by ID for Admin",
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
     *              property="approve",
     *              type="boolean",
     *              example=true,
     *              description="required"
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success. Job Approved or Decline",
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
    public function approveJobByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository("AppBundle:Job")->find($id);
        if($job instanceof Job){
            if($request->request->has('approve') && is_bool($request->request->get('approve'))
            ){
                if($job->getApprove() == true){
                    if(!empty($job->getSpec())){
                        $file = $job->getSpec();
                        if(!isset($file['approved']) || $file['approved'] !== true){
                            $view = $this->view(['error'=>'Job Spec must be approve for job approve'], Response::HTTP_BAD_REQUEST);
                            return $this->handleView($view);
                        }
                    }
                }

                if(empty($job->getCompanyDescriptionChange())){
                   $job->setCompanyDescriptionChange($job->getCompanyDescription());
                }
                if(empty($job->getRoleDescriptionChange())){
                    $job->setRoleDescriptionChange($job->getRoleDescription());
                }
                $job->setApprove($request->request->get('approve'));
                $em->persist($job);
                $em->flush();

                /*if($job->getApprove() == true){
                    $emailData = array(
                        'job' => [
                            'user' => ['firstName'=>$job->getUser()->getFirstName()],
                            'jobTitle' => $job->getJobTitle(),
                            'link' => $request->getSchemeAndHttpHost().'/business/dashboard'
                        ]
                    );
                    $message = (new \Swift_Message('Your Job has been approved on Yes2Work'))
                        ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                        ->setTo($job->getUser()->getEmail())
                        ->setBody(
                            $this->renderView('emails/client/job_approve.html.twig',
                                $emailData
                            ),
                            'text/html'
                        );

                    SendEmail::sendEmailForClient($job->getUser(), $em, $message, $this->get('mailer'), $emailData, SendEmail::TYPE_CLIENT_JOB_APPROVE);
                    $candidatesCriteria = $em->getRepository("AppBundle:ProfileDetails")->getCandidateWithCriteria([
                        'gender' => $job->getGender(),
                        'ethnicity' => $job->getEthnicity(),
                        'location' => $job->getLocation(),
                        'availability' => $job->getAvailability()
                    ]);
                    if(!empty($candidatesCriteria)){
                        $companyDetails = $em->getRepository("AppBundle:CompanyDetails")->findOneBy(['user'=>$job->getUser()]);
                        foreach ($candidatesCriteria as $candidatesCriterion){
                            if($candidatesCriterion instanceof ProfileDetails){
                                if($candidatesCriterion->getPercentage() > 50
                                    && $candidatesCriterion->getLooking() == true
                                    && !empty($candidatesCriterion->getVideo())
                                    && isset($candidatesCriterion->getVideo()['approved'])
                                    && $candidatesCriterion->getVideo()['approved'] == true
                                ){
                                    $emailData = [
                                        'user' => ['firstName'=>$candidatesCriterion->getUser()->getFirstName()],
                                        'jse' => ($companyDetails instanceof CompanyDetails && is_bool($companyDetails->getJse())) ? $companyDetails->getJse() : false,
                                        'industry' => implode(', ',$job->getIndustry()),
                                        'jobTitle'=>$job->getJobTitle(),
                                        'city'=>$job->getAddressCity(),
                                        'link'=>$request->getSchemeAndHttpHost().'/candidate/job_alerts_new',
                                        'closureDate'=>$job->getClosureDate()
                                    ];
                                    $message = (new \Swift_Message('A new Yes2Work job has just been opened – are you interested?'))
                                        ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                                        ->setTo($job->getUser()->getEmail())
                                        ->setBody(
                                            $this->renderView('emails/candidate/new_job_loaded.html.twig',
                                                $emailData
                                            ),
                                            'text/html'
                                        );

                                    SendEmail::sendEmailForCandidate($candidatesCriterion->getUser(), $em, $message, $this->get('mailer'),
                                        $emailData, SendEmail::TYPE_CANDIDATE_NEW_JOB_LOADED);

                                }
                            }
                        }
                    }

                }*/

                if($job->getApprove() == true){
                    $logging = new Logging($this->getUser(),22, $job->getJobTitle(), $job->getId());
                    $em->persist($logging);
                    $em->flush();
                }
                else{
                    $logging = new Logging($this->getUser(),23, $job->getJobTitle(), $job->getId());
                    $em->persist($logging);
                    $em->flush();
                }

                $view = $this->view([], Response::HTTP_NO_CONTENT);
            }
            else{
                $view = $this->view(['error'=>'All field is required and should be empty'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'Job Not Found'], Response::HTTP_NOT_FOUND);
        }
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/file/approve")
     * @SWG\Get(path="/api/admin/job/file/approve",
     *   tags={"Admin Job"},
     *   security={true},
     *   summary="Get All FILES NEED APPROVE FOR JOB SPEC",
     *   description="The method for getting all files when need approve for admin",
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
    public function getJobSpecApproveAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $files = $em->getRepository("AppBundle:Job")->getJobSpecApprove($request->query->all());

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
     * @param $id
     * @return Response
     *
     * @Rest\Post("/{id}/file/approve", requirements={"id"="\d+"})
     * @SWG\Post(path="/api/admin/job/{id}/file/approve",
     *   tags={"Admin Job"},
     *   security={true},
     *   summary="UPLOAD JOB SPEC BY jobId FOR ADMIN",
     *   description="The method for upload job spec by jobId for admin",
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
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="spec",
     *              type="string",
     *              example="file.pdf"
     *          )
     *      ),
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="adminUrl",
     *              type="string",
     *              example="file.pdf"
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
    public function uploadJobSpecApproveAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository("AppBundle:Job")->find($id);
        if($job instanceof Job){
            if($request->files->has('spec')){
                $fileUpload = $request->files->get('spec');
                if($fileUpload instanceof UploadedFile){
                    $fileName = md5(uniqid()).'.'.$fileUpload->getClientOriginalExtension();
                    try {
                        $fileUpload->move("uploads/client/".$job->getUser()->getId()."/job",$fileName);
                        $file = $job->getSpec();
                        $file['adminUrl'] = $request->getSchemeAndHttpHost()."/uploads/client/".$job->getUser()->getId()."/job/".$fileName;

                        $job->setSpec($file);

                        $em->persist($job);
                        $em->flush();
                        $view = $this->view([
                            'adminUrl' => $request->getSchemeAndHttpHost()."/uploads/client/".$job->getUser()->getId()."/job/".$fileName
                        ], Response::HTTP_OK);

                    } catch (\Exception $e) {
                        $view = $this->view(['error'=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                        return $this->handleView($view);
                    }
                }
                else{
                    $view = $this->view(['error'=>'Job Spec is not file'], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>'Job Spec is required'], Response::HTTP_BAD_REQUEST);
            }
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
     * @Rest\Patch("/{id}/file/approve", requirements={"id"="\d+"})
     * @SWG\Patch(path="/api/admin/job/{id}/file/approve",
     *   tags={"Admin Job"},
     *   security={true},
     *   summary="APPROVE JOB SPEC BY jobId FOR ADMIN",
     *   description="The method for approve job spec by jobId for admin",
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
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="approve",
     *              type="boolean",
     *              example=true,
     *              description="required"
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success."
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
    public function approveJobSpecApproveByIdAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository("AppBundle:Job")->find($id);
        if($job instanceof Job){
            if($request->request->has('approve') && is_bool($request->request->get('approve'))){
                $file = $job->getSpec();
                if($request->request->get('approve') == true){
                    if (isset($file['adminUrl']) && !empty($file['adminUrl'])){
                        $file['approved'] = true;
                        $job->setSpec($file);
                        $em->persist($job);
                        $em->flush();

                        $view = $this->view([], Response::HTTP_NO_CONTENT);
                    }
                    else{
                        $view = $this->view(['error'=>'Admin version docs required for approve'], Response::HTTP_BAD_REQUEST);
                    }
                }
                else{
                    $job->setSpec(null);
                    $em->persist($job);
                    $em->flush();

                    $fileSystem = new Filesystem();
                    $parse = parse_url($file['url']);
                    if(isset($parse['path']) && !empty($parse['path'])){
                        $parse['path'] = ltrim($parse['path'], '/');
                        if($fileSystem->exists($parse['path'])){
                            $fileSystem->remove($parse['path']);
                        }
                    }
                    $view = $this->view([], Response::HTTP_NO_CONTENT);
                }
            }
            else{
                $view = $this->view(['error'=>'approve params is required and should be boolean type'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'Job Not Found'], Response::HTTP_NOT_FOUND);
        }
        return $this->handleView($view);
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
