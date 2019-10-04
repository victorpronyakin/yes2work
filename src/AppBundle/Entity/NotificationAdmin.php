<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * NotificationAdmin
 *
 * @ORM\Table(name="notification_admin")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotificationAdminRepository")
 */
class NotificationAdmin
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
     * @ORM\Column(type="boolean", nullable=true, options={"default":true})
     */
    private $notifyEmail;

    /**
     * @Assert\Choice({1,2,3},strict=true,message="candidateSign should be 1..3",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":1})
     */
    private $candidateSign;

    /**
     * @Assert\Choice({1,2,3},strict=true,message="candidateFile should be 1..3",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":1})
     */
    private $candidateFile;

    /**
     * @Assert\Choice({1,2,3},strict=true,message="candidateRequestVideo should be 1..3",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":1})
     */
    private $candidateRequestVideo;

    /**
     * @Assert\Choice({1,2,3},strict=true,message="candidateDeactivate should be 1..3",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":1})
     */
    private $candidateDeactivate;

    /**
     * @Assert\Choice({1,2,3},strict=true,message="clientSign should be 1..3",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":1})
     */
    private $clientSign;

    /**
     * @Assert\Choice({1,2,3},strict=true,message="interviewSetUp should be 1..3",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":1})
     */
    private $interviewSetUp;

    /**
     * @Assert\Choice({1,2,3},strict=true,message="jobNew should be 1..3",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":1})
     */
    private $jobNew;

    /**
     * @Assert\Choice({1,2,3},strict=true,message="jobChange should be 1..3",groups={"updateNotify"})
     * @ORM\Column(type="integer", nullable=true, options={"default":1})
     */
    private $jobChange;

    /**
     * NotificationAdmin constructor.
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->notifyEmail = true;
        $this->candidateSign = 1;
        $this->candidateFile = 1;
        $this->candidateRequestVideo = 1;
        $this->candidateDeactivate = 1;
        $this->clientSign = 1;
        $this->interviewSetUp = 1;
        $this->jobNew = 1;
        $this->jobChange = 1;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getCandidateSign()
    {
        return $this->candidateSign;
    }

    /**
     * @param mixed $candidateSign
     */
    public function setCandidateSign($candidateSign)
    {
        $this->candidateSign = $candidateSign;
    }

    /**
     * @return mixed
     */
    public function getCandidateFile()
    {
        return $this->candidateFile;
    }

    /**
     * @param mixed $candidateFile
     */
    public function setCandidateFile($candidateFile)
    {
        $this->candidateFile = $candidateFile;
    }

    /**
     * @return mixed
     */
    public function getCandidateRequestVideo()
    {
        return $this->candidateRequestVideo;
    }

    /**
     * @param mixed $candidateRequestVideo
     */
    public function setCandidateRequestVideo($candidateRequestVideo)
    {
        $this->candidateRequestVideo = $candidateRequestVideo;
    }

    /**
     * @return mixed
     */
    public function getCandidateDeactivate()
    {
        return $this->candidateDeactivate;
    }

    /**
     * @param mixed $candidateDeactivate
     */
    public function setCandidateDeactivate($candidateDeactivate)
    {
        $this->candidateDeactivate = $candidateDeactivate;
    }

    /**
     * @return mixed
     */
    public function getClientSign()
    {
        return $this->clientSign;
    }

    /**
     * @param mixed $clientSign
     */
    public function setClientSign($clientSign)
    {
        $this->clientSign = $clientSign;
    }

    /**
     * @return mixed
     */
    public function getInterviewSetUp()
    {
        return $this->interviewSetUp;
    }

    /**
     * @param mixed $interviewSetUp
     */
    public function setInterviewSetUp($interviewSetUp)
    {
        $this->interviewSetUp = $interviewSetUp;
    }

    /**
     * @return mixed
     */
    public function getJobNew()
    {
        return $this->jobNew;
    }

    /**
     * @param mixed $jobNew
     */
    public function setJobNew($jobNew)
    {
        $this->jobNew = $jobNew;
    }

    /**
     * @return mixed
     */
    public function getJobChange()
    {
        return $this->jobChange;
    }

    /**
     * @param mixed $jobChange
     */
    public function setJobChange($jobChange)
    {
        $this->jobChange = $jobChange;
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
