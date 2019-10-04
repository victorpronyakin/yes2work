<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Applicants
 *
 * @ORM\Table(name="applicants")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ApplicantsRepository")
 */
class Applicants
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
     * 1 = Awaiting approve for Client
     * 2 = Shortlist for Client
     * 3 = Approved for Interview
     * 4 = Decline for Client
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * true=candidate
     * false = client
     * @ORM\Column(type="boolean", name="check1")
     */
    private $check;

    /**
     * Applicants constructor.
     * @param $client
     * @param $candidate
     * @param $status
     * @param $check
     * @param null $job
     */
    public function __construct($client, $candidate, $status, $check, $job=NULL)
    {
        $this->client = $client;
        $this->candidate = $candidate;
        $this->job = $job;
        $this->check = $check;
        $this->status = $status;
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

    /**
     * @return mixed
     */
    public function getCheck()
    {
        return $this->check;
    }

    /**
     * @param mixed $check
     */
    public function setCheck($check)
    {
        $this->check = $check;
    }

}
