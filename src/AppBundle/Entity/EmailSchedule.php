<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailShedule
 *
 * @ORM\Table(name="email_schedule")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmailScheduleRepository")
 */
class EmailSchedule
{
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
     * @ORM\Column(type="text")
     */
    private $emailData;

    /**
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * 2 = day
     * 3 = week
     * 4 = month
     * @ORM\Column(type="integer")
     */
    private $delay;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * EmailSchedule constructor.
     * @param $user
     * @param $emailData
     * @param $type
     * @param $delay
     */
    public function __construct($user, $emailData, $type, $delay)
    {
        $this->user = $user;
        $this->emailData = json_encode($emailData);
        $this->type = $type;
        $this->delay = $delay;
        $this->created = new \DateTime();
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
    public function getEmailData()
    {
        return json_decode($this->emailData, true);
    }

    /**
     * @param mixed $emailData
     */
    public function setEmailData($emailData)
    {
        $this->emailData = json_encode($emailData);
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }


    /**
     * @return mixed
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param mixed $delay
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }
}
