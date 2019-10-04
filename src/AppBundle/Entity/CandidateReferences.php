<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CandidateReferences
 *
 * @ORM\Table(name="candidate_references")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CandidateReferencesRepository")
 */
class CandidateReferences
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
     * @Assert\NotBlank(
     *     message="Name of company is required",
     *     groups={"validateReferences"}
     * )
     * @ORM\Column(type="string")
     */
    private $company;

    /**
     * @Assert\NotBlank(
     *     message="Role is required",
     *     groups={"validateReferences"}
     * )
     * @ORM\Column(type="string")
     */
    private $role;

    /**
     * @Assert\NotBlank(
     *     message="Field is required",
     *     groups={"validateReferences"}
     * )
     *
     * @ORM\Column(type="string")
     */
    private $specialization;

    /**
     * @Assert\NotBlank(
     *     message="Start Date is required",
     *     groups={"validateReferences"}
     * )
     * @Assert\Date(
     *     message="Start Date is invalid",
     *     groups={"validateReferences"}
     * )
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @Assert\NotBlank(
     *     message="End Date is required",
     *     groups={"validateReferences"}
     * )
     * @Assert\Date(
     *     message="End Date is invalid",
     *     groups={"validateReferences"}
     * )
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @Assert\Type(
     *     type="boolean",
     *     message="permission should be boolean type",
     *     groups={"validateReferences"}
     * )
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isReference;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $managerFirstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $managerLastName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $managerTitle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $managerEmail;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $managerComment;

    /**
     * @Assert\Type(
     *     type="boolean",
     *     message="permission should be boolean type",
     *     groups={"validateReferences"}
     * )
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $permission;

    /**
     * CandidateReferences constructor.
     * @param $user
     * @param array $parameters
     */
    public function __construct($user, $parameters = array())
    {
        $this->user = $user;
        foreach($parameters as $key => $value) {
            if(property_exists($this,$key) && $key!='user' && $key!='id'){
                if($key == 'startDate' || $key == 'endDate'){
                    $now = new \DateTime();
                    $newDate =(!empty($value) && $value != "null") ? ($value instanceof \DateTime ) ? $value : new \DateTime($value) : NULL;
                    if($newDate instanceof \DateTime){
                        $date_arr = explode(', ',$value);
                        if(isset($date_arr[1])){
                            $newDate->setDate($date_arr[1],$newDate->format('m'),$newDate->format('d'));
                        }
                        $newDate->setTimezone($now->getTimezone());
                    }
                    $this->$key = $newDate;
                }
                elseif(is_bool($value)){
                    $this->$key = $value;
                }
                else{
                    if(!empty($value) && $value != "null"){
                        $this->$key = $value;
                    }
                    else{
                        $this->$key = NULL;
                    }
                }
            }
        }
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
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getSpecialization()
    {
        return $this->specialization;
    }

    /**
     * @param mixed $specialization
     */
    public function setSpecialization($specialization)
    {
        $this->specialization = $specialization;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getIsReference()
    {
        return $this->isReference;
    }

    /**
     * @param mixed $isReference
     */
    public function setIsReference($isReference)
    {
        $this->isReference = $isReference;
    }

    /**
     * @return mixed
     */
    public function getManagerFirstName()
    {
        return $this->managerFirstName;
    }

    /**
     * @param mixed $managerFirstName
     */
    public function setManagerFirstName($managerFirstName)
    {
        $this->managerFirstName = $managerFirstName;
    }

    /**
     * @return mixed
     */
    public function getManagerLastName()
    {
        return $this->managerLastName;
    }

    /**
     * @param mixed $managerLastName
     */
    public function setManagerLastName($managerLastName)
    {
        $this->managerLastName = $managerLastName;
    }

    /**
     * @return mixed
     */
    public function getManagerTitle()
    {
        return $this->managerTitle;
    }

    /**
     * @param mixed $managerTitle
     */
    public function setManagerTitle($managerTitle)
    {
        $this->managerTitle = $managerTitle;
    }

    /**
     * @return mixed
     */
    public function getManagerEmail()
    {
        return $this->managerEmail;
    }

    /**
     * @param mixed $managerEmail
     */
    public function setManagerEmail($managerEmail)
    {
        $this->managerEmail = $managerEmail;
    }

    /**
     * @return mixed
     */
    public function getManagerComment()
    {
        return $this->managerComment;
    }

    /**
     * @param mixed $managerComment
     */
    public function setManagerComment($managerComment)
    {
        $this->managerComment = $managerComment;
    }

    /**
     * @return mixed
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @param mixed $permission
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;
    }

    /**
     * @param array $parameters
     */
    public function update($parameters = array()){
        foreach($parameters as $key => $value) {
            if(property_exists($this,$key) && $key!='user' && $key!='id'){
                if($key == 'startDate' || $key == 'endDate'){
                    $now = new \DateTime();
                    $newDate =(!empty($value) && $value != "null") ? ($value instanceof \DateTime ) ? $value : new \DateTime($value) : NULL;
                    if($newDate instanceof \DateTime){
                        $date_arr = explode(', ',$value);
                        if(isset($date_arr[1])){
                            $newDate->setDate($date_arr[1],$newDate->format('m'),$newDate->format('d'));
                        }
                        $newDate->setTimezone($now->getTimezone());
                    }
                    $this->$key = $newDate;
                }
                elseif(is_bool($value)){
                    $this->$key = $value;
                }
                else{
                    if(!empty($value) && $value != "null"){
                        $this->$key = $value;
                    }
                    else{
                        $this->$key = NULL;
                    }
                }
            }
        }
    }
}
