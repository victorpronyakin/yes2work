<?php
/**
 * Created by PhpStorm.
 * Date: 16.04.18
 * Time: 14:48
 */

namespace AppBundle\Controller\Api\Business;

use AppBundle\Entity\CompanyDetails;
use AppBundle\Entity\NotificationClient;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MainController
 * @package AppBundle\Controller\Api\Business
 * @Rest\Route("/profile")
 * @Security("has_role('ROLE_CLIENT')")
 */
class ProfileController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Rest\Get("/")
     * @SWG\Get(path="/api/business/profile/",
     *   tags={"Business Profile"},
     *   security={true},
     *   summary="Get Business Profile Details",
     *   description="The method for getting profile details for business",
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
     *              property="profile",
     *              @SWG\Property(
     *                  property="user",
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
     *                      property="jobTitle",
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
     *              @SWG\Property(
     *                  property="company",
     *                  type="object",
     *                  @SWG\Property(
     *                      property="name",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="address",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="addressCountry",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="addressState",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="addressZipCode",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="addressCity",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="addressSuburb",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="addressStreet",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="addressStreetNumber",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="addressBuildName",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="addressUnit",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="companySize",
     *                      type="integer",
     *                      description="1= 1-10E, 2= 11-50E, 3= 50-200E, 4= 200-1000E,5= >1000E"
     *                  ),
     *                  @SWG\Property(
     *                      property="jse",
     *                      type="boolean",
     *                      description="0|1"
     *                  ),
     *                  @SWG\Property(
     *                      property="industry",
     *                      type="array",
     *                      @SWG\Items(type="string")
     *                  ),
     *                  @SWG\Property(
     *                      property="description",
     *                      type="string"
     *                  ),
     *              ),
     *          ),
     *          @SWG\Property(
     *              property="notification",
     *              type="object",
     *              @SWG\Property(
     *                  property="notifyEmail",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="newCandidateStatus",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="newCandidate",
     *                  type="integer",
     *                  description="2 or 3"
     *              ),
     *              @SWG\Property(
     *                  property="jobApproveStatus",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="jobApprove",
     *                  type="integer",
     *                  description="1,2,3"
     *              ),
     *              @SWG\Property(
     *                  property="jobDeclineStatus",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="jobDecline",
     *                  type="integer",
     *                  description="1,2,3"
     *              ),
     *              @SWG\Property(
     *                  property="candidateApplicantStatus",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="candidateApplicant",
     *                  type="integer",
     *                  description="1,2,3"
     *              ),
     *              @SWG\Property(
     *                  property="candidateDeclineStatus",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="candidateDecline",
     *                  type="integer",
     *                  description="1,2,3"
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
    public function getProfileDetailsAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $profileInfo = $em->getRepository("AppBundle:User")->getBusinessProfile($this->getUser()->getId());
        $companyDetails = $em->getRepository("AppBundle:CompanyDetails")->getBusinessCompanyDetails($this->getUser()->getId());
        $checkNotification = $em->getRepository('AppBundle:NotificationClient')->findOneBy(['user'=>$this->getUser()]);
        if(!$checkNotification instanceof NotificationClient){
            $checkNotification = new NotificationClient($this->getUser());
            $em->persist($checkNotification);
            $em->flush();
        }
        $notification = $em->getRepository("AppBundle:NotificationClient")->getNotify($this->getUser()->getId());

        $view = $this->view([
            'profile' => [
                'user'=>$profileInfo,
                'company'=>$companyDetails
            ],
            'notification' => $notification

        ], Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Put("/")
     * @SWG\Put(path="/api/business/profile/",
     *   tags={"Business Profile"},
     *   security={true},
     *   summary="Update Business Profile",
     *   description="The method for updating profile for business",
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
     *                  property="jobTitle",
     *                  type="string",
     *                  example="jobTitle",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="phone",
     *                  type="string",
     *                  example="phone",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="email",
     *                  type="string",
     *                  example="email@email.com",
     *                  description="required"
     *              )
     *          ),
     *          @SWG\Property(
     *              property="company",
     *              type="object",
     *              @SWG\Property(
     *                  property="name",
     *                  type="string",
     *                  example="name",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="address",
     *                  type="string",
     *                  example="address",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressCountry",
     *                  type="string",
     *                  example="addressCountry",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressState",
     *                  type="string",
     *                  example="addressState",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressZipCode",
     *                  type="string",
     *                  example="addressZipCode",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressCity",
     *                  type="string",
     *                  example="addressCity",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressSuburb",
     *                  type="string",
     *                  example="addressSuburb",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressStreet",
     *                  type="string",
     *                  example="addressStreet",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressStreetNumber",
     *                  type="string",
     *                  example="addressStreetNumber",
     *                  description="required"
     *              ),
     *              @SWG\Property(
     *                  property="addressBuildName",
     *                  type="string",
     *                  example="addressBuildName"
     *              ),
     *              @SWG\Property(
     *                  property="addressUnit",
     *                  type="string",
     *                  example="addressUnit"
     *              ),
     *              @SWG\Property(
     *                  property="companySize",
     *                  type="integer",
     *                  description="required. 1= 1-10E, 2= 11-50E, 3= 50-200E, 4= 200-1000E,5= >1000E",
     *                  example=1
     *              ),
     *              @SWG\Property(
     *                  property="jse",
     *                  type="boolean",
     *                  description="required. true|false",
     *                  example=true
     *              ),
     *              @SWG\Property(
     *                  property="industry",
     *                  type="array",
     *                  @SWG\Items(type="string"),
     *                  description="required.",
     *              ),
     *              @SWG\Property(
     *                  property="description",
     *                  type="string",
     *                  description="required",
     *                  example="description"
     *              ),
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success. Business Profile Updated",
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
    public function updateProfileInfoAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($request->request->has('user') && $request->request->has('company')){
            $userDetails = $request->request->get('user');
            $companyInfo = $request->request->get('company');
            if(isset($userDetails['firstName']) && isset($userDetails['lastName']) && isset($userDetails['email']) && isset($userDetails['phone']) && isset($userDetails['jobTitle'])){
                $user->setFirstName($userDetails['firstName']);
                $user->setLastName($userDetails['lastName']);
                $user->setEmail($userDetails['email']);
                $user->setUsername($userDetails['email']);
                $user->setPhone($userDetails['phone']);
                $user->setJobTitle($userDetails['jobTitle']);
                $errors = $this->get('validator')->validate($user, null, array('updateClient'));
                if(count($errors) === 0){
                    $em->persist($user);
                    $companyDetails = $em->getRepository("AppBundle:CompanyDetails")->findOneBy(['user'=>$user]);
                    $companyDetails->update($companyInfo);
                    $errors = $this->get('validator')->validate($companyDetails, null, array('updateCompany'));
                    if(count($errors) === 0){
                        $em->persist($companyDetails);
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
                else {
                    $error_description = [];
                    foreach ($errors as $er) {
                        $error_description[] = $er->getMessage();
                    }
                    $view = $this->view(['error'=>$error_description], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>['all user fields is required']], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>['fields user and company is required']], Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/firstPopUp")
     * @SWG\Get(path="/api/business/profile/firstPopUp",
     *   tags={"Business Profile"},
     *   security={true},
     *   summary="Get Business firstPopUp status",
     *   description="The method for getting status firstPopUp for business",
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
     *              property="firstPopUp",
     *              type="boolean",
     *              description="IF FALSE NEED SHOW POPUP, and IF TRUE NOT NEED SHOW"
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
    public function getStatusFirstPopUpAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $companyDetails = $em->getRepository("AppBundle:CompanyDetails")->findOneBy(['user'=>$user]);
        if($companyDetails instanceof CompanyDetails){
            $view = $this->view(['firstPopUp'=>$companyDetails->getFirstPopUp()], Response::HTTP_OK);
        }
        else{
            $view = $this->view(['firstPopUp'=>false], Response::HTTP_OK);

        }
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Patch("/firstPopUp")
     * @SWG\Patch(path="/api/business/profile/firstPopUp",
     *   tags={"Business Profile"},
     *   security={true},
     *   summary="Set Business firstPopUp status",
     *   description="The method for Setting status firstPopUp for business",
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
     *              property="firstPopUp",
     *              type="boolean",
     *              example=true,
     *              description="required, ONLY TRUE"
     *          )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success. Status Set"
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
    public function setStatusFirstPopUpAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $companyDetails = $em->getRepository("AppBundle:CompanyDetails")->findOneBy(['user'=>$user]);
        if($request->request->has('firstPopUp') && $request->request->get('firstPopUp') == true ){
            $companyDetails->setFirstPopUp(true);
            $em->persist($companyDetails);
            $em->flush();

            $view = $this->view([], Response::HTTP_NO_CONTENT);
        }
        else{
            $view = $this->view(['error'=>'field firstPopUp is required and should be = true'], Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }
}