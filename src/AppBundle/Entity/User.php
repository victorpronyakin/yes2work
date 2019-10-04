<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * User
 *
 * @ORM\Table("users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"emailCanonical"},
 *     errorPath="emailCanonical",
 *     message="Email address already used",
 *     groups={"registerClient","registerCandidate","updateClient","updateCandidate","updateAdmin"}
 * )
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank(
     *     message="firstName should not be blank",
     *     groups={"registerClient","registerCandidate","updateClient","updateCandidate","updateAdmin"}
     * )
     * @ORM\Column(type="string")
     */
    protected $firstName;

    /**
     * @Assert\NotBlank(
     *     message="lastName should not be blank",
     *     groups={"registerClient","registerCandidate","updateClient","updateCandidate","updateAdmin"}
     * )
     * @ORM\Column(type="string")
     */
    protected $lastName;

    /**
     *  @Assert\NotBlank(
     *     message="Email should not be blank",
     *     groups={"registerClient","registerCandidate","updateClient","updateCandidate","updateAdmin"}
     * )
     * @Assert\Email(
     *     message = "Email is not valid",
     *     groups={"registerClient","registerCandidate","updateClient","updateCandidate","updateAdmin"}
     * )
     */
    protected $emailCanonical;

    /**
     * @Assert\NotBlank(
     *     message="phone should not be blank",
     *     groups={"registerClient","registerCandidate","updateClient","updateCandidate"}
     * )
     * @ORM\Column(type="string", nullable=true)
     */
    protected $phone;

    /**
     *  @Assert\NotBlank(
     *     message="password should not be blank",
     *     groups={"registerClient","registerCandidate"}
     * )
     */
    protected $plainPassword;


    /**
     *  @Assert\NotBlank(
     *     message="jobTitle should not be blank",
     *     groups={"registerClient","updateClient"}
     * )
     * @ORM\Column(type="string", nullable=true)
     */
    protected $jobTitle;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $approved;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $agentName;


    public function __construct()
    {
        parent::__construct();
        $this->created = new \DateTime();
    }
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    /**
     * @param mixed $job_title
     */
    public function setJobTitle($job_title)
    {
        $this->jobTitle = $job_title;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
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
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * @param mixed $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }

    /**
     * @return mixed
     */
    public function getAgentName()
    {
        return $this->agentName;
    }

    /**
     * @param mixed $agentName
     */
    public function setAgentName($agentName)
    {
        $this->agentName = $agentName;
    }

    /**
     * @param $role
     * @param $firstName
     * @param $lastName
     * @param $email
     * @param $phone
     * @param $password
     * @param null $jobTitle
     */
    public function setRegisterDetails($role, $firstName, $lastName, $email, $phone, $password, $jobTitle=NULL){
        $this->addRole($role);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setEmail($email);
        $this->setUsername($email);
        $this->setPhone($phone);
        $this->setPlainPassword($password);
        $this->setJobTitle($jobTitle);
    }
}