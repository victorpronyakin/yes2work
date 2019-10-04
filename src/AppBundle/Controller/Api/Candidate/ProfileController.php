<?php
/**
 * Created by PhpStorm.
 * Date: 17.04.18
 * Time: 14:14
 */

namespace AppBundle\Controller\Api\Candidate;


use AppBundle\Entity\CandidateAchievements;
use AppBundle\Entity\CandidateQualifications;
use AppBundle\Entity\CandidateReferences;
use AppBundle\Entity\ProfileDetails;
use AppBundle\Entity\Settings;
use AppBundle\Helper\HelpersClass;
use AppBundle\Helper\SendEmail;
use AppBundle\Helper\Ziggeo\Ziggeo;
use AppBundle\Helper\Ziggeo\ZiggeoException;
use Aws\Credentials\Credentials;
use Aws\ElasticTranscoder\ElasticTranscoderClient;
use Aws\S3\Exception\S3Exception;
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
 * Class MainController
 * @package AppBundle\Controller\Api\Candidate
 * @Rest\Route("profile")
 * @Security("has_role('ROLE_CANDIDATE')")
 */
class ProfileController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Rest\Get("/")
     * @SWG\Get(path="/api/candidate/profile/",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Get Candidate Profile Details",
     *   description="The method for getting profile details for candidate",
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
     *          ),
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
    public function getProfileAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $userDetails = $em->getRepository("AppBundle:User")->getCandidateProfile($this->getUser()->getId());
        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->getCandidateDetails($this->getUser()->getId());
        $settings = $em->getRepository('AppBundle:Settings')->findOneBy([]);
        if(!$settings instanceof Settings){
            $settings = new Settings(false);
            $em->persist($settings);
            $em->flush();
        }
        $view = $this->view(['user'=>$userDetails,'profile'=>$profileDetails,'allowVideo'=>$settings->getAllowVideo()], Response::HTTP_OK);
        return $this->handleView($view);

    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Put("/")
     * @SWG\Put(path="/api/candidate/profile/",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Edit Candidate Profile Details Without Files",
     *   description="The method for Edit profile details for candidate",
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
     *                  property="universityExemption",
     *                  type="boolean",
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
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="percentage",
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="looking",
     *              type="boolean"
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
    public function editProfileAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if($request->request->has('user') && !empty(($request->request->get('user')))){
            $userData = $request->request->get('user');

            if(isset($userData['firstName']) && isset($userData['lastName']) && isset($userData['email']) && isset($userData['phone'])){
                $user->setFirstName($userData['firstName']);
                $user->setLastName($userData['lastName']);
                $user->setEmail($userData['email']);
                $user->setUsername($userData['email']);
                $user->setPhone($userData['phone']);
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

                            $view = $this->view([
                                'percentage'=>$profileDetails->getPercentage(),
                                'looking' => $profileDetails->getLooking()
                            ], Response::HTTP_OK);
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

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     *
     * @Rest\Patch("/")
     * @SWG\Patch(path="/api/candidate/profile/",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Change Status Candidate",
     *   description="The method for change status for candidate",
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
     *              type="boolean",
     *              property="looking",
     *              example=true,
     *              description="one of two field"
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="looking",
     *              type="boolean"
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
    public function updateStatusProfileAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
        if($profileDetails instanceof $profileDetails){
            if($request->request->has('looking') && is_bool($request->request->get('looking'))){
                $profileDetails->setLooking($request->request->get('looking'));

                if($request->request->get('looking') == false){
                    $profileDetails->setLastDeactivated(new \DateTime());
                    $emailData = [
                        'candidate' => [
                            'firstName' => $user->getFirstName(),
                            'lastName' => $user->getLastName(),
                            'email' => $user->getEmail(),
                            'phone' => $user->getPhone()
                        ] ,
                        'link' => $request->getSchemeAndHttpHost().'/admin/edit_candidate?candidateId='.$user->getId()
                    ];
                    $message = (new \Swift_Message('A candidate has just deactivated their profile'))
                        ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                        ->setBody(
                            $this->renderView('emails/admin/candidate_deactivated.html.twig',
                                $emailData
                            ),
                            'text/html'
                        );

                    SendEmail::sendEmailForAdmin($em, $message, $this->get('mailer'), $emailData, SendEmail::TYPE_ADMIN_CANDIDATE_DEACTIVATE);
                }
                $em->persist($profileDetails);
                $em->flush();
                $view = $this->view([
                    'looking' => $profileDetails->getLooking()
                ], Response::HTTP_OK);
            }
            else{
                $view = $this->view(['error'=>'Field looking is required, and should be boolean type'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'Profile Not found'], Response::HTTP_NOT_FOUND);
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
     * @Rest\Post("/file")
     * @SWG\Post(path="/api/candidate/profile/file",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Upload candidate file Candidate",
     *   description="The method for upload file for candidate",
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
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="looking",
     *              type="boolean"
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
    public function uploadFileAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
        if($profileDetails instanceof $profileDetails){
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
                                            'name'=>$fileUpload->getClientOriginalName(),
                                            'size'=>$fileUpload->getClientSize(),
                                            'time'=>time(),
                                            'approved'=>false
                                        ];
                                        if($key != 'picture'){
                                            $emailData = array(
                                                'link' => $request->getSchemeAndHttpHost().'/admin/dashboard'
                                            );
                                            $message = (new \Swift_Message('A new document is awaiting your approval'))
                                                ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                                                ->setBody(
                                                    $this->renderView('emails/admin/new_file_uploaded.html.twig',
                                                        $emailData
                                                    ),
                                                    'text/html'
                                                );
                                            SendEmail::sendEmailForAdmin($em, $message, $this->get('mailer'), $emailData, SendEmail::TYPE_ADMIN_CANDIDATE_FILE);
                                        }
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
                                        'name'=>$fileArray->getClientOriginalName(),
                                        'size'=>$fileArray->getClientSize(),
                                        'time'=>time(),
                                        'approved'=>false
                                    ];
                                    if($key != 'picture'){
                                        $emailData = array(
                                            'link' => $request->getSchemeAndHttpHost().'/admin/dashboard'
                                        );
                                        $message = (new \Swift_Message('A new document is awaiting your approval'))
                                            ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                                            ->setBody(
                                                $this->renderView('emails/admin/new_file_uploaded.html.twig',
                                                    $emailData
                                                ),
                                                'text/html'
                                            );
                                        SendEmail::sendEmailForAdmin($em, $message, $this->get('mailer'), $emailData, SendEmail::TYPE_ADMIN_CANDIDATE_FILE);
                                    }
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
                $view = $this->view([
                    'percentage'=>$profileDetails->getPercentage(),
                    'looking' => $profileDetails->getLooking(),
                    'files'=>$files
                ], Response::HTTP_OK);
            }
            else{
                $view = $this->view(['error'=>'Files is empty'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'Profile Not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Patch("/file")
     * @SWG\Patch(path="/api/candidate/profile/file",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Remove Candidate File",
     *   description="The method for remove File for candidate",
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
     *              property="percentage",
     *              type="integer",
     *              example=50
     *          ),
     *          @SWG\Property(
     *              property="looking",
     *              type="boolean"
     *          ),
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
    public function removeFileAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
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
                            if($checkFile == true) {
                                $newFiles = [];
                                if (!empty($files)) {
                                    foreach ($files as $f) {
                                        $newFiles[] = $f;
                                    }
                                }
                                $profileDetails->$methodNameSet($newFiles);
                                $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                                $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                                $em->persist($profileDetails);
                                $em->flush();
                                $view = $this->view([
                                    'percentage'=>$profileDetails->getPercentage(),
                                    'looking' => $profileDetails->getLooking(),
                                    $key => $profileDetails->$methodNameGet()
                                ], Response::HTTP_OK);
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
        $view = $this->view(['error'=>'error'], Response::HTTP_BAD_REQUEST);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/video")
     * @SWG\Get(path="/api/candidate/profile/video",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Get Candidate Video",
     *   description="The method for getting video for candidate",
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
     *              property="video",
     *              type="object",
     *              @SWG\Property(
     *                  property="url",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="name",
     *                  type="string"
     *              )
     *          )
     *     )
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
    public function getVideoAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
        if($profileDetails instanceof ProfileDetails){
            $view = $this->view(['video'=>$profileDetails->getVideo()], Response::HTTP_OK);
        }
        else{
            $view = $this->view(['video'=>null], Response::HTTP_OK);

        }
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Post("/video")
     * @SWG\Post(path="/api/candidate/profile/video",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Upload Candidate Video",
     *   description="The method for uploading video for candidate",
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
     *              property="token",
     *              type="string",
     *              example="432d64555ec3f86c95f4452bde463d80",
     *              description="required if empty video"
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success. Video Uploaded",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="percentage",
     *              type="integer",
     *              example=50
     *          ),
     *          @SWG\Property(
     *              property="looking",
     *              type="boolean"
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
     *   )
     * )
     */
    public function uploadVideoAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
        if($request->request->has('token')){
            //Get Video Ziggeo
            try{
                $ziggeo = new Ziggeo($this->container->getParameter('ziggeo_token'), $this->container->getParameter('ziggeo_secret'), $this->container->getParameter('ziggeo_encrypt'));
                $videoZiggeo = $ziggeo->videos()->download_video($request->request->get('token'));
            }
            catch (\Exception $e){
                $view = $this->view(['error'=>$e->getMessage()], Response::HTTP_BAD_REQUEST);
                return $this->handleView($view);
            }
            //Upload to s3
            $credentials = new Credentials($this->container->getParameter('aws_key'), $this->container->getParameter('aws_secret'));
	        $s3Client = new S3Client([
		        'version'     => 'latest',
		        'region'      => $this->container->getParameter('aws_region'),
		        'credentials' => $credentials
	        ]);
            $fileName = $request->request->get('token')."_".$user->getFirstName()."_".$user->getId().".mp4";
            try{
                // Get the object.
                $result = $s3Client->putObject([
                    'Bucket' => $this->container->getParameter('aws_bucket'),
                    'Key'    => $fileName,
                    'Body'   => $videoZiggeo,
                    'ACL'    => 'public-read'
                ]);
                $oldVideo = $profileDetails->getVideo();
                if(isset($oldVideo['name']) && !empty($oldVideo['name'])){
                    try{
                        $resultRemove = $s3Client->deleteObject(array(
                            'Bucket' => $this->container->getParameter('aws_bucket'),
                            'Key'    => $oldVideo['name']
                        ));
                    } catch (\Exception $e){}
                }

                $video = [
                    'url'=>$result['ObjectURL'],
                    'adminUrl'=>$result['ObjectURL'],
                    'name'=>$fileName,
                    'time'=>time(),
                    'approved'=>true
                ];
                $profileDetails->setVideo($video);
                $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                $em->persist($profileDetails);
                $em->flush();

                $view = $this->view([
                    'percentage'=>$profileDetails->getPercentage(),
                    'looking' => $profileDetails->getLooking(),
                    'video'=>$video
                ], Response::HTTP_OK);
                return $this->handleView($view);
            }
            catch (S3Exception $e){
                $view = $this->view(['error'=>$e->getMessage()], Response::HTTP_BAD_REQUEST);
                return $this->handleView($view);
            }
        }

        $view = $this->view(['error'=>'token is required'], Response::HTTP_BAD_REQUEST);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Delete("/video")
     * @SWG\Delete(path="/api/candidate/profile/video",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="DELETE Candidate Video",
     *   description="The method for DELETE video for candidate",
     *   produces={"application/json"},
     *   consumes={"multipart/form-data"},
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
     *   @SWG\Response(
     *      response=200,
     *      description="Success. Video Deleted",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="percentage",
     *              type="integer",
     *              example=50
     *          ),
     *          @SWG\Property(
     *              property="looking",
     *              type="boolean"
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
    public function removeVideoAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
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
                    $fileSystem = new Filesystem();
                    if($fileSystem->exists("uploads/candidate/".$user->getId()."/".$video['name'])){
                        try{
                            $fileSystem->remove("uploads/candidate/".$user->getId()."/".$video['name']);
                        }
                        catch (\Exception $e){}
                    }
                    $result = $s3Client->deleteObject(array(
                        'Bucket' => $this->container->getParameter('aws_bucket'),
                        'Key'    => $video['name']
                    ));
                } catch (\Exception $e) {}
            }
            $profileDetails->setVideo(NULL);
            $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
            $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
            $em->persist($profileDetails);
            $em->flush();
            $view = $this->view([
                'percentage'=>$profileDetails->getPercentage(),
                'looking' => $profileDetails->getLooking()
            ], Response::HTTP_OK);
            return $this->handleView($view);
        }
        else{
            $view = $this->view(['error'=>'Video not Found'], Response::HTTP_BAD_REQUEST);

        }
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/qualifications")
     * @SWG\Get(path="/api/candidate/profile/qualifications",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Get Candidate qualifications",
     *   description="The method for getting candidate qualifications",
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
     *          type="array",
     *          @SWG\Items(
     *              type="object",
     *              @SWG\Property(
     *                  property="id",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  type="integer",
     *                  property="type",
     *                  description="1 = Matric, 2 = Gr10 , 3 = Tertiary"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="schoolName",
     *                  description="required if type=1"
     *              ),
     *              @SWG\Property(
     *                  type="datetime",
     *                  property="matriculatedYear",
     *                  example="2018-00-09",
     *                  description="required if type=1"
     *              ),
     *              @SWG\Property(
     *                  type="object",
     *                  property="completeSubject",
     *                  description="required if type=1"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="tertiaryInstitution",
     *                  description="required if type=3"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="tertiaryInstitutionCustom",
     *                  description="required if type=3 and tertiaryInstitution=other"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="levelQ",
     *                  description="required if type=3"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="specificQ",
     *                  description="required if type=3"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="specificQCustom",
     *                  description="required if type=3 and specific=Other"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="specialization",
     *                  description="required if type=3"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="specializationCustom",
     *                  description="required if type=3 and specialization=Other"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="education",
     *                  description="required if type=3"
     *              ),
     *              @SWG\Property(
     *                  type="datetime",
     *                  property="startYear",
     *                  example="2018-00-09",
     *                  description="required if type=3"
     *              ),
     *              @SWG\Property(
     *                  type="datetime",
     *                  property="endYear",
     *                  example="2018-00-09",
     *                  description="required if type=3"
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
     *   )
     * )
     */
    public function getQualificationsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $qualifications = $em->getRepository("AppBundle:CandidateQualifications")->getQualificationsCandidate($this->getUser()->getId());

        $view = $this->view($qualifications, Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Post("/qualifications")
     * @SWG\Post(path="/api/candidate/profile/qualifications",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Create Candidate qualifications",
     *   description="The method for create candidate qualifications",
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
     *              type="integer",
     *              property="type",
     *              description="1 = Matric, 2 = Gr10 , 3 = Tertiary"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="schoolName",
     *              description="required if type=1"
     *          ),
     *          @SWG\Property(
     *              type="datetime",
     *              property="matriculatedYear",
     *              example="2018-00-09",
     *              description="required if type=1"
     *          ),
     *          @SWG\Property(
     *              type="object",
     *              property="completeSubject",
     *              description="required if type=1"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="tertiaryInstitution",
     *              description="required if type=3"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="tertiaryInstitutionCustom",
     *              description="required if type=3 and tertiaryInstitution=Other"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="levelQ",
     *              description="required if type=3"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="specificQ",
     *              description="required if type=3"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="specificQCustom",
     *              description="required if type=3 and specific=Other"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="specialization",
     *              description="required if type=3"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="specializationCustom",
     *              description="required if type=3 and specialization=Other"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="education",
     *              description="required if type=3"
     *          ),
     *          @SWG\Property(
     *              type="datetime",
     *              property="startYear",
     *              example="2018-00-09",
     *              description="required if type=3"
     *          ),
     *          @SWG\Property(
     *              type="datetime",
     *              property="endYear",
     *              example="2018-00-09",
     *              description="required if type=3"
     *          ),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              type="object",
     *              property="qualification",
     *              @SWG\Property(
     *                  property="id",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  type="integer",
     *                  property="type",
     *                  description="1 = Matric, 2 = Gr10 , 3 = Tertiary"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="schoolName",
     *                  description="required if type=1"
     *              ),
     *              @SWG\Property(
     *                  type="datetime",
     *                  property="matriculatedYear",
     *                  example="2018-00-09",
     *                  description="required if type=1"
     *              ),
     *              @SWG\Property(
     *                  type="array",
     *                  property="completeSubject",
     *                  description="required if type=1",
     *                  @SWG\Items(
     *                      type="object"
     *                  )
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="tertiaryInstitution",
     *                  description="required if type=3"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="tertiaryInstitutionCustom",
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="levelQ",
     *                  description="required if type=3"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="specificQ",
     *                  description="required if type=3"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="specificQCustom",
     *                  description="required if type=3 and specific=Other"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="specialization",
     *                  description="required if type=3"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="specializationCustom",
     *                  description="required if type=3 and specialization=Other"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="education",
     *                  description="required if type=3"
     *              ),
     *              @SWG\Property(
     *                  type="datetime",
     *                  property="startYear",
     *                  example="2018-00-09",
     *                  description="required if type=3"
     *              ),
     *              @SWG\Property(
     *                  type="datetime",
     *                  property="endYear",
     *                  example="2018-00-09",
     *                  description="required if type=3"
     *              ),
     *          ),
     *          @SWG\Property(
     *              property="percentage",
     *              type="integer",
     *              example=50
     *          ),
     *          @SWG\Property(
     *              property="looking",
     *              type="boolean"
     *          )
     *     )
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
     *   )
     * )
     */
    public function createQualificationsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        if($request->request->has('type') && !empty($request->request->get('type')) && in_array($request->request->get('type'), [1,2,3])){
            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);
            if($profileDetails instanceof ProfileDetails){
                $qualification = new CandidateQualifications($this->getUser(), $request->request->get('type'));
                $qualification->update($request->request->all());
                $validator = $this->get('validator');
                if($qualification->getType() == 1){
                    $errors = $validator->validate($qualification, null, array('validateMatric'));
                }
                elseif ($qualification->getType() == 3){
                    $errors = $validator->validate($qualification, null, array('validateTertiary'));
                }
                else{
                    $errors = $validator->validate($qualification, null, array('validateGr10'));
                }

                if(count($errors) === 0){
                    $em->persist($qualification);
                    $em->flush();

                    $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                    $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                    $em->persist($profileDetails);
                    $em->flush();

                    $view = $this->view([
                        'qualification' => [
                            'id'=> $qualification->getId(),
                            'type' => $qualification->getType(),
                            'schoolName' => $qualification->getSchoolName(),
                            'matriculatedYear' => $qualification->getMatriculatedYear(),
                            'completeSubject' => $qualification->getCompleteSubject(),
                            'tertiaryInstitution' => $qualification->getTertiaryInstitution(),
                            'tertiaryInstitutionCustom' => $qualification->getTertiaryInstitutionCustom(),
                            'levelQ' => $qualification->getLevelQ(),
                            'specificQ' => $qualification->getSpecificQ(),
                            'specificQCustom' => $qualification->getSpecificQCustom(),
                            'specialization' => $qualification->getSpecialization(),
                            'specializationCustom' => $qualification->getSpecializationCustom(),
                            'education' => $qualification->getEducation(),
                            'startYear' => $qualification->getStartYear(),
                            'endYear' => $qualification->getEndYear(),
                        ],
                        'percentage'=>$profileDetails->getPercentage(),
                        'looking' => $profileDetails->getLooking()
                    ], Response::HTTP_OK);
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
                $view = $this->view(['error'=>['Profile Details Not Found']], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>['Type is required and should be valid']], Response::HTTP_BAD_REQUEST);
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
     * @Rest\Put("/qualifications/{id}",requirements={"id"="\d+"})
     * @SWG\Put(path="/api/candidate/profile/qualifications/{id}",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Edit Candidate qualifications",
     *   description="The method for edit candidate qualifications",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      type="integer",
     *      default="",
     *      description="qualifications ID"
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
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              type="integer",
     *              property="type",
     *              description="1 = Matric, 2 = Gr10 , 3 = Tertiary"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="schoolName",
     *              description="required if type=1"
     *          ),
     *          @SWG\Property(
     *              type="datetime",
     *              property="matriculatedYear",
     *              example="2018-00-09",
     *              description="required if type=1"
     *          ),
     *          @SWG\Property(
     *              type="object",
     *              property="completeSubject",
     *              description="required if type=1"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="tertiaryInstitution",
     *              description="required if type=3"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="tertiaryInstitutionCustom",
     *              description="required if type=3 and tertiaryInstitution=Other"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="levelQ",
     *              description="required if type=3"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="specificQ",
     *              description="required if type=3"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="specificQCustom",
     *              description="required if type=3 and specific=Other"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="specialization",
     *              description="required if type=3"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="specializationCustom",
     *              description="required if type=3 and specialization=Other"
     *          ),
     *          @SWG\Property(
     *              type="string",
     *              property="education",
     *              description="required if type=3"
     *          ),
     *          @SWG\Property(
     *              type="datetime",
     *              property="startYear",
     *              example="2018-00-09",
     *              description="required if type=3"
     *          ),
     *          @SWG\Property(
     *              type="datetime",
     *              property="endYear",
     *              example="2018-00-09",
     *              description="required if type=3"
     *          ),
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
     *              property="looking",
     *              type="boolean"
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
    public function editQualificationsAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        $qualification = $em->getRepository("AppBundle:CandidateQualifications")->findOneBy(['id'=>$id,'user'=>$this->getUser()]);
        if($qualification instanceof CandidateQualifications){
            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);
            if($profileDetails instanceof ProfileDetails){
                if($request->request->has('type') && !empty($request->request->get('type')) && in_array($request->request->get('type'), [1,2,3])){
                    $qualification->update($request->request->all());
                    $validator = $this->get('validator');
                    if($qualification->getType() == 1){
                        $errors = $validator->validate($qualification, null, array('validateMatric'));
                    }
                    elseif ($qualification->getType() == 3){
                        $errors = $validator->validate($qualification, null, array('validateTertiary'));
                    }
                    else{
                        $errors = $validator->validate($qualification, null, array('validateGr10'));
                    }
                    if(count($errors) === 0){
                        $em->persist($qualification);
                        $em->flush();

                        $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                        $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                        $em->persist($profileDetails);
                        $em->flush();
                        $view = $this->view([
                            'percentage'=>$profileDetails->getPercentage(),
                            'looking' => $profileDetails->getLooking()
                        ], Response::HTTP_OK);
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
                    $view = $this->view(['error'=>['Type is required and should be valid']], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>['Profile Details Not Found']], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>['Qualifications not found']], Response::HTTP_NOT_FOUND);
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
     * @Rest\Delete("/qualifications/{id}",requirements={"id"="\d+"})
     * @SWG\Delete(path="/api/candidate/profile/qualifications/{id}",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Delete Candidate qualifications",
     *   description="The method for Delete candidate qualifications",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      type="integer",
     *      default="",
     *      description="qualifications ID"
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
     *              property="looking",
     *              type="boolean"
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
    public function deleteQualificationsAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        $qualification = $em->getRepository("AppBundle:CandidateQualifications")->findOneBy(['id'=>$id,'user'=>$this->getUser()]);
        if($qualification instanceof CandidateQualifications){
            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);
            if($profileDetails instanceof ProfileDetails){
                $em->remove($qualification);
                $em->flush();

                $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                $em->persist($profileDetails);
                $em->flush();
                $view = $this->view([
                    'percentage'=>$profileDetails->getPercentage(),
                    'looking' => $profileDetails->getLooking()
                ], Response::HTTP_OK);
            }
            else{
                $view = $this->view(['error'=>'Profile Details Not Found'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'Qualifications not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/achievement")
     * @SWG\Get(path="/api/candidate/profile/achievement",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Get Candidate Achievements",
     *   description="The method for getting candidate Achievements",
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
     *          type="array",
     *          @SWG\Items(
     *              type="object",
     *              @SWG\Property(
     *                  property="id",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="description",
     *                  type="string"
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
     *   )
     * )
     */
    public function getAchievementsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $achievements = $em->getRepository("AppBundle:CandidateAchievements")->getAchievementsCandidate($this->getUser()->getId());

        $view = $this->view($achievements, Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Post("/achievement")
     * @SWG\Post(path="/api/candidate/profile/achievement",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Create Candidate Achievements",
     *   description="The method for create candidate achievement. MAX 5 Achievements",
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
     *              type="string",
     *              property="description",
     *              example="description",
     *              description="Required. Max 50 Characters",
     *          ),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              type="object",
     *              property="achievement",
     *              @SWG\Property(
     *                  property="id",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  type="string",
     *                  property="description"
     *              ),
     *          ),
     *          @SWG\Property(
     *              property="percentage",
     *              type="integer",
     *              example=50
     *          ),
     *          @SWG\Property(
     *              property="looking",
     *              type="boolean"
     *          )
     *     )
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
     *   )
     * )
     */
    public function createAchievementsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        if($request->request->has('description')){
            $achievements = $em->getRepository("AppBundle:CandidateAchievements")->findBy(['user'=>$this->getUser()]);
            if(count($achievements)<5){
                $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);
                if($profileDetails instanceof ProfileDetails){
                    $achievement = new CandidateAchievements($this->getUser(), $request->request->get('description'));
                    $validator = $this->get('validator');
                    $errors = $validator->validate($achievement, null, array('validateAchievements'));
                    if(count($errors) === 0){
                        $em->persist($achievement);
                        $em->flush();

                        $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                        $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                        $em->persist($profileDetails);
                        $em->flush();

                        $view = $this->view([
                            'achievement' => [
                                'id'=> $achievement->getId(),
                                'description' => $achievement->getDescription()
                            ],
                            'percentage'=>$profileDetails->getPercentage(),
                            'looking' => $profileDetails->getLooking()
                        ], Response::HTTP_OK);
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
                    $view = $this->view(['error'=>'Profile Details Not Found'], Response::HTTP_NOT_FOUND);
                }
            }
            else{
                $view = $this->view(['error'=>'limit achievements. Max 5 achievements.'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'field description is required'], Response::HTTP_BAD_REQUEST);
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
     * @Rest\Put("/achievement/{id}",requirements={"id"="\d+"})
     * @SWG\Put(path="/api/candidate/profile/achievement/{id}",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Edit Candidate Achievements",
     *   description="The method for edit candidate achievement",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      type="integer",
     *      default="",
     *      description="Achievement ID"
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
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              type="string",
     *              property="description",
     *              example="description",
     *              description="Required. Max 50 Characters",
     *          ),
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
     *              property="looking",
     *              type="boolean"
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
    public function editAchievementAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        $achievement = $em->getRepository("AppBundle:CandidateAchievements")->findOneBy(['id'=>$id,'user'=>$this->getUser()]);
        if($achievement instanceof CandidateAchievements){
            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);
            if($profileDetails instanceof ProfileDetails){
                if($request->request->has('description')){
                    $achievement->setDescription($request->request->get('description'));
                    $validator = $this->get('validator');
                    $errors = $validator->validate($achievement, null, array('validateAchievements'));
                    if(count($errors) === 0){
                        $em->persist($achievement);
                        $em->flush();

                        $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                        $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                        $em->persist($profileDetails);
                        $em->flush();
                        $view = $this->view([
                            'percentage'=>$profileDetails->getPercentage(),
                            'looking' => $profileDetails->getLooking()
                        ], Response::HTTP_OK);
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
                    $view = $this->view(['error'=>'field description is required'], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>'Profile Details Not Found'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'Achievement not found'], Response::HTTP_NOT_FOUND);
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
     * @Rest\Delete("/achievement/{id}",requirements={"id"="\d+"})
     * @SWG\Delete(path="/api/candidate/profile/achievement/{id}",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Delete Candidate Achievements",
     *   description="The method for Delete candidate achievement",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      type="integer",
     *      default="",
     *      description="Achievement ID"
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
     *              property="looking",
     *              type="boolean"
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
    public function deleteAchievementAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        $achievement = $em->getRepository("AppBundle:CandidateAchievements")->findOneBy(['id'=>$id,'user'=>$this->getUser()]);
        if($achievement instanceof CandidateAchievements){
            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);
            if($profileDetails instanceof ProfileDetails){
                $em->remove($achievement);
                $em->flush();

                $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                $em->persist($profileDetails);
                $em->flush();
                $view = $this->view([
                    'percentage'=>$profileDetails->getPercentage(),
                    'looking' => $profileDetails->getLooking()
                ], Response::HTTP_OK);
            }
            else{
                $view = $this->view(['error'=>'Profile Details Not Found'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'Achievement not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/references")
     * @SWG\Get(path="/api/candidate/profile/references",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Get Candidate References",
     *   description="The method for getting candidate References",
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
     *          type="array",
     *          @SWG\Items(
     *              type="object",
     *              @SWG\Property(
     *                  property="id",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="company",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="role",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="specialization",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="startDate",
     *                  type="datetime"
     *              ),
     *              @SWG\Property(
     *                  property="endDate",
     *                  type="datetime"
     *              ),
     *              @SWG\Property(
     *                  property="isReference",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="managerFirstName",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="managerLastName",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="managerTitle",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="managerEmail",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="managerComment",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="permission",
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
     *   )
     * )
     */
    public function getReferencesAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $references = $em->getRepository("AppBundle:CandidateReferences")->getReferencesCandidate($this->getUser()->getId(), false);

        $view = $this->view($references, Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Rest\Post("/references")
     * @SWG\Post(path="/api/candidate/profile/references",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Create Candidate Reference",
     *   description="The method for create candidate Reference. MAX 5 References",
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
     *              property="company",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="role",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="specialization",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="startDate",
     *              type="datetime"
     *          ),
     *          @SWG\Property(
     *              property="endDate",
     *              type="datetime"
     *          ),
     *          @SWG\Property(
     *              property="isReference",
     *              type="boolean"
     *          ),
     *          @SWG\Property(
     *              property="managerFirstName",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="managerLastName",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="managerTitle",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="managerEmail",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="managerComment",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="permission",
     *              type="boolean"
     *          ),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=200,
     *      description="Success.",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              type="object",
     *              property="reference",
     *              @SWG\Property(
     *                  property="id",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="company",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="role",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="specialization",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="startDate",
     *                  type="datetime"
     *              ),
     *              @SWG\Property(
     *                  property="endDate",
     *                  type="datetime"
     *              ),
     *              @SWG\Property(
     *                  property="isReference",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="managerFirstName",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="managerLastName",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="managerTitle",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="managerEmail",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="managerComment",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="permission",
     *                  type="boolean"
     *              ),
     *          ),
     *          @SWG\Property(
     *              property="percentage",
     *              type="integer",
     *              example=50
     *          ),
     *          @SWG\Property(
     *              property="looking",
     *              type="boolean"
     *          )
     *     )
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
     *   )
     * )
     */
    public function createReferencesAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);
        if($profileDetails instanceof ProfileDetails){
            $references = $em->getRepository("AppBundle:CandidateReferences")->findBy(['user'=>$this->getUser()]);
            if(count($references)<5){
                $reference = new CandidateReferences($this->getUser(), $request->request->all());
                $validator = $this->get('validator');
                $errors = $validator->validate($reference, null, array('validateReferences'));
                if(count($errors) === 0){
                    $em->persist($reference);
                    $em->flush();

                    $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                    $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                    $em->persist($profileDetails);
                    $em->flush();

                    $view = $this->view([
                        'reference' => [
                            'id' => $reference->getId(),
                            'company' => $reference->getCompany(),
                            'role' => $reference->getRole(),
                            'specialization' => $reference->getSpecialization(),
                            'startDate' => $reference->getStartDate(),
                            'endDate' => $reference->getEndDate(),
                            'isReference' => $reference->getIsReference(),
                            'managerFirstName' => $reference->getManagerFirstName(),
                            'managerLastName' => $reference->getManagerLastName(),
                            'managerTitle' => $reference->getManagerTitle(),
                            'managerEmail' => $reference->getManagerEmail(),
                            'managerComment' => $reference->getManagerComment(),
                            'permission' => $reference->getPermission()
                        ],
                        'percentage'=>$profileDetails->getPercentage(),
                        'looking' => $profileDetails->getLooking()
                    ], Response::HTTP_OK);
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
                $view = $this->view(['error'=>'limit references. Max 5 references.'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'Profile Details Not Found'], Response::HTTP_NOT_FOUND);
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
     * @Rest\Put("/references/{id}",requirements={"id"="\d+"})
     * @SWG\Put(path="/api/candidate/profile/references/{id}",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Edit Candidate References",
     *   description="The method for edit candidate References",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      type="integer",
     *      default="",
     *      description="references ID"
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
     *   @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="company",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="role",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="specialization",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="startDate",
     *              type="datetime"
     *          ),
     *          @SWG\Property(
     *              property="endDate",
     *              type="datetime"
     *          ),
     *          @SWG\Property(
     *              property="isReference",
     *              type="boolean"
     *          ),
     *          @SWG\Property(
     *              property="managerFirstName",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="managerLastName",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="managerTitle",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="managerEmail",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="managerComment",
     *              type="string"
     *          ),
     *          @SWG\Property(
     *              property="permission",
     *              type="boolean"
     *          ),
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
     *              property="looking",
     *              type="boolean"
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
    public function editReferencesAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        $reference = $em->getRepository("AppBundle:CandidateReferences")->findOneBy(['id'=>$id,'user'=>$this->getUser()]);
        if($reference instanceof CandidateReferences){
            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);
            if($profileDetails instanceof ProfileDetails){
                $reference->update($request->request->all());
                $validator = $this->get('validator');
                $errors = $validator->validate($reference, null, array('validateReferences'));
                if(count($errors) === 0){
                    $em->persist($reference);
                    $em->flush();

                    $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                    $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                    $em->persist($profileDetails);
                    $em->flush();

                    $view = $this->view([
                        'percentage'=>$profileDetails->getPercentage(),
                        'looking' => $profileDetails->getLooking()
                    ], Response::HTTP_OK);
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
                $view = $this->view(['error'=>'Profile Details Not Found'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'Reference not found'], Response::HTTP_NOT_FOUND);
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
     * @Rest\Delete("/references/{id}",requirements={"id"="\d+"})
     * @SWG\Delete(path="/api/candidate/profile/references/{id}",
     *   tags={"Candidate Profile"},
     *   security={true},
     *   summary="Delete Candidate References",
     *   description="The method for Delete candidate References",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      type="integer",
     *      default="",
     *      description="references ID"
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
     *              property="looking",
     *              type="boolean"
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
    public function deleteReferencesAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        $reference = $em->getRepository("AppBundle:CandidateReferences")->findOneBy(['id'=>$id,'user'=>$this->getUser()]);
        if($reference instanceof CandidateReferences){
            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$this->getUser()]);
            if($profileDetails instanceof ProfileDetails){
                $em->remove($reference);
                $em->flush();

                $profileDetails = HelpersClass::candidateProfileCompletePercentage($profileDetails, $em);
                $profileDetails = HelpersClass::checkAutoVisible($profileDetails, $em);
                $em->persist($profileDetails);
                $em->flush();

                $view = $this->view([
                    'percentage'=>$profileDetails->getPercentage(),
                    'looking' => $profileDetails->getLooking()
                ], Response::HTTP_OK);
            }
            else{
                $view = $this->view(['error'=>'Profile Details Not Found'], Response::HTTP_NOT_FOUND);
            }
        }
        else{
            $view = $this->view(['error'=>'Reference not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

}
