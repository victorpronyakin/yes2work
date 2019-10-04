<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Interviews
 *
 * @ORM\Table(name="interviews")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InterviewsRepository")
 */
class Interviews
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
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $candidate;

    /**
     * @ORM\ManyToOne(targetEntity="Job")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $job;

    /**
     * 1 = Set up
     * 2 = Pending
     * 3 = Placed
     * 4 = Decline
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * true = candidate
     * false = client
     * @ORM\Column(type="boolean")
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * Interviews constructor.
     * @param $client
     * @param $candidate
     * @param $status
     * @param $type
     * @param null $job
     */
    public function __construct($client, $candidate, $status, $type, $job=NULL)
    {
        $this->client = $client;
        $this->candidate = $candidate;
        $this->status = $status;
        $this->type = $type;
        $this->job = $job;
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
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getCandidate()
    {
        return $this->candidate;
    }

    /**
     * @param mixed $candidate
     */
    public function setCandidate($candidate)
    {
        $this->candidate = $candidate;
    }

    /**
     * @return mixed
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param mixed $job
     */
    public function setJob($job)
    {
        $this->job = $job;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
