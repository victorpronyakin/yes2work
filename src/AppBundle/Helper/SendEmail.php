<?php
/**
 * Created by PhpStorm.
 * Date: 18.06.18
 * Time: 11:11
 */

namespace AppBundle\Helper;


use AppBundle\Entity\EmailSchedule;
use AppBundle\Entity\NotificationAdmin;
use AppBundle\Entity\NotificationCandidate;
use AppBundle\Entity\NotificationClient;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

/**
 * Class SendEmail
 * @package AppBundle\Helper
 */
class SendEmail
{
    /**
     * ROLE CONST
     */
    const ROLE_ADMIN = 'TYPE_ADMIN';
    const ROLE_CANDIDATE = 'TYPE_CANDIDATE';
    const ROLE_CLIENT = 'TYPE_CLIENT';

    /**
     * START Admin
     */
    const TYPE_ADMIN_CANDIDATE_SIGN = 'candidateSign';
    const TYPE_ADMIN_CANDIDATE_FILE = 'candidateFile';
    const TYPE_ADMIN_CANDIDATE_REQUEST_VIDEO = 'candidateRequestVideo';
    const TYPE_ADMIN_CANDIDATE_DEACTIVATE = 'candidateDeactivate';
    const TYPE_ADMIN_CLIENT_SIGN = 'clientSign';
    const TYPE_ADMIN_INTERVIEW_SET_UP = 'interviewSetUp';
    const TYPE_ADMIN_JOB_NEW = 'jobNew';
    const TYPE_ADMIN_JOB_CHANGE = 'jobChange';
    /**
     * END ADMIN
     */

    /**
     * START CLIENT
     */
    const TYPE_CLIENT_NEW_CANDIDATE = 'newCandidate';
    const TYPE_CLIENT_JOB_APPROVE = 'jobApprove';
    const TYPE_CLIENT_JOB_DECLINE = 'jobDecline';
    const TYPE_CLIENT_CANDIDATE_APPLICANT = 'candidateApplicant';
    const TYPE_CLIENT_CANDIDATE_DECLINE = 'candidateDecline';
    /**
     * END CLIENT
     */

    /**
     * START CANDIDATE
     */
    const TYPE_CANDIDATE_INTERVIEW_REQUEST = 'interviewRequest';
    const TYPE_CANDIDATE_APPLICATION_DECLINE = 'applicationDecline';
    const TYPE_CANDIDATE_NEW_JOB_LOADED = 'newJobLoaded';
    const TYPE_CANDIDATE_JOB_ENDING_SOON = 'jobEndingSoon';
    const TYPE_CANDIDATE_DOCUMENT_APPROVE = 'documentApproveStatus';
    const TYPE_CANDIDATE_REMINDER_PROFILE = 'reminderProfile';
    /**
     * END CANDIDATE
     */


    /**
     * @param $role
     * @param $type
     * @return bool
     * @throws \ReflectionException
     */
    private static function getValidType($role, $type) {
        $oClass = new \ReflectionClass(__CLASS__);
        $constant = $oClass->getConstants();
        $key = array_search($type, $constant);
        if($key != false){
            if (strpos($key, $role) !== false) {
                return $constant[$key];
            }
        }
        return false;
    }

    /**
     * @param EntityManager $em
     * @param \Swift_Message $_message
     * @param \Swift_Mailer $_mailer
     * @param $emailData
     * @param null $type
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public static function sendEmailForAdmin(EntityManager $em, \Swift_Message $_message, \Swift_Mailer $_mailer, $emailData, $type=null){
        $admins = $em->getRepository("AppBundle:NotificationAdmin")->findBy(['notifyEmail'=>true]);
        if(!empty($type)){
            $key = self::getValidType(SendEmail::ROLE_ADMIN, $type);
        }
        else{
            $key = false;
        }
        foreach ($admins as $admin){
            if($admin instanceof NotificationAdmin){
                if($key != false){
                    if(property_exists(NotificationAdmin::class, $key)){
                       $method = 'get'.ucfirst($key);
                       if(method_exists($admin, $method)){
                           $delay = $admin->$method();
                           if($delay == 1){
                               $_message->setTo($admin->getUser()->getEmail());
                               try{
                                   $_mailer->send($_message);
                               }catch(\Swift_TransportException $e){

                               }
                           }
                           elseif(in_array($delay, [2,3]) && $type != SendEmail::TYPE_ADMIN_CANDIDATE_DEACTIVATE){
                               $emailSchedule = new EmailSchedule($admin->getUser(), $emailData, $key, $delay);
                               $em->persist($emailSchedule);
                               $em->flush();
                           }
                       }
                       else{
                           $_message->setTo($admin->getUser()->getEmail());
                           try{
                               $_mailer->send($_message);
                           }catch(\Swift_TransportException $e){

                           }
                       }
                    }
                    else{
                        $_message->setTo($admin->getUser()->getEmail());
                        try{
                            $_mailer->send($_message);
                        }catch(\Swift_TransportException $e){

                        }
                    }
                }
                else{
                    $_message->setTo($admin->getUser()->getEmail());
                    try{
                        $_mailer->send($_message);
                    }catch(\Swift_TransportException $e){

                    }
                }
            }
        }
    }

    /**
     * @param User $client
     * @param EntityManager $em
     * @param \Swift_Message $_message
     * @param \Swift_Mailer $_mailer
     * @param $emailData
     * @param null $type
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public static function sendEmailForClient(User $client, EntityManager $em, \Swift_Message $_message, \Swift_Mailer $_mailer, $emailData, $type=null){
        $clientNotify = $em->getRepository("AppBundle:NotificationClient")->findOneBy(['user'=>$client,'notifyEmail'=>true]);
        if($clientNotify instanceof NotificationClient){
            if(!empty($type)){
                $key = self::getValidType(SendEmail::ROLE_CLIENT, $type);
            }
            else{
                $key = false;
            }
            if($key != false){
                if(property_exists(NotificationClient::class, $key)){
                    $method = 'get'.ucfirst($key);
                    $methodCheck = 'get'.ucfirst($key).'Status';
                    if(method_exists($clientNotify, $method) && method_exists($clientNotify, $methodCheck)){
                        if($clientNotify->$methodCheck() == true){
                            $delay = $clientNotify->$method();
                            if($delay == 1 || $type == self::TYPE_CLIENT_JOB_APPROVE){
                                if($clientNotify->getNotifyEmail() == true){
                                    $_message->setTo($clientNotify->getUser()->getEmail());
                                    try{
                                        $_mailer->send($_message);
                                    }catch(\Swift_TransportException $e){

                                    }
                                }
                            }
                            elseif(in_array($delay, [2,3])){
                                $emailSchedule = new EmailSchedule($clientNotify->getUser(), $emailData, $key, $delay);
                                $em->persist($emailSchedule);
                                $em->flush();
                            }
                        }
                    }
                    else{
                        $_message->setTo($clientNotify->getUser()->getEmail());
                        try{
                            $_mailer->send($_message);
                        }catch(\Swift_TransportException $e){

                        }
                    }
                }
                else{
                    $_message->setTo($clientNotify->getUser()->getEmail());
                    try{
                        $_mailer->send($_message);
                    }catch(\Swift_TransportException $e){

                    }
                }
            }
            else{
                $_message->setTo($clientNotify->getUser()->getEmail());
                try{
                    $_mailer->send($_message);
                }catch(\Swift_TransportException $e){

                }
            }
        }
    }

    /**
     * @param User $candidate
     * @param EntityManager $em
     * @param \Swift_Message $_message
     * @param \Swift_Mailer $_mailer
     * @param $emailData
     * @param null $type
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public static function sendEmailForCandidate(User $candidate, EntityManager $em, \Swift_Message $_message, \Swift_Mailer $_mailer, $emailData, $type=null){
        $candidateNotify = $em->getRepository("AppBundle:NotificationCandidate")->findOneBy(['user'=>$candidate]);
        if($candidateNotify instanceof NotificationCandidate){
            if(!empty($type)){
                $key = self::getValidType(SendEmail::ROLE_CANDIDATE, $type);
            }
            else{
                $key = false;
            }
            if($key != false){
                if(property_exists(NotificationCandidate::class, $key)){
                    $method = 'get'.ucfirst($key);
                    $methodCheck = 'get'.ucfirst($key).'Status';
                    if(method_exists($candidateNotify, $method) && method_exists($candidateNotify, $methodCheck)){
                        if($candidateNotify->$methodCheck() == true){
                            $delay = $candidateNotify->$method();
                            if($delay == 1){
                                if($candidateNotify->getNotifyEmail() == true){
                                    $_message->setTo($candidateNotify->getUser()->getEmail());
                                    try{
                                        $_mailer->send($_message);
                                    }catch(\Swift_TransportException $e){

                                    }
                                }
                                if($candidateNotify->getNotifySMS() == true){
                                    self::sendSMSNotifyCandidate($em, $candidate, $type,$emailData);
                                }
                            }
                            elseif(in_array($delay, [2,3])){
                                $emailSchedule = new EmailSchedule($candidateNotify->getUser(), $emailData, $key, $delay);
                                $em->persist($emailSchedule);
                                $em->flush();
                            }
                        }
                    }
                    else{
                        if($candidateNotify->getNotifyEmail() == true){
                            $_message->setTo($candidateNotify->getUser()->getEmail());
                            try{
                                $_mailer->send($_message);
                            }catch(\Swift_TransportException $e){

                            }
                        }
                        if($candidateNotify->getNotifySMS() == true){
                            self::sendSMSNotifyCandidate($em, $candidate, $type,$emailData);
                        }
                    }
                }
                else{
                    if($candidateNotify->getNotifyEmail() == true){
                        $_message->setTo($candidateNotify->getUser()->getEmail());
                        try{
                            $_mailer->send($_message);
                        }catch(\Swift_TransportException $e){

                        }
                    }
                    if($candidateNotify->getNotifySMS() == true){
                        self::sendSMSNotifyCandidate($em, $candidate, $type,$emailData);
                    }
                }
            }
            else{
                if($candidateNotify->getNotifyEmail() == true){
                    $_message->setTo($candidateNotify->getUser()->getEmail());
                    try{
                        $_mailer->send($_message);
                    }catch(\Swift_TransportException $e){

                    }
                }
                if($candidateNotify->getNotifySMS() == true){
                    self::sendSMSNotifyCandidate($em, $candidate, $type,$emailData);
                }
            }
        }
    }

    /**
     * @param EntityManager $em
     * @param User $user
     * @param $type
     * @param array $params
     * @throws \ReflectionException
     */
    public static function sendSMSNotifyCandidate(EntityManager $em, User $user, $type, $params=array()){
        if(!empty($user->getPhone())){
            if(substr($user->getPhone(), 0, 1) == '+'){
                $number = substr($user->getPhone(), 1);
            }
            else{
                $number = $user->getPhone();
            }
            $candidateNotify = $em->getRepository("AppBundle:NotificationCandidate")->findOneBy(['user'=>$user, 'notifySMS'=>true]);
            if($candidateNotify instanceof NotificationCandidate){
                if($type == self::TYPE_CANDIDATE_INTERVIEW_REQUEST){
                    $message = "Yes2Work Interview Request!\n\n";
                    $message .= "Hi ".((isset($params['user']['firstName'])) ? $params['user']['firstName'] : "")."!\n\n";
                    $message .= "A ".((isset($params['jse']) && $params['jse'] == true) ? "Listed" : "Unlisted")." ";
                    $message .= ((isset($params['industry'])) ? $params['industry'] : "")." business situated in ";
                    $message .= ((isset($params['city'])) ? $params['city'] : "")." is interested in interviewing you for a ";
                    $message .= ((isset($params['jobTitle'])) ? $params['jobTitle'] : "")." role!\n\n";
                    $message .= "One of our consultants at Yes2Work will be in touch with you shortly to discuss this with you.\n\n";
                    $message .= "Login to your Yes2Work profile to see more details.\n\n";
                    $message .= "https://app.yes2work.co.za \n\n";
                    $message .= "The Yes2Work Team\n";
                    $message .= "support@yes2work.co.za";
                }
                elseif ($type == self::TYPE_CANDIDATE_NEW_JOB_LOADED){
                    $message = "Yes2Work Job Alert!\n\n";
                    $message .= "Hi ".((isset($params['user']['firstName'])) ? $params['user']['firstName'] : "")."!\n\n";
                    $message .= "A ".((isset($params['jse']) && $params['jse'] == true) ? "Listed" : "Unlisted")." ";
                    $message .= ((isset($params['industry'])) ? $params['industry'] : "")." business situated in ";
                    $message .= ((isset($params['city'])) ? $params['city'] : "")." has loaded a new ";
                    $message .= ((isset($params['jobTitle'])) ? $params['jobTitle'] : "")." job which you may be interested in.\n\n";
                    $message .= "Please Login to Yes2Work to find out more and either Apply or Decline this opportunity.\n\n";
                    $message .= "https://app.yes2work.co.za \n\n";
                    $message .= "This job expires on: ".((isset($params['closureDate']) && $params['closureDate'] instanceof \DateTime) ? $params['closureDate']->format("Y-m-d") : "soon" ).".\n\n";
                    $message .= "The Yes2Work Team\n";
                    $message .= "support@yes2work.co.za";
                }
                elseif ($type == self::TYPE_CANDIDATE_APPLICATION_DECLINE){
                    $message = "Yes2Work Application declined\n\n";
                    $message .= "Hi ".((isset($params['user']['firstName'])) ? $params['user']['firstName'] : "")."!\n\n";
                    $message .= "Unfortunately, your application for ";
                    $message .= ((isset($params['jobTitle'])) ? $params['jobTitle'] : "")." job at a ";
                    $message .= ((isset($params['jse']) && $params['jse'] == true) ? "Listed" : "Unlisted")." ";
                    $message .= ((isset($params['industry'])) ? $params['industry'] : "")." business situated in ";
                    $message .= ((isset($params['city'])) ? $params['city'] : "")." has not been successful.\n\n";
                    $message .= "Login to your Yes2Work profile to see more details.\n\n";
                    $message .= "https://app.yes2work.co.za \n\n";
                    $message .= "The Yes2Work Team\n";
                    $message .= "support@yes2work.co.za";
                }

                if(isset($number) && !empty($number) && isset($message) && !empty($message)){
                    $api = new SendSMSApp();
                    $result = $api->message_send($number, $message, 'Yes2Work');
                }
            }
        }
    }
}