<?php
/**
 * Created by PhpStorm.
 * Date: 13.06.18
 * Time: 13:11
 */

namespace AppBundle\Controller\Api\Admin;

use AppBundle\Entity\Logging;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

/**
 * Class LoggingController
 * @package AppBundle\Controller\Api\Admin
 *
 * @Rest\Route("logging")
 * @Security("has_role('ROLE_ADMIN')")
 */
class LoggingController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/")
     * @SWG\Get(path="/api/admin/logging/",
     *   tags={"Admin Logging"},
     *   security={true},
     *   summary="Get All Logging",
     *   description="The method for getting all logging for admin",
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
     *      name="orderBy",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="Timestamp, User, Action"
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
     *      name="adminID",
     *      in="query",
     *      required=false,
     *      type="integer",
     *      description="search by admin"
     *   ),
     *   @SWG\Parameter(
     *      name="type",
     *      in="query",
     *      required=false,
     *      type="integer",
     *      default="",
     *      description="search by type,
     *      1 = Create Candidate
     *      2 = Edit Candidate
     *      3 = Activate Candidate
     *      4 = Deactivate Candidate
     *      5 = Remove Candidate
     *      6 = Approve File
     *      7 = Decline File
     *      8 = Approve Candidate
     *      9 = Decline Candidate
     *      10 = Create Business
     *      11 = Edit Business
     *      12 = Activate Business
     *      13 = Deactivate Business
     *      14 = Remove Business
     *      15 = Approve Business
     *      16 = Decline Business
     *      17 = Create Job
     *      18 = Edit Job
     *      19 = Open Job
     *      20 = Close Job
     *      21 = Remove Job
     *      22 = Approve Job
     *      23 = Decline Job
     *      24 = Create Admin
     *      25 = Edit Admin
     *      26 = Remove Admin
     *      27 = Approve Video
     *      28 = Decline Video
     *      29 = Upload Video
     *      30 = Remove Video"
     *   ),
     *   @SWG\Parameter(
     *      name="search",
     *      in="query",
     *      required=false,
     *      type="string",
     *      description="search by action item"
     *   ),
     *   @SWG\Parameter(
     *      name="dateStart",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="search by >= dateStart"
     *   ),
     *   @SWG\Parameter(
     *      name="dateEnd",
     *      in="query",
     *      required=false,
     *      type="string",
     *      default="",
     *      description="search by <= dateEnd"
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
     *                      property="adminID",
     *                      type="integer"
     *                  ),
     *                  @SWG\Property(
     *                      property="firstName",
     *                      type="string"
     *                   ),
     *                   @SWG\Property(
     *                      property="lastName",
     *                      type="string"
     *                   ),
     *                   @SWG\Property(
     *                      property="type",
     *                      type="integer"
     *                   ),
     *                   @SWG\Property(
     *                      property="action",
     *                      type="string"
     *                   ),
     *                   @SWG\Property(
     *                      property="itemID",
     *                      type="integer"
     *                   ),
     *                   @SWG\Property(
     *                      property="created",
     *                      type="datetime",
     *                      example="2018-09-09"
     *                   )
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
    public function getAllLoggingAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $params = $request->query->all();
        $logging = $em->getRepository("AppBundle:Logging")->getAllLogging($params);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $this->generateDataLogging($logging, $params),
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
     * @param array $items
     * @param array $params
     * @return array
     */
    private function generateDataLogging(array $items, $params = array()){
        $logging = [];
        $actions = [
            'Create Candidate',
            'Edit Candidate',
            'Activate Candidate',
            'Deactivate Candidate',
            'Remove Candidate',
            'Approve File',
            'Decline File',
            'Approve Candidate',
            'Decline Candidate',
            'Create Business',
            'Edit Business',
            'Activate Business',
            'Deactivate Business',
            'Remove Business',
            'Approve Business',
            'Decline Business',
            'Create Job',
            'Edit Job',
            'Open Job',
            'Close Job',
            'Remove Job',
            'Approve Job',
            'Decline Job',
            'Create Admin',
            'Edit Admin',
            'Remove Admin',
            'Approve Video',
            'Decline Video',
            'Upload Video',
            'Remove Video'
        ];

        if(!empty($items)){
            foreach ($items as $log){
                if($log instanceof Logging){
                    $logging[] = [
                        'id' => $log->getId(),
                        'adminID' => $log->getUser()->getId(),
                        'firstName' => $log->getUser()->getFirstName(),
                        'lastName' => $log->getUser()->getLastName(),
                        'type' => $log->getType(),
                        'action' => (isset($actions[$log->getType()-1])) ? $actions[$log->getType()-1]." - ".$log->getTitle() : $log->getTitle(),
                        'itemID' => $log->getItemID(),
                        'created' => $log->getCreated()
                    ];
                }
            }
        }

        if(isset($params['orderBy']) && !empty($params['orderBy']) && in_array($params['orderBy'], ['Timestamp', 'User', 'Action'])){
            if(isset($params['orderSort']) && !empty($params['orderSort']) && in_array($params['orderSort'], ['asc', 'desc'])){
                if($params['orderBy'] == 'Timestamp'){
                    if($params['orderSort'] == 'asc'){
                        usort($logging, function($a, $b) {
                            return $a['created'] > $b['created'];
                        });
                    }
                    else{
                        usort($logging, function($a, $b) {
                            return $a['created'] < $b['created'];
                        });
                    }
                }
                elseif ($params['orderBy'] == 'User'){
                    if($params['orderSort'] == 'asc'){
                        usort($logging, function($a, $b) {
                            return $a['firstName'] > $b['firstName'];
                        });
                    }
                    else{
                        usort($logging, function($a, $b) {
                            return $a['firstName'] < $b['firstName'];
                        });
                    }
                }
                elseif ($params['orderBy'] == 'Action'){
                    if($params['orderSort'] == 'asc'){
                        usort($logging, function($a, $b) {
                            return $a['action'] > $b['action'];
                        });
                    }
                    else{
                        usort($logging, function($a, $b) {
                            return $a['action'] < $b['action'];
                        });
                    }
                }
            }
        }

        return $logging;
    }

}
