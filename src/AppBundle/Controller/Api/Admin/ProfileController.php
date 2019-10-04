<?php
/**
 * Created by PhpStorm.
 * Date: 18.04.18
 * Time: 13:34
 */

namespace AppBundle\Controller\Api\Admin;


use AppBundle\Entity\EmailSchedule;
use AppBundle\Entity\NotificationAdmin;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class ProfileController
 * @package AppBundle\Controller\Api\Admin
 *
 * @Rest\Route("profile")
 * @Security("has_role('ROLE_ADMIN')")
 */
class ProfileController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Rest\Get("/")
     * @SWG\Get(path="/api/admin/profile/",
     *   tags={"Admin Profile"},
     *   security={true},
     *   summary="Get Admin Profile Details",
     *   description="The method for getting profile details for Admin",
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
     *              type="object",
     *              property="notification",
     *              description="ALL VALUE 1=immediately,2=daily,3=weekly",
     *              @SWG\Property(
     *                  property="notifyEmail",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="candidateSign",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="candidateFile",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="candidateRequestVideo",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="candidateDeactivate",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="clientSign",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="interviewSetUp",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="jobNew",
     *                  type="integer"
     *              ),
     *              @SWG\Property(
     *                  property="jobChange",
     *                  type="integer"
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
    public function getProfileAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $userDetails = $em->getRepository("AppBundle:User")->getCandidateProfile($this->getUser()->getId());
        $notificationAdmin = $em->getRepository("AppBundle:NotificationAdmin")->findOneBy(['user'=>$this->getUser()]);
        if(!$notificationAdmin instanceof NotificationAdmin){
            $notificationAdmin = new NotificationAdmin($this->getUser());
            $em->persist($notificationAdmin);
            $em->flush();
        }
        $notification = $em->getRepository("AppBundle:NotificationAdmin")->getNotify($this->getUser()->getId());
        $view = $this->view([
            'profile' => $userDetails,
            'notification' => $notification
        ], Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Put("/")
     * @SWG\Put(path="/api/admin/profile/",
     *   tags={"Admin Profile"},
     *   security={true},
     *   summary="Update Admin Profile",
     *   description="The method for updating profile for Admin",
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
     *              property="firstName",
     *              type="string",
     *              example="firstName",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="lastName",
     *              type="string",
     *              example="lastName",
     *              description="required"
     *          ),
     *          @SWG\Property(
     *              property="phone",
     *              type="string",
     *              example="phone",
     *              description="not required"
     *          ),
     *          @SWG\Property(
     *               property="email",
     *               type="string",
     *               example="email@email.com",
     *               description="required"
     *         )
     *      )
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success. Profile Updated",
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
    public function editProfileAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if($request->request->has('firstName') && $request->request->has('lastName') && $request->request->has('email')){
            $user->setFirstName($request->request->get('firstName'));
            $user->setLastName($request->request->get('lastName'));
            $user->setEmail($request->request->get('email'));
            $user->setUsername($request->request->get('email'));
            $user->setPhone(($request->request->has('phone')) ? $request->request->get('phone') : NULL );
            $errors = $this->get('validator')->validate($user, null, array('updateAdmin'));
            if(count($errors) === 0){
                $em->persist($user);
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
            $view = $this->view(['error'=>['firstName, lastName, email field required']], Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Rest\Get("/notification")
     * @SWG\Get(path="/api/admin/profile/notification",
     *   tags={"Admin Profile"},
     *   security={true},
     *   summary="Get Admin Profile Details",
     *   description="The method for getting profile details for Admin",
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
     *      description="Success. ALL VALUE 1=immediately,2=daily,3=weekly,4=monthly",
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(
     *              property="notifyEmail",
     *              type="boolean"
     *          ),
     *          @SWG\Property(
     *              property="candidateSign",
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="candidateFile",
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="candidateRequestVideo",
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="candidateDeactivate",
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="clientSign",
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="interviewSetUp",
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="jobNew",
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="jobChange",
     *              type="integer"
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
    public function getNotificationAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $notificationAdmin = $em->getRepository("AppBundle:NotificationAdmin")->findOneBy(['user'=>$this->getUser()]);
        if(!$notificationAdmin instanceof NotificationAdmin){
            $notificationAdmin = new NotificationAdmin($this->getUser());
            $em->persist($notificationAdmin);
            $em->flush();
        }
        $notification = $em->getRepository("AppBundle:NotificationAdmin")->getNotify($this->getUser()->getId());

        $view = $this->view($notification, Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Put("/notification")
     * @SWG\Put(path="/api/admin/profile/notification",
     *   tags={"Admin Profile"},
     *   security={true},
     *   summary="Update Admin Notification Details",
     *   description="The method for updating notification Details for Admin",
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
     *              property="notifyEmail",
     *              type="boolean"
     *          ),
     *          @SWG\Property(
     *              property="candidateSign",
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="candidateFile",
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="candidateRequestVideo",
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="candidateDeactivate",
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="clientSign",
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="interviewSetUp",
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="jobNew",
     *              type="integer"
     *          ),
     *          @SWG\Property(
     *              property="jobChange",
     *              type="integer"
     *          ),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success. Notification Details Update",
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
    public function editNotificationAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        if(!empty($request->request->all())){
            $notify = $em->getRepository("AppBundle:NotificationAdmin")->findOneBy(['user'=>$this->getUser()]);
            if(!$notify instanceof NotificationAdmin){
                $notify = new NotificationAdmin($this->getUser());
            }
            $notify->update($request->request->all());
            $errors = $this->get('validator')->validate($notify, null, array('updateNotify'));
            if(count($errors) === 0){
                $em->persist($notify);
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
            $view = $this->view(['error'=>['must be at least one field']], Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }
}
