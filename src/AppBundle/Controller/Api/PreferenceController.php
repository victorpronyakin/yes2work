<?php
/**
 * Created by PhpStorm.
 * Date: 17.04.18
 * Time: 12:26
 */

namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PreferenceController
 * @package AppBundle\Controller\Api
 *
 * @Route("/preference")
 * @Security("has_role('ROLE_USER')")
 */
class PreferenceController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Put("/change_password")
     * @SWG\Put(path="/api/preference/change_password",
     *   tags={"Preference"},
     *   security={true},
     *   summary="Change Password",
     *   description="The method Change password",
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
     *              type="password",
     *              property="old_password",
     *              example="old_password",
     *              description="required",
     *          ),
     *          @SWG\Property(
     *              type="password",
     *              property="new_password",
     *              example="password",
     *              description="required",
     *          ),
     *          @SWG\Property(
     *              type="password",
     *              property="confirm_password",
     *              example="password",
     *              description="required",
     *          ),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=204,
     *      description="Success. Password Changed",
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
     *   )
     * )
     */
    public function changePasswordAction(Request $request){
        $user = $this->getUser();
        if($request->request->has('old_password') && !empty($request->request->get('old_password'))
            && $request->request->has('new_password') && !empty($request->request->get('new_password'))
            && $request->request->has('confirm_password') && !empty($request->request->get('confirm_password'))
        ){
            if($request->request->get('new_password') == $request->request->get('confirm_password')){
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $salt = $user->getSalt();

                if($encoder->isPasswordValid($user->getPassword(), $request->request->get('old_password'), $salt)) {
                    $user->setPlainPassword($request->request->get('new_password'));
                    $this->get('fos_user.user_manager')->updateUser($user);
                    $view = $this->view([], Response::HTTP_NO_CONTENT);
                } else {
                    $view = $this->view(['error'=>'Old password is not correct'], Response::HTTP_BAD_REQUEST);
                }
            }
            else{
                $view = $this->view(['error'=>'Passwords do not match'], Response::HTTP_BAD_REQUEST);
            }
        }
        else{
            $view = $this->view(['error'=>'Old password, New password and Confirm password is required'], Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }
}