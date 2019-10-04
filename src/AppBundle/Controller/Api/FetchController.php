<?php
/**
 * Created by PhpStorm.
 * Date: 01.06.18
 * Time: 15:32
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Applicants;
use AppBundle\Entity\CompanyDetails;
use AppBundle\Entity\EmailSchedule;
use AppBundle\Entity\HideJob;
use AppBundle\Entity\Job;
use AppBundle\Entity\NotificationAdmin;
use AppBundle\Entity\NotificationCandidate;
use AppBundle\Entity\NotificationClient;
use AppBundle\Entity\Opportunities;
use AppBundle\Entity\ProfileDetails;
use AppBundle\Entity\User;
use AppBundle\Helper\SendEmail;
use AppBundle\Helper\SendSMSApp;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FetchController
 * @package AppBundle\Controller\Api
 *
 * @Route("/fetch")
 */
class FetchController extends FOSRestController
{
    /**
     * @param Request $request
     * @Rest\Get("/send_email")
     */
    public function sendEmailAction(Request $request){
        shell_exec('php ../bin/console swiftmailer:spool:send --env=prod');

        exit();
    }

    /**
     * @param Request $request
     * @throws \Exception
     *
     * @Rest\Get("/closure_job")
     */
    public function closureJobAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $closureJobs = $em->getRepository("AppBundle:Job")->getClosureJobs();
        if(!empty($closureJobs)){
            foreach ($closureJobs as $job){
                if($job instanceof Job){
                    $message = (new \Swift_Message('A Yes2Work job you loaded is closing soon'))
                        ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                        ->setTo($job->getUser()->getEmail())
                        ->setBody(
                            $this->renderView('emails/client/closure_job.html.twig', [
                                'job' => $job,
                                'link' => $request->getSchemeAndHttpHost().'/business/jobs/edit/'.$job->getId()
                            ]),
                            'text/html'
                        );
                    try{
                        $this->get('mailer')->send($message);
                    }catch(\Exception $e){}
                }
            }
        }

        exit();
    }

    /**
     * @param Request $request
     *
     * @Rest\Get("/expire_job")
     */
    public function expiredJobAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $jobsExpired = $em->getRepository("AppBundle:Job")->getExpiredJobs();
        if(!empty($jobsExpired)){
            foreach ($jobsExpired as $jobExpired){
                if($jobExpired instanceof Job){
                    $jobExpired->setStatus(false);
                    $em->persist($jobExpired);
                    $em->flush();

                    $applicants = $em->getRepository("AppBundle:Applicants")->findBy(['job'=>$jobExpired, 'status'=>1]);
                    foreach ($applicants as $applicant){
                        if($applicant instanceof Applicants){
                            $em->remove($applicant);
                            $em->flush();
                        }
                    }

                    $hideJobs = $em->getRepository("AppBundle:HideJob")->findBy(['job'=>$jobExpired]);
                    foreach ($hideJobs as $hideJob){
                        if($hideJob instanceof HideJob){
                            $em->remove($hideJob);
                            $em->flush();
                        }
                    }
                }
            }
        }
        $jobsExpiration = $em->getRepository("AppBundle:Job")->getExpirationJobs(1);
        if(!empty($jobsExpiration)){
            foreach ($jobsExpiration as $jobExpiration){
                if($jobExpiration instanceof Job){
                    $message = (new \Swift_Message('A Yes2Work job you loaded is expiring soon'))
                        ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                        ->setTo($jobExpiration->getUser()->getEmail())
                        ->setBody(
                            $this->renderView('emails/client/job_expiration.html.twig', [
                                'job' => $jobExpiration,
                                'link' => $request->getSchemeAndHttpHost().'/business/jobs/edit/'.$jobExpiration->getId()
                            ]),
                            'text/html'
                        );
                    try{
                        $this->get('mailer')->send($message);
                    }catch(\Swift_TransportException $e){

                    }
                }
            }
        }
        exit();
    }

    /**
     * @param Request $request
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     *
     * @Rest\Get("/send_daily_notify")
     */
    public function sendDailyNotifyAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        /**
         * GENERATE Job Posts Ending Soon
         */
        $notifyCandidatesJobEnding = $em->getRepository("AppBundle:NotificationCandidate")->findBy(['jobEndingSoonStatus'=>true,'jobEndingSoon'=>2]);
        if(!empty($notifyCandidatesJobEnding)){
            $now = new \DateTime();
            foreach ($notifyCandidatesJobEnding as $notifyJobEnding){
                if($notifyJobEnding instanceof NotificationCandidate){
                    if($notifyJobEnding->getNotifyEmail() == true || $notifyJobEnding->getNotifySMS() == true){
                        $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$notifyJobEnding->getUser()]);
                        if($profileDetails instanceof ProfileDetails && $profileDetails->getLooking() == true){
                            $opportunities = $em->getRepository("AppBundle:Opportunities")->findBy(['candidate'=>$notifyJobEnding->getUser(), 'status'=>1]);
                            if(!empty($opportunities)){
                                $endingJobs = [];
                                foreach ($opportunities as $opportunity){
                                    if($opportunity instanceof Opportunities){
                                        $job = $opportunity->getJob();
                                        if($job instanceof Job && $job->getStatus() == true && $job->getApprove() == true){
                                            $closureDate = $job->getClosureDate();
                                            if($closureDate instanceof \DateTime){
                                                $diffDate = $closureDate->diff($now);
                                                if(isset($diffDate->days) && $diffDate->days<4){
                                                    $companyDetails = $em->getRepository("AppBundle:CompanyDetails")->findOneBy(['user'=>$job->getUser()]);
                                                    $endingJobs[]=[
                                                        'jse' => ($companyDetails instanceof CompanyDetails && is_bool($companyDetails->getJse())) ? $companyDetails->getJse() : false,
                                                        'industry' => implode(', ',$job->getIndustry()),
                                                        'jobTitle'=>$job->getJobTitle(),
                                                        'city'=>$job->getAddressCity(),
                                                    ];
                                                }
                                            }
                                        }
                                    }
                                }
                                if(!empty($endingJobs)){
                                    if($notifyJobEnding->getNotifyEmail() == true){
                                        $emailData = [
                                            'user' => ['firstName'=>$notifyCandidatesJobEnding->getUser()->getFirstName()],
                                            'endingJobs'=>$endingJobs,
                                            'link'=>$request->getSchemeAndHttpHost().'/candidate/opportunities'
                                        ];
                                        $message = (new \Swift_Message('A Yes2Work job you qualify for is expiring soon'))
                                            ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                                            ->setTo($notifyCandidatesJobEnding->getUser()->getEmail())
                                            ->setBody(
                                                $this->renderView('emails/candidate/job_ending_soon.html.twig',
                                                    $emailData
                                                ),
                                                'text/html'
                                            );
                                        try{
                                            $this->get('mailer')->send($message);
                                        }catch(\Swift_TransportException $e){

                                        }
                                    }
                                    if($notifyJobEnding->getNotifySMS() == true){
                                        if(!empty($notifyJobEnding->getUser()->getPhone())){
                                            if(substr($notifyJobEnding->getUser()->getPhone(), 0, 1) == '+'){
                                                $number = substr($notifyJobEnding->getUser()->getPhone(), 1);
                                            }
                                            else{
                                                $number = $notifyJobEnding->getUser()->getPhone();
                                            }

                                            $message = "A Yes2Work Job is expiring\n\n";
                                            $message .= "Hi ".$notifyCandidatesJobEnding->getUser()->getFirstName()."!\n\n";
                                            foreach ($endingJobs as $endingJob){
                                                $message .= "The ".((isset($endingJob['jobTitle'])) ? $endingJob['jobTitle'] : "")." job at a ";
                                                $message .= ((isset($endingJob['jse']) && $endingJob['jse'] == true) ? "Listed" : "Unlisted")." ";
                                                $message .= ((isset($endingJob['industry'])) ? $endingJob['industry'] : "")." business situated in ";
                                                $message .= ((isset($endingJob['city'])) ? $endingJob['city'] : "")." is expiring in a few days’ time.\n\n";
                                            }
                                            $message .= "Don’t miss out on this opportunity! \n\n";
                                            $message .= "Please login to Yes2Work to find out more and either Apply or Decline this opportunity. \n\n";
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

                            }
                        }
                    }
                }
            }
        }

        $result = $this->generateNotify($em, 2);

        exit();
    }

    /**
     * @param Request $request
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     *
     * @Rest\Get("/send_weekly_notify")
     */
    public function sendWeeklyNotifyAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        /**
         * GENERATE REMINDER PROFILE NOTIFY
         */
        /*$notifyCandidates = $em->getRepository("AppBundle:NotificationCandidate")->findBy(['reminderProfileStatus'=>true,'reminderProfile'=>3]);
        if(!empty($notifyCandidates)){
            foreach ($notifyCandidates as $notifyCandidate){
                if($notifyCandidate instanceof NotificationCandidate){
                    if($notifyCandidate->getUser() instanceof User && $notifyCandidate->getUser()->isEnabled()){
                        if($notifyCandidate->getNotifyEmail() == true || $notifyCandidate->getNotifySMS() == true){
                            $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$notifyCandidate->getUser()]);
                            if($profileDetails instanceof ProfileDetails){
                                if($profileDetails->getPercentage() < 50 || empty($profileDetails->getVideo())){
                                    if($notifyCandidate->getNotifyEmail() == true){
                                        $message = (new \Swift_Message('Your Yes2Work Profile is not yet complete'))
                                            ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                                            ->setTo($notifyCandidate->getUser()->getEmail())
                                            ->setBody(
                                                $this->renderView('emails/candidate/reminder_profile.html.twig', [
                                                    'candidate' => [
                                                        'firstName'=>$notifyCandidate->getUser()->getFirstName()
                                                    ],
                                                    'link' => $request->getSchemeAndHttpHost().'/candidate/profile_details'
                                                ]),
                                                'text/html'
                                            );
                                        try{
                                            $this->get('mailer')->send($message);
                                        }catch(\Swift_TransportException $e){

                                        }
                                    }
                                    if($notifyCandidate->getNotifySMS() == true){
                                        if(!empty($notifyCandidate->getUser()->getPhone())){
                                            if(substr($notifyCandidate->getUser()->getPhone(), 0, 1) == '+'){
                                                $number = substr($notifyCandidate->getUser()->getPhone(), 1);
                                            }
                                            else{
                                                $number = $notifyCandidate->getUser()->getPhone();
                                            }
                                            $message = "Hi ".$notifyCandidate->getUser()->getFirstName()."!\n\n";
                                            $message .= "Please note that your profile on Yes2Work is incomplete and you are currently unable to apply for any jobs advertised on Yes2Work. Please ensure that you have uploaded your CV and other requested information and recorded a Video Interview. \n\n";
                                            $message .= "Please login to Yes2Work to update your profile.\n\n";
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
                            }
                        }
                    }
                }
            }
        }*/


        $result = $this->generateNotify($em, 3);

        exit();
    }

    /**
     * @param Request $request
     * @throws \ReflectionException
     *
     * @Rest\Get("/send_monthly_notify")
     */
    public function sendMonthlyNotifyAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        
        exit();
    }

    /**
     * @param Request $request
     * @param $delay
     * @throws
     *
     * @Rest\Get("/send_deactivate_profile_notify/{delay}")
     */
    public function sendDeactivateProfileNotifyAction(Request $request, $delay){
        $em = $this->getDoctrine()->getManager();
        if(in_array($delay,[2, 3])){
            if($delay == 2){
                $startDate = new \DateTime('-1 days');
                $endDate = new \DateTime();
            }
            else{
                $startDate = new \DateTime('-1 week');
                $endDate = new \DateTime();
            }
            $notificationAdmins = $em->getRepository("AppBundle:NotificationAdmin")->findBy(['notifyEmail'=>true,'candidateDeactivate'=>$delay]);
            if(!empty($notificationAdmins)){
                $deactivateCandidates = $em->getRepository("AppBundle:ProfileDetails")->findBy(['looking'=>false]);
                $candidates = [];
                if(!empty($deactivateCandidates)){
                    foreach ($deactivateCandidates as $candidateDetails){
                        if($candidateDetails instanceof ProfileDetails && $candidateDetails->getUser() instanceof User){
                            if($candidateDetails->getUser()->isEnabled()){
                                if(!empty($candidateDetails->getLastDeactivated())){
                                    $lastDeactivated = $candidateDetails->getLastDeactivated();
                                    if($lastDeactivated instanceof \DateTime
                                        && $lastDeactivated->format('Y-m-d H:i') >= $startDate->format('Y-m-d H:i')
                                        && $lastDeactivated->format('Y-m-d H:i') <= $endDate->format('Y-m-d H:i')
                                    ){
                                        $candidates[] = [
                                            'candidate' => [
                                                'firstName' => $candidateDetails->getUser()->getFirstName(),
                                                'lastName' => $candidateDetails->getUser()->getLastName(),
                                                'email' => $candidateDetails->getUser()->getEmail(),
                                                'phone' => $candidateDetails->getUser()->getPhone()
                                            ] ,
                                            'link' => $request->getSchemeAndHttpHost().'/admin/edit_candidate?candidateId='.$candidateDetails->getUser()->getId()
                                        ];
                                    }
                                }
                            }
                        }
                    }

                    if(!empty($candidates)){
                        foreach ($notificationAdmins as $admin){
                            if($admin instanceof NotificationAdmin && $admin->getUser() instanceof User){
                                $message = (new \Swift_Message('A candidates has just deactivated their profile'))
                                    ->setFrom($this->container->getParameter('mailer_user_name'), 'CAs_Online')
                                    ->setTo($admin->getUser()->getEmail())
                                    ->setBody(
                                        $this->renderView('emails/admin/total/candidate_deactivated.html.twig', [
                                            'candidates' => $candidates
                                        ]),
                                        'text/html'
                                    );

                                try{
                                    $this->get('mailer')->send($message);
                                }catch(\Swift_TransportException $e){

                                }
                            }
                        }
                    }

                }
            }
        }
        exit();
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Rest\Get("/update_reminder_notify")
     *
     */
    public function updateReminderNotifyAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $candidateNotifications = $em->getRepository("AppBundle:NotificationCandidate")->findBy(['reminderProfile'=>2]);
        if(!empty($candidateNotifications)){
            foreach ($candidateNotifications as $candidateNotification) {
                if($candidateNotification instanceof NotificationCandidate){
                    $candidateNotification->setReminderProfile(3);
                    $em->persist($candidateNotification);
                    $em->flush();
                }
            }
        }

        return $this->handleView($this->view([], Response::HTTP_OK));
    }

    /**
     * @Rest\Get("/test")
     */
    public function testAction(){
        $message = (new \Swift_Message('A Yes2Work job you loaded is expiring soon'))
            ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
            ->setTo('mishabbk@gmail.com')
            ->setBody(
                $this->renderView('emails/client/job_expiration.html.twig', [
                    'job' => [
                        'user'=>[
                            'firstName' => 'UserTest',
                        ],
                        'jobTitle' => 'JobTest'
                    ],
                    'link' => 'https://yes2work.co.za/business/jobs/edit/'
                ]),
                'text/html'
            );
        try{
            $this->get('mailer')->send($message);
        }catch(\Swift_TransportException $e){

        }

        die();
    }

    /**
     * @param EntityManager $em
     * @param $delay
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    private function generateNotify(EntityManager $em, $delay){
        if($delay == 2){
            $titleMessage = 'Daily Report';
        }
        elseif ($delay == 3){
            $titleMessage = 'Weekly Report';
        }
        else{
            return false;
        }
        $emailsSchedule = $em->getRepository("AppBundle:EmailSchedule")->findBy(['delay'=>$delay]);
        $sortEmailsSchedule = [];
        if(!empty($emailsSchedule)){
            foreach ($emailsSchedule as $emailSchedule){
                if($emailSchedule instanceof EmailSchedule){
                    if(!isset($sortEmailsSchedule[$emailSchedule->getUser()->getId()])){
                        $sortEmailsSchedule[$emailSchedule->getUser()->getId()]['user'] = $emailSchedule->getUser()->getId();
                    }
                    if(!isset($sortEmailsSchedule[$emailSchedule->getUser()->getId()]['types'])){
                        $sortEmailsSchedule[$emailSchedule->getUser()->getId()]['types'] = [];
                    }
                    $sortEmailsSchedule[$emailSchedule->getUser()->getId()]['types'][$emailSchedule->getType()][] = $emailSchedule->getEmailData();
                }
            }
        }

        if(!empty($sortEmailsSchedule)){
            foreach ($sortEmailsSchedule as $sortEmailSchedule){
                if(isset($sortEmailSchedule['user']) && isset($sortEmailSchedule['types']) && !empty($sortEmailSchedule['types'])){
                    $user = $sortEmailSchedule['user'];
                    if($user instanceof User){
                        if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SUPER_ADMIN')){
                            $notifyAdmin = $em->getRepository("AppBundle:NotificationAdmin")->findOneBy(['user'=>$user,'notifyEmail'=>true]);
                            if($notifyAdmin instanceof NotificationAdmin){
                                $message = (new \Swift_Message($titleMessage))
                                    ->setFrom($this->container->getParameter('mailer_user_name'), 'CAs_Online')
                                    ->setTo($user->getEmail())
                                    ->setBody(
                                        $this->renderView('emails/admin/total/report.html.twig', [
                                            'types' => $sortEmailSchedule['types']
                                        ]),
                                        'text/html'
                                    );

                                try{
                                    $this->get('mailer')->send($message);
                                }catch(\Swift_TransportException $e){

                                }
                            }
                        }
                        elseif ($user->hasRole('ROLE_CANDIDATE')){
                            if($delay != 3){
                                $notifyCandidate = $em->getRepository("AppBundle:NotificationCandidate")->findOneBy(['user'=>$user]);
                                $profileDetails = $em->getRepository("AppBundle:ProfileDetails")->findOneBy(['user'=>$user]);
                                if($notifyCandidate instanceof NotificationCandidate && $profileDetails instanceof ProfileDetails){
                                    if($notifyCandidate->getNotifyEmail() == true || $notifyCandidate->getNotifySMS() == true){
                                        foreach($sortEmailSchedule['types'] as $type=>$dataTypes){
                                            if(!empty($dataTypes)){
                                                $template = false;
                                                $templateDate = false;
                                                $titleMessageRole = 'CAs_Online';
                                                if($type == SendEmail::TYPE_CANDIDATE_INTERVIEW_REQUEST){
                                                    if($notifyCandidate->getInterviewRequestStatus() == true && $notifyCandidate->getInterviewRequest() == $delay && $profileDetails->getLooking() == true){
                                                        $template = 'emails/candidate/total/interview_request.html.twig';
                                                        $titleMessageRole = 'Great news – Yes2Work has got you an Interview Request!';
                                                        $templateDate['jobs'] = [];
                                                        foreach ($dataTypes as $dataType){
                                                            $temp = [];
                                                            if(isset($dataType['jse'])){
                                                                $temp['jse']=$dataType['jse'];
                                                            }
                                                            if(isset($dataType['industry'])){
                                                                $temp['industry']=$dataType['industry'];
                                                            }
                                                            if(isset($dataType['jobTitle'])){
                                                                $temp['jobTitle']=$dataType['jobTitle'];
                                                            }
                                                            if(isset($dataType['city'])){
                                                                $temp['city']=$dataType['city'];
                                                            }

                                                            if(!empty($temp)){
                                                                $templateDate['jobs'][] = $temp;
                                                            }
                                                        }
                                                    }
                                                }
                                                elseif ($type == SendEmail::TYPE_CANDIDATE_NEW_JOB_LOADED){
                                                    if($notifyCandidate->getNewJobLoadedStatus() == true && $notifyCandidate->getNewJobLoaded() == $delay && $profileDetails->getLooking() == true){
                                                        $template = 'emails/candidate/total/new_job_loaded.html.twig';
                                                        $titleMessageRole = 'A new Yes2Work jobs has just been opened – are you interested?';
                                                        $templateDate['jobs'] = [];
                                                        foreach ($dataTypes as $dataType){
                                                            $temp = [];
                                                            if(isset($dataType['jse'])){
                                                                $temp['jse']=$dataType['jse'];
                                                            }
                                                            if(isset($dataType['industry'])){
                                                                $temp['industry']=$dataType['industry'];
                                                            }
                                                            if(isset($dataType['jobTitle'])){
                                                                $temp['jobTitle']=$dataType['jobTitle'];
                                                            }
                                                            if(isset($dataType['city'])){
                                                                $temp['city']=$dataType['city'];
                                                            }
                                                            if(isset($dataType['closureDate']) && $dataType['closureDate'] instanceof \DateTime){
                                                                $temp['closureDate']=$dataType['closureDate'];
                                                            }
                                                            if(isset($dataType['link'])){
                                                                $templateDate['link']=$dataType['link'];
                                                            }

                                                            if(!empty($temp)){
                                                                $templateDate['jobs'][] = $temp;
                                                            }
                                                        }
                                                    }
                                                }
                                                elseif($type == SendEmail::TYPE_CANDIDATE_APPLICATION_DECLINE){
                                                    if($notifyCandidate->getApplicationDeclineStatus() == true && $notifyCandidate->getApplicationDecline() == $delay){
                                                        $template = 'emails/candidate/total/application_decline.html.twig';
                                                        $titleMessageRole = 'Your application on Yes2Work has been declined by the Employer';
                                                        $templateDate['jobs'] = [];
                                                        foreach ($dataTypes as $dataType){
                                                            $temp = [];
                                                            if(isset($dataType['jse'])){
                                                                $temp['jse']=$dataType['jse'];
                                                            }
                                                            if(isset($dataType['industry'])){
                                                                $temp['industry']=$dataType['industry'];
                                                            }
                                                            if(isset($dataType['jobTitle'])){
                                                                $temp['jobTitle']=$dataType['jobTitle'];
                                                            }
                                                            if(isset($dataType['city'])){
                                                                $temp['city']=$dataType['city'];
                                                            }
                                                            if(isset($dataType['link'])){
                                                                $templateDate['link']=$dataType['link'];
                                                            }

                                                            if(!empty($temp)){
                                                                $templateDate['jobs'][] = $temp;
                                                            }
                                                        }
                                                    }
                                                }
                                                if($template != false && $templateDate != false){
                                                    if($notifyCandidate->getNotifyEmail() == true){
                                                        $templateDate['firstName'] = $user->getFirstName();
                                                        $message = (new \Swift_Message($titleMessageRole))
                                                            ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                                                            ->setTo($user->getEmail())
                                                            ->setBody(
                                                                $this->renderView($template, $templateDate),
                                                                'text/html'
                                                            );

                                                        try{
                                                            $this->get('mailer')->send($message);
                                                        }catch(\Swift_TransportException $e){

                                                        }
                                                    }
                                                    if($notifyCandidate->getNotifySMS() == true){
                                                        if(!empty($user->getPhone())){
                                                            if(substr($user->getPhone(), 0, 1) == '+'){
                                                                $number = substr($user->getPhone(), 1);
                                                            }
                                                            else{
                                                                $number = $user->getPhone();
                                                            }
                                                            if($type == SendEmail::TYPE_CANDIDATE_INTERVIEW_REQUEST){
                                                                if(isset($templateDate['jobs']) && !empty($templateDate['jobs'])){
                                                                    $message = "Yes2Work Interview Request!\n\n";
                                                                    $message .= "Hi ".$templateDate['firstName']."!\n\n";
                                                                    foreach ($templateDate['jobs'] as $job){
                                                                        $message .= "A ".((isset($job['jse']) && $job['jse'] == true) ? "Listed" : "Unlisted")." ";
                                                                        $message .= ((isset($job['industry'])) ? $job['industry'] : "")." business situated in ";
                                                                        $message .= ((isset($job['city'])) ? $job['city'] : "")." is interested in interviewing you for a ";
                                                                        $message .= ((isset($job['jobTitle'])) ? $job['jobTitle'] : "")." role!\n\n";
                                                                    }
                                                                    $message .= "One of our consultants at Yes2Work will be in touch with you shortly to discuss this with you.\n\n";
                                                                    $message .= "Login to your Yes2Work profile to see more details.\n\n";
                                                                    $message .= "https://app.yes2work.co.za \n\n";
                                                                    $message .= "The Yes2Work Team\n";
                                                                    $message .= "support@yes2work.co.za";
                                                                }
                                                            }
                                                            elseif ($type == SendEmail::TYPE_CANDIDATE_NEW_JOB_LOADED){
                                                                if(isset($templateDate['jobs']) && !empty($templateDate['jobs'])){
                                                                    $message = "Yes2Work Job Alert!\n\n";
                                                                    $message .= "Hi ".$templateDate['firstName']."!\n\n";
                                                                    foreach ($templateDate['jobs'] as $job){
                                                                        $message .= "A ".((isset($job['jse']) && $job['jse'] == true) ? "Listed" : "Unlisted")." ";
                                                                        $message .= ((isset($job['industry'])) ? $job['industry'] : "")." business situated in ";
                                                                        $message .= ((isset($job['city'])) ? $job['city'] : "")." has loaded a new ";
                                                                        $message .= ((isset($job['jobTitle'])) ? $job['jobTitle'] : "")." job which you may be interested in.\n\n";
                                                                        $message .= "This job expires on: ".((isset($job['closureDate']) && $job['closureDate'] instanceof \DateTime) ? $job['closureDate']->format("Y-m-d") : "soon" ).".\n\n";
                                                                    }
                                                                    $message .= "Please Login to Yes2Work to find out more and either Apply or Decline this opportunity.\n\n";
                                                                    $message .= "https://app.yes2work.co.za \n\n";
                                                                    $message .= "The Yes2Work Team\n";
                                                                    $message .= "support@yes2work.co.za";
                                                                }
                                                            }
                                                            elseif($type == SendEmail::TYPE_CANDIDATE_APPLICATION_DECLINE){
                                                                $message = "Yes2Work Application declined\n\n";
                                                                $message .= "Hi ".$templateDate['firstName']."!\n\n";
                                                                foreach ($templateDate['jobs'] as $job) {
                                                                    $message .= "Unfortunately, your application for ";
                                                                    $message .= ((isset($job['jobTitle'])) ? $job['jobTitle'] : "") . " job at a ";
                                                                    $message .= ((isset($job['jse']) && $job['jse'] == true) ? "Listed" : "Unlisted") . " ";
                                                                    $message .= ((isset($job['industry'])) ? $job['industry'] : "") . " business situated in ";
                                                                    $message .= ((isset($job['city'])) ? $job['city'] : "") . " has not been successful.\n\n";
                                                                }
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
                                        }
                                    }
                                }
                            }
                        }
                        elseif ($user->hasRole('ROLE_CLIENT')){
                            $notifyClient = $em->getRepository("AppBundle:NotificationClient")->findOneBy(['user'=>$user,'notifyEmail'=>true]);
                            if($notifyClient instanceof NotificationClient){
                                foreach($sortEmailSchedule['types'] as $type=>$dataTypes){
                                    if(!empty($dataTypes)){
                                        $template = false;
                                        $templateDate = false;
                                        $titleMessageRole = 'CAs_Online';
                                        if ($type == SendEmail::TYPE_CLIENT_CANDIDATE_APPLICANT){
                                            if($notifyClient->getCandidateApplicantStatus() == true && $notifyClient->getCandidateApplicant() == $delay){
                                                $template = 'emails/client/total/candidate_application.html.twig';
                                                $titleMessageRole = 'Summary of applicants for your job loaded on Yes2Work';
                                                $templateDate['jobs'] = [];
                                                foreach ($dataTypes as $dataType){
                                                    if(isset($dataType['jobTitle'])){
                                                       if(!isset($templateDate['jobs'][$dataType['jobTitle']]) || !isset($templateDate['jobs'][$dataType['jobTitle']]['countApplicants'])){
                                                           $templateDate['jobs'][$dataType['jobTitle']]['countApplicants'] = 0;
                                                       }
                                                       $templateDate['jobs'][$dataType['jobTitle']]['countApplicants'] = $templateDate['jobs'][$dataType['jobTitle']]['countApplicants'] + 1;
                                                        if(isset($dataType['link'])){
                                                            $templateDate['jobs'][$dataType['jobTitle']]['link'] = $dataType['link'];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        if($template != false && $templateDate != false){
                                            $templateDate['firstName'] = $user->getFirstName();
                                            $message = (new \Swift_Message($titleMessageRole))
                                                ->setFrom($this->container->getParameter('mailer_user_name'), 'Yes2Work')
                                                ->setTo($user->getEmail())
                                                ->setBody(
                                                    $this->renderView($template, $templateDate),
                                                    'text/html'
                                                );

                                            try{
                                                $this->get('mailer')->send($message);
                                            }catch(\Swift_TransportException $e){

                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }

        if(!empty($emailsSchedule)){
            foreach ($emailsSchedule as $emailSchedule){
                if($emailSchedule instanceof EmailSchedule){
                    $em->remove($emailSchedule);
                }
            }
            $em->flush();
        }
        return true;
    }
}
