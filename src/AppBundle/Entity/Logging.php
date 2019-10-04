<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Logging
 *
 * @ORM\Table(name="logging")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LoggingRepository")
 */
class Logging
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
     * 1 = Create Candidate
     * 2 = Edit Candidate
     * 3 = Activate Candidate
     * 4 = Deactivate Candidate
     * 5 = Remove Candidate
     * 6 = Approve File
     * 7 = Decline File
     * 8 = Approve Candidate
     * 9 = Decline Candidate
     * 10 = Create Business
     * 11 = Edit Business
     * 12 = Activate Business
     * 13 = Deactivate Business
     * 14 = Remove Business
     * 15 = Approve Business
     * 16 = Decline Business
     * 17 = Create Job
     * 18 = Edit Job
     * 19 = Open Job
     * 20 = Close Job
     * 21 = Remove Job
     * 22 = Approve Job
     * 23 = Decline Job
     * 24 = Create Admin
     * 25 = Edit Admin
     * 26 = Remove Admin
     * 27 = Approve Video
     * 28 = Decline Video
     * 29 = Upload Video
     * 30 = Remove Video
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $itemID;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * Logging constructor.
     * @param $user
     * @param $type
     * @param $title
     * @param $itemID
     */
    public function __construct($user, $type, $title, $itemID=null)
    {
        $this->user = $user;
        $this->type = $type;
        $this->title = $title;
        $this->itemID = $itemID;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getItemID()
    {
        return $this->itemID;
    }

    /**
     * @param mixed $itemID
     */
    public function setItemID($itemID)
    {
        $this->itemID = $itemID;
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
