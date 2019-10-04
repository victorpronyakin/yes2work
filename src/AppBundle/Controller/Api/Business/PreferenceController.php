<?php
/**
 * Created by PhpStorm.
 * Date: 18.04.18
 * Time: 15:51
 */

namespace AppBundle\Controller\Api\Business;


use AppBundle\Entity\EmailSchedule;
use AppBundle\Entity\NotificationClient;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class PreferenceController
 * @package AppBundle\Controller\Api\Business
 * @Rest\Route("preference")
 * @Security("has_role('ROLE_CLIENT')")
 */
class PreferenceController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Rest\Get("/notification")
     * @SWG\Get(path="/api/business/preference/notification",
     *   tags={"Business Preference"},
     *   security={true},
     *   summary="Get Business Preference Notification",
     *   description="The method for getting Preference Notification for Business",
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
    public function getNotificationAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $checkNotification = $em->getRepository('AppBundle:NotificationClient')->findOneBy(['user'=>$this->getUser()]);
        if(!$checkNotification instanceof NotificationClient){
            $checkNotification = new NotificationClient($this->getUser());
            $em->persist($checkNotification);
            $em->flush();
        }
        $notification = $em->getRepository("AppBundle:NotificationClient")->getNotify($this->getUser()->getId());

        $view = $this->view(['notification'=>$notification], Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Put("/notification")
     * @SWG\Put(path="/api/business/preference/notification",
     *   tags={"Business Preference"},
     *   security={true},
     *   summary="Update Business Notification Details",
     *   description="The method for updating notification Details for Business",
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
            $notify = $em->getRepository("AppBundle:NotificationClient")->findOneBy(['user'=>$this->getUser()]);
            if(!$notify instanceof NotificationClient){
                $notify = new NotificationClient($this->getUser());
            }
            $notify->update($request->request->all());
            $errors = $this->get('validator')->validate($notify, null, array('updateNotify'));
            if(count($errors) === 0){
                $em->persist($notify);
                $em->flush();
                foreach ($request->request->all() as $typeNotify=>$value){
                    $emailsSchedule = $em->getRepository("AppBundle:EmailSchedule")->findBy(['user'=>$this->getUser(), 'type'=>$typeNotify]);
                    if(!empty($emailsSchedule)){
                        foreach ($emailsSchedule as $emailSchedule){
                            if($emailSchedule instanceof EmailSchedule){
                                if($value < 2){
                                    $em->remove($emailSchedule);
                                }
                                else{
                                    $emailSchedule->setDelay($value);
                                    $em->persist($emailSchedule);
                                }
                            }
                        }
                        $em->flush();
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
            $view = $this->view(['error'=>['must be at least one field']], Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }
}