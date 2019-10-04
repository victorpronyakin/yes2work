<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CandidateAchievements
 *
 * @ORM\Table(name="candidate_qualifications")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CandidateQualificationsRepository")
 */
class CandidateQualifications
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
     *     message="type should not be blank",
     *     groups={"validateMatric", "validateTertiary", "validateGr10"}
     * )
     * @Assert\Choice({1,2,3},
     *     strict=true,
     *     message="type should be Matric or Gr10 or Tertiary",
     *     groups={"validateMatric", "validateTertiary", "validateGr10"}
     * )
     * 1 = Matric
     * 2 = GR10
     * 3 = Tertiary
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @Assert\NotBlank(
     *     message="School Name should not be blank",
     *     groups={"validateMatric"}
     * )
     * @ORM\Column(type="string", nullable=true)
     */
    private $schoolName;

    /**
     * @Assert\NotBlank(
     *     message="Year Matriculated should not be blank",
     *     groups={"validateMatric"}
     * )
     * @ORM\Column(type="string", nullable=true)
     */
    private $matriculatedYear;

    /**
     * @Assert\NotBlank(
     *     message="Complete Subject should not be blank",
     *     groups={"validateMatric"}
     * )
     *
     * @ORM\Column(type="array", nullable=true)
     */
    private $completeSubject;

    /**
     * @Assert\NotBlank(
     *     message="Tertiary Institution Name is required",
     *     groups={"validateTertiary"}
     * )
     * @ORM\Column(type="string", nullable=true)
     */
    private $tertiaryInstitution;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $tertiaryInstitutionCustom;

    /**
     * @Assert\NotBlank(
     *     message="Qualification Level should not be blank",
     *     groups={"validateTertiary"}
     * )
     * @ORM\Column(type="string", nullable=true)
     */
    private $levelQ;

    /**
     * @Assert\NotBlank(
     *     message="Specific Qualification should not be blank",
     *     groups={"validateTertiary"}
     * )
     * @ORM\Column(type="string", nullable=true)
     */
    private $specificQ;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $specificQCustom;

    /**
     * @Assert\NotBlank(
     *     message="Specialization should not be blank",
     *     groups={"validateTertiary"}
     * )
     * @ORM\Column(type="string", nullable=true)
     */
    private $specialization;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $specializationCustom;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $education;

    /**
     * @Assert\NotBlank(
     *     message="Start Year should not be blank",
     *     groups={"validateTertiary"}
     * )
     * @Assert\Date(message="startYear should be date format",groups={"validateTertiary"})
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startYear;

    /**
     * @Assert\NotBlank(
     *     message="End Year should not be blank",
     *     groups={"validateTertiary"}
     * )
     * @Assert\Date(message="endYear should be date format",groups={"validateTertiary"})
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endYear;

    /**
     * CandidateAchievements constructor.
     * @param $user
     * @param $type
     */
    public function __construct($user, $type)
    {
        $this->user = $user;
        $this->type = $type;
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
    public function getSchoolName()
    {
        return $this->schoolName;
    }

    /**
     * @param mixed $schoolName
     */
    public function setSchoolName($schoolName)
    {
        $this->schoolName = $schoolName;
    }

    /**
     * @return mixed
     */
    public function getMatriculatedYear()
    {
        return $this->matriculatedYear;
    }

    /**
     * @param mixed $matriculatedYear
     */
    public function setMatriculatedYear($matriculatedYear)
    {
        $this->matriculatedYear = $matriculatedYear;
    }

    /**
     * @return mixed
     */
    public function getCompleteSubject()
    {
        return $this->completeSubject;
    }

    /**
     * @param mixed $completeSubject
     */
    public function setCompleteSubject($completeSubject)
    {
        $this->completeSubject = $completeSubject;
    }

    /**
     * @return mixed
     */
    public function getTertiaryInstitution()
    {
        return $this->tertiaryInstitution;
    }

    /**
     * @param mixed $tertiaryInstitution
     */
    public function setTertiaryInstitution($tertiaryInstitution)
    {
        $this->tertiaryInstitution = $tertiaryInstitution;
    }

    /**
     * @return mixed
     */
    public function getTertiaryInstitutionCustom()
    {
        return $this->tertiaryInstitutionCustom;
    }

    /**
     * @param mixed $tertiaryInstitutionCustom
     */
    public function setTertiaryInstitutionCustom($tertiaryInstitutionCustom)
    {
        $this->tertiaryInstitutionCustom = $tertiaryInstitutionCustom;
    }

    /**
     * @return mixed
     */
    public function getLevelQ()
    {
        return $this->levelQ;
    }

    /**
     * @param mixed $levelQ
     */
    public function setLevelQ($levelQ)
    {
        $this->levelQ = $levelQ;
    }

    /**
     * @return mixed
     */
    public function getSpecificQ()
    {
        return $this->specificQ;
    }

    /**
     * @param mixed $specificQ
     */
    public function setSpecificQ($specificQ)
    {
        $this->specificQ = $specificQ;
    }

    /**
     * @return mixed
     */
    public function getSpecificQCustom()
    {
        return $this->specificQCustom;
    }

    /**
     * @param mixed $specificQCustom
     */
    public function setSpecificQCustom($specificQCustom)
    {
        $this->specificQCustom = $specificQCustom;
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
    public function getSpecializationCustom()
    {
        return $this->specializationCustom;
    }

    /**
     * @param mixed $specializationCustom
     */
    public function setSpecializationCustom($specializationCustom)
    {
        $this->specializationCustom = $specializationCustom;
    }

    /**
     * @return mixed
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * @param mixed $education
     */
    public function setEducation($education)
    {
        $this->education = $education;
    }

    /**
     * @return mixed
     */
    public function getStartYear()
    {
        return $this->startYear;
    }

    /**
     * @param mixed $startYear
     */
    public function setStartYear($startYear)
    {
        $this->startYear = $startYear;
    }

    /**
     * @return mixed
     */
    public function getEndYear()
    {
        return $this->endYear;
    }

    /**
     * @param mixed $endYear
     */
    public function setEndYear($endYear)
    {
        $this->endYear = $endYear;
    }

    /**
     * @param array $parameters
     */
    public function update($parameters = array()){
        foreach($parameters as $key => $value) {
            if(property_exists($this,$key) && $key!='user' && $key!='id'){
                if($key == 'startYear' || $key == 'endYear'){
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
