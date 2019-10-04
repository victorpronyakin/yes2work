<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * NotificationCandidate
 *
 * @ORM\Table(name="notification_candidate")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotificationCandidateRepository")
 */
class NotificationCandidate
{
    /**
     * When Notify Type
     * 1 = immediate
     * 2 = daily
     * 3 = weekly
     * 4 = monthly
     */

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @Assert\Choice({false,true},strict=true,message="notifyEmail should be boolean type",groups={"updateNotify"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $notifyEmail;

    /**
     * @Assert\Choice({false,true},strict=true,message="notifySMS should be boolean type",groups={"updateNotify"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $notifySMS;

    /**
     * @Assert\Choice({false,true},strict=true,message="Interview Requests should be ON or OFF",groups={"updateNotify"})
     * @ORM\Column(type="boolean", nullable=true, options={"default":true})
     */
    private $interviewRequestStatus;

    /**
     * @Assert\Choice({1,2,3},strict=true,message="Interview Requests should be Immediate or Daily or Weekly",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":1})
     */
    private $interviewRequest;

    /**
     * @Assert\Choice({false,true},strict=true,message="Application Decline should be ON or OFF",groups={"updateNotify"})
     * @ORM\Column(type="boolean", nullable=true, options={"default":true})
     */
    private $applicationDeclineStatus;

    /**
     * @Assert\Choice({1,2,3},strict=true,message="Application Decline should be Immediate or Daily or Weekly",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":1})
     */
    private $applicationDecline;

    /**
     * @Assert\Choice({false,true},strict=true,message="New Jobs Loaded should be ON or OFF",groups={"updateNotify"})
     * @ORM\Column(type="boolean", nullable=true, options={"default":true})
     */
    private $newJobLoadedStatus;

    /**
     * @Assert\Choice({1,2,3},strict=true,message="New Jobs Loaded should be Immediate or Daily or Weekly",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":1})
     */
    private $newJobLoaded;

    /**
     * @Assert\Choice({false,true},strict=true,message="Job Posts Ending Soon should be ON or OFF",groups={"updateNotify"})
     * @ORM\Column(type="boolean", nullable=true, options={"default":true})
     */
    private $jobEndingSoonStatus;

    /**
     * @Assert\Choice({2,3},strict=true,message="Job Posts Ending Soon be Daily or Weekly",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":2})
     */
    private $jobEndingSoon;


    /**
     * @Assert\Choice({false,true},strict=true,message="Documents Approved should be ON or OFF",groups={"updateNotify"})
     * @ORM\Column(type="boolean", nullable=true, options={"default":true})
     */
    private $documentApproveStatus;

    /**
     * @Assert\Choice({false,true},strict=true,message="Reminder to Complete Profile should be ON or OFF",groups={"updateNotify"})
     * @ORM\Column(type="boolean", nullable=true, options={"default":true})
     */
    private $reminderProfileStatus;

    /**
     * @Assert\Choice({2,3,4},strict=true,message="Reminder to Complete Profile be Weekly or Monthly",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":3})
     */
    private $reminderProfile;

    /**
     * NotificationCandidate constructor.
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->notifyEmail = true;
        $this->notifySMS = true;
        $this->interviewRequestStatus = true;
        $this->interviewRequest = 1;
        $this->applicationDeclineStatus = true;
        $this->applicationDecline = 1;
        $this->newJobLoadedStatus = true;
        $this->newJobLoaded = 1;
        $this->jobEndingSoonStatus = true;
        $this->jobEndingSoon = 2;
        $this->documentApproveStatus = true;
        $this->reminderProfileStatus = true;
        $this->reminderProfile = 3;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getNotifyEmail()
    {
        return $this->notifyEmail;
    }

    /**
     * @param mixed $notifyEmail
     */
    public function setNotifyEmail($notifyEmail)
    {
        $this->notifyEmail = $notifyEmail;
    }

    /**
     * @return mixed
     */
    public function getNotifySMS()
    {
        return $this->notifySMS;
    }

    /**
     * @param mixed $notifySMS
     */
    public function setNotifySMS($notifySMS)
    {
        $this->notifySMS = $notifySMS;
    }

    /**
     * @return mixed
     */
    public function getInterviewRequestStatus()
    {
        return $this->interviewRequestStatus;
    }

    /**
     * @param mixed $interviewRequestStatus
     */
    public function setInterviewRequestStatus($interviewRequestStatus)
    {
        $this->interviewRequestStatus = $interviewRequestStatus;
    }

    /**
     * @return mixed
     */
    public function getInterviewRequest()
    {
        return $this->interviewRequest;
    }

    /**
     * @param mixed $interviewRequest
     */
    public function setInterviewRequest($interviewRequest)
    {
        $this->interviewRequest = $interviewRequest;
    }

    /**
     * @return mixed
     */
    public function getApplicationDeclineStatus()
    {
        return $this->applicationDeclineStatus;
    }

    /**
     * @param mixed $applicationDeclineStatus
     */
    public function setApplicationDeclineStatus($applicationDeclineStatus)
    {
        $this->applicationDeclineStatus = $applicationDeclineStatus;
    }

    /**
     * @return mixed
     */
    public function getApplicationDecline()
    {
        return $this->applicationDecline;
    }

    /**
     * @param mixed $applicationDecline
     */
    public function setApplicationDecline($applicationDecline)
    {
        $this->applicationDecline = $applicationDecline;
    }

    /**
     * @return mixed
     */
    public function getNewJobLoadedStatus()
    {
        return $this->newJobLoadedStatus;
    }

    /**
     * @param mixed $newJobLoadedStatus
     */
    public function setNewJobLoadedStatus($newJobLoadedStatus)
    {
        $this->newJobLoadedStatus = $newJobLoadedStatus;
    }

    /**
     * @return mixed
     */
    public function getNewJobLoaded()
    {
        return $this->newJobLoaded;
    }

    /**
     * @param mixed $newJobLoaded
     */
    public function setNewJobLoaded($newJobLoaded)
    {
        $this->newJobLoaded = $newJobLoaded;
    }

    /**
     * @return mixed
     */
    public function getJobEndingSoonStatus()
    {
        return $this->jobEndingSoonStatus;
    }

    /**
     * @param mixed $jobEndingSoonStatus
     */
    public function setJobEndingSoonStatus($jobEndingSoonStatus)
    {
        $this->jobEndingSoonStatus = $jobEndingSoonStatus;
    }

    /**
     * @return mixed
     */
    public function getJobEndingSoon()
    {
        return $this->jobEndingSoon;
    }

    /**
     * @param mixed $jobEndingSoon
     */
    public function setJobEndingSoon($jobEndingSoon)
    {
        $this->jobEndingSoon = $jobEndingSoon;
    }


    /**
     * @return mixed
     */
    public function getDocumentApproveStatus()
    {
        return $this->documentApproveStatus;
    }

    /**
     * @param mixed $documentApproveStatus
     */
    public function setDocumentApproveStatus($documentApproveStatus)
    {
        $this->documentApproveStatus = $documentApproveStatus;
    }

    /**
     * @return mixed
     */
    public function getReminderProfileStatus()
    {
        return $this->reminderProfileStatus;
    }

    /**
     * @param mixed $reminderProfileStatus
     */
    public function setReminderProfileStatus($reminderProfileStatus)
    {
        $this->reminderProfileStatus = $reminderProfileStatus;
    }

    /**
     * @return mixed
     */
    public function getReminderProfile()
    {
        return $this->reminderProfile;
    }

    /**
     * @param mixed $reminderProfile
     */
    public function setReminderProfile($reminderProfile)
    {
        $this->reminderProfile = $reminderProfile;
    }

    /**
     * @param array $parameters
     */
    public function update($parameters = array()) {
        foreach($parameters as $key => $value) {
            if(property_exists($this,$key) && $key!='user'){
                $this->$key = $value;
            }
        }
    }
}
