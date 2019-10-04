<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * NotificationClient
 *
 * @ORM\Table(name="notification_client")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotificationClientRepository")
 */
class NotificationClient
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
     * @Assert\Choice({false,true},strict=true,message="New Candidates should be ON or OFF",groups={"updateNotify"})
     * @ORM\Column(type="boolean", nullable=true, options={"default":true})
     */
    private $newCandidateStatus;

    /**
     * @Assert\Choice({2,3},strict=true,message="New Candidates should Daily or Weekly",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":2})
     */
    private $newCandidate;

    /**
     * @Assert\Choice({false,true},strict=true,message="Job Approved should be ON or OFF",groups={"updateNotify"})
     * @ORM\Column(type="boolean", nullable=true, options={"default":true})
     */
    private $jobApproveStatus;

    /**
     * @Assert\Choice({1,2,3},strict=true,message="Job Approved should be Immediate or Daily or Weekly",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":1})
     */
    private $jobApprove;

    /**
     * @Assert\Choice({false,true},strict=true,message="Job Declined should be ON or OFF",groups={"updateNotify"})
     * @ORM\Column(type="boolean", nullable=true, options={"default":true})
     */
    private $jobDeclineStatus;

    /**
     * @Assert\Choice({1,2,3},strict=true,message="Job Declined should be Immediate or Daily or Weekly",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":1})
     */
    private $jobDecline;

    /**
     * @Assert\Choice({false,true},strict=true,message="Candidate Applications should be ON or OFF",groups={"updateNotify"})
     * @ORM\Column(type="boolean", nullable=true, options={"default":true})
     */
    private $candidateApplicantStatus;

    /**
     * @Assert\Choice({1,2,3},strict=true,message="Candidate Applications should be Immediate or Daily or Weekly",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":1})
     */
    private $candidateApplicant;

    /**
     * @Assert\Choice({false,true},strict=true,message="Candidate Declines should be ON or OFF",groups={"updateNotify"})
     * @ORM\Column(type="boolean", nullable=true, options={"default":true})
     */
    private $candidateDeclineStatus;

    /**
     * @Assert\Choice({1,2,3},strict=true,message="Candidate Declines should be Immediate or Daily or Weekly",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":1})
     */
    private $candidateDecline;


    /**
     * NotificationClient constructor.
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->notifyEmail = true;
        $this->newCandidateStatus = true;
        $this->newCandidate = 2;
        $this->jobApproveStatus = true;
        $this->jobApprove = 1;
        $this->jobDeclineStatus = true;
        $this->jobDecline = 1;
        $this->candidateApplicantStatus = true;
        $this->candidateApplicant = 1;
        $this->candidateDeclineStatus = true;
        $this->candidateDecline = 1;
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
    public function getNewCandidateStatus()
    {
        return $this->newCandidateStatus;
    }

    /**
     * @param mixed $newCandidateStatus
     */
    public function setNewCandidateStatus($newCandidateStatus)
    {
        $this->newCandidateStatus = $newCandidateStatus;
    }

    /**
     * @return mixed
     */
    public function getNewCandidate()
    {
        return $this->newCandidate;
    }

    /**
     * @param mixed $newCandidate
     */
    public function setNewCandidate($newCandidate)
    {
        $this->newCandidate = $newCandidate;
    }

    /**
     * @return mixed
     */
    public function getJobApproveStatus()
    {
        return $this->jobApproveStatus;
    }

    /**
     * @param mixed $jobApproveStatus
     */
    public function setJobApproveStatus($jobApproveStatus)
    {
        $this->jobApproveStatus = $jobApproveStatus;
    }

    /**
     * @return mixed
     */
    public function getJobApprove()
    {
        return $this->jobApprove;
    }

    /**
     * @param mixed $jobApprove
     */
    public function setJobApprove($jobApprove)
    {
        $this->jobApprove = $jobApprove;
    }

    /**
     * @return mixed
     */
    public function getJobDeclineStatus()
    {
        return $this->jobDeclineStatus;
    }

    /**
     * @param mixed $jobDeclineStatus
     */
    public function setJobDeclineStatus($jobDeclineStatus)
    {
        $this->jobDeclineStatus = $jobDeclineStatus;
    }

    /**
     * @return mixed
     */
    public function getJobDecline()
    {
        return $this->jobDecline;
    }

    /**
     * @param mixed $jobDecline
     */
    public function setJobDecline($jobDecline)
    {
        $this->jobDecline = $jobDecline;
    }

    /**
     * @return mixed
     */
    public function getCandidateApplicantStatus()
    {
        return $this->candidateApplicantStatus;
    }

    /**
     * @param mixed $candidateApplicantStatus
     */
    public function setCandidateApplicantStatus($candidateApplicantStatus)
    {
        $this->candidateApplicantStatus = $candidateApplicantStatus;
    }

    /**
     * @return mixed
     */
    public function getCandidateApplicant()
    {
        return $this->candidateApplicant;
    }

    /**
     * @param mixed $candidateApplicant
     */
    public function setCandidateApplicant($candidateApplicant)
    {
        $this->candidateApplicant = $candidateApplicant;
    }


    /**
     * @return mixed
     */
    public function getCandidateDeclineStatus()
    {
        return $this->candidateDeclineStatus;
    }

    /**
     * @param mixed $candidateDeclineStatus
     */
    public function setCandidateDeclineStatus($candidateDeclineStatus)
    {
        $this->candidateDeclineStatus = $candidateDeclineStatus;
    }

    /**
     * @return mixed
     */
    public function getCandidateDecline()
    {
        return $this->candidateDecline;
    }

    /**
     * @param mixed $candidateDecline
     */
    public function setCandidateDecline($candidateDecline)
    {
        $this->candidateDecline = $candidateDecline;
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
