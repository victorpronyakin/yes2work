<?php
/**
 * Created by PhpStorm.
 * Date: 28.02.18
 * Time: 16:18
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProfileDetailsRepository")
 * @ORM\Table(name="profile_details")
 */
class ProfileDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @Assert\NotBlank(message="SA ID Number should not be blank",groups={"updateDetails"})
     * @ORM\Column(type="string")
     */
    private $idNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nationality;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $ethnicity;

    /**
     * @Assert\Date(message="BEE Check should be date format",groups={"updateDetails"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $beeCheck;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $mostRole;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $mostEmployer;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $specialization;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $gender;

    /**
     * @Assert\Date(message="dateOfBirth should be date format",groups={"updateDetails"})
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateOfBirth;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mostSalary;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $salaryPeriod;

    /**
     * @Assert\Choice({false,true},strict=true,message="criminal should be boolean type",groups={"updateDetails"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $criminal;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $criminalDescription;

    /**
     * @Assert\Choice({false,true},strict=true,message="credit should be boolean type",groups={"updateDetails"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $credit;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $creditDescription;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $homeAddress;

    /**
     * @Assert\Choice({false,true},strict=true,message="Driver License should be boolean type",groups={"updateDetails"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $driverLicense;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $driverNumber;

    /**
     * 1 = Below Average
     * 2 = Average
     * 3 = Good
     * 4 = Exceptional
     * @ORM\Column(type="integer", nullable=true)
     */
    private $englishProficiency;

    /**
     * @Assert\Choice({false,true},strict=true,message="employed should be boolean type",groups={"updateDetails"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $employed;

    /**
     * @Assert\Date(message="When can you start should be date format",groups={"updateDetails"})
     * @ORM\Column(type="date", nullable=true)
     */
    private $employedDate;

    /**
     * @Assert\Choice({false,true},strict=true,message="availability should be boolean type",groups={"updateDetails"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $availability;

    /**
     * 1=30 Day notice period
     * 2=60 Day notice period
     * 3=90 Day notice period
     * 4=I can provide a specific date
     *
     * @Assert\Choice({1,2,3,4},message="Select available period",groups={"updateDetails"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $availabilityPeriod;

    /**
     * @Assert\Date(message="dateAvailability should be date format",groups={"updateDetails"})
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateAvailability;

    /**
     * @Assert\Type(type="array",message="citiesWorking should be array type",groups={"updateDetails"})
     * @ORM\Column(type="array", nullable=true)
     */
    private $citiesWorking;

    /**
     * @Assert\Type(type="array",message="Copy Of ID should be array type",groups={"updateDetails"})
     * @ORM\Column(type="array", nullable=true)
     */
    private $copyOfID;

    /**
     * @Assert\Type(type="array",message="CV should be array type",groups={"updateDetails"})
     * @ORM\Column(type="array", nullable=true)
     */
    private $cv;


    /**
     * @Assert\Choice({false,true},strict=true,message="universityExemption should be boolean type",groups={"updateDetails"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $universityExemption;

    /**
     * @Assert\Type(type="array",message="matricCertificate should be array type",groups={"updateDetails"})
     * @ORM\Column(type="array", nullable=true)
     */
    private $matricCertificate;

    /**
     * @Assert\Type(type="array",message="Matric Transcript should be array type",groups={"updateDetails"})
     * @ORM\Column(type="array", nullable=true)
     */
    private $matricTranscript;

    /**
     * @Assert\Type(type="array",message="Certificate of Qualification should be array type",groups={"updateDetails"})
     * @ORM\Column(type="array", nullable=true)
     */
    private $certificateOfQualification;

    /**
     * @Assert\Type(type="array",message="Academic Transcript should be array type",groups={"updateDetails"})
     * @ORM\Column(type="array", nullable=true)
     */
    private $academicTranscript;

    /**
     * @Assert\Type(type="array",message="creditCheck should be array type",groups={"updateDetails"})
     * @ORM\Column(type="array", nullable=true)
     */
    private $creditCheck;

    /**
     * @Assert\Type(type="array",message="payslip should be array type",groups={"updateDetails"})
     * @ORM\Column(type="array", nullable=true)
     */
    private $payslip;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $video;

    /**
     * @ORM\Column(type="integer")
     */
    private $percentage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $looking;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default":0})
     */
    private $view;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default":0})
     */
    private $play;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastDeactivated;

    /**
     * @Assert\Choice({false,true},strict=true,message="Is First Job should be boolean type",groups={"updateDetails"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $firstJob;

    /**
     * ProfileDetails constructor.
     * @param $user
     * @param $idNumber
     */
    public function __construct($user, $idNumber)
    {
        $this->user = $user;
        $this->idNumber = $idNumber;
        $this->percentage = 10;
        $this->looking = false;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
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
    public function getIdNumber()
    {
        return $this->idNumber;
    }

    /**
     * @param mixed $idNumber
     */
    public function setIdNumber($idNumber)
    {
        $this->idNumber = $idNumber;
    }

    /**
     * @return mixed
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * @param mixed $nationality
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    }

    /**
     * @return mixed
     */
    public function getEthnicity()
    {
        return $this->ethnicity;
    }

    /**
     * @param mixed $ethnicity
     */
    public function setEthnicity($ethnicity)
    {
        $this->ethnicity = $ethnicity;
    }

    /**
     * @return mixed
     */
    public function getBeeCheck()
    {
        return $this->beeCheck;
    }

    /**
     * @param mixed $beeCheck
     */
    public function setBeeCheck($beeCheck)
    {
        $this->beeCheck = $beeCheck;
    }

    /**
     * @return mixed
     */
    public function getMostRole()
    {
        return $this->mostRole;
    }

    /**
     * @param mixed $mostRole
     */
    public function setMostRole($mostRole)
    {
        $this->mostRole = $mostRole;
    }

    /**
     * @return mixed
     */
    public function getMostEmployer()
    {
        return $this->mostEmployer;
    }

    /**
     * @param mixed $mostEmployer
     */
    public function setMostEmployer($mostEmployer)
    {
        $this->mostEmployer = $mostEmployer;
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
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * @param mixed $dateOfBirth
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return mixed
     */
    public function getMostSalary()
    {
        return $this->mostSalary;
    }

    /**
     * @param mixed $mostSalary
     */
    public function setMostSalary($mostSalary)
    {
        $this->mostSalary = $mostSalary;
    }

    /**
     * @return mixed
     */
    public function getSalaryPeriod()
    {
        return $this->salaryPeriod;
    }

    /**
     * @param mixed $salaryPeriod
     */
    public function setSalaryPeriod($salaryPeriod)
    {
        $this->salaryPeriod = $salaryPeriod;
    }

    /**
     * @return mixed
     */
    public function getCriminal()
    {
        return $this->criminal;
    }

    /**
     * @param mixed $criminal
     */
    public function setCriminal($criminal)
    {
        $this->criminal = $criminal;
    }

    /**
     * @return mixed
     */
    public function getCriminalDescription()
    {
        return $this->criminalDescription;
    }

    /**
     * @param mixed $criminalDescription
     */
    public function setCriminalDescription($criminalDescription)
    {
        $this->criminalDescription = $criminalDescription;
    }

    /**
     * @return mixed
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * @param mixed $credit
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;
    }

    /**
     * @return mixed
     */
    public function getCreditDescription()
    {
        return $this->creditDescription;
    }

    /**
     * @param mixed $creditDescription
     */
    public function setCreditDescription($creditDescription)
    {
        $this->creditDescription = $creditDescription;
    }

    /**
     * @return mixed
     */
    public function getHomeAddress()
    {
        return $this->homeAddress;
    }

    /**
     * @param mixed $homeAddress
     */
    public function setHomeAddress($homeAddress)
    {
        $this->homeAddress = $homeAddress;
    }

    /**
     * @return mixed
     */
    public function getDriverLicense()
    {
        return $this->driverLicense;
    }

    /**
     * @param mixed $driverLicense
     */
    public function setDriverLicense($driverLicense)
    {
        $this->driverLicense = $driverLicense;
    }

    /**
     * @return mixed
     */
    public function getDriverNumber()
    {
        return $this->driverNumber;
    }

    /**
     * @param mixed $driverNumber
     */
    public function setDriverNumber($driverNumber)
    {
        $this->driverNumber = $driverNumber;
    }

    /**
     * @return mixed
     */
    public function getEnglishProficiency()
    {
        return $this->englishProficiency;
    }

    /**
     * @param mixed $englishProficiency
     */
    public function setEnglishProficiency($englishProficiency)
    {
        $this->englishProficiency = $englishProficiency;
    }

    /**
     * @return mixed
     */
    public function getEmployed()
    {
        return $this->employed;
    }

    /**
     * @param mixed $employed
     */
    public function setEmployed($employed)
    {
        $this->employed = $employed;
    }

    /**
     * @return mixed
     */
    public function getEmployedDate()
    {
        return $this->employedDate;
    }

    /**
     * @param mixed $employedDate
     */
    public function setEmployedDate($employedDate)
    {
        $this->employedDate = $employedDate;
    }

    /**
     * @return mixed
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * @param mixed $availability
     */
    public function setAvailability($availability)
    {
        $this->availability = $availability;
    }

    /**
     * @return mixed
     */
    public function getAvailabilityPeriod()
    {
        return $this->availabilityPeriod;
    }

    /**
     * @param mixed $availabilityPeriod
     */
    public function setAvailabilityPeriod($availabilityPeriod)
    {
        $this->availabilityPeriod = $availabilityPeriod;
    }

    /**
     * @return mixed
     */
    public function getDateAvailability()
    {
        return $this->dateAvailability;
    }

    /**
     * @param mixed $dateAvailability
     */
    public function setDateAvailability($dateAvailability)
    {
        $this->dateAvailability = $dateAvailability;
    }

    /**
     * @return mixed
     */
    public function getCitiesWorking()
    {
        return $this->citiesWorking;
    }

    /**
     * @param mixed $citiesWorking
     */
    public function setCitiesWorking($citiesWorking)
    {
        $this->citiesWorking = $citiesWorking;
    }

    /**
     * @return mixed
     */
    public function getCopyOfID()
    {
        return $this->copyOfID;
    }

    /**
     * @param mixed $copyOfID
     */
    public function setCopyOfID($copyOfID)
    {
        $this->copyOfID = $copyOfID;
    }

    /**
     * @return mixed
     */
    public function getCv()
    {
        return $this->cv;
    }

    /**
     * @param mixed $cv
     */
    public function setCv($cv)
    {
        $this->cv = $cv;
    }

    /**
     * @return mixed
     */
    public function getUniversityExemption()
    {
        return $this->universityExemption;
    }

    /**
     * @param mixed $universityExemption
     */
    public function setUniversityExemption($universityExemption)
    {
        $this->universityExemption = $universityExemption;
    }

    /**
     * @return mixed
     */
    public function getMatricCertificate()
    {
        return $this->matricCertificate;
    }

    /**
     * @param mixed $matricCertificate
     */
    public function setMatricCertificate($matricCertificate)
    {
        $this->matricCertificate = $matricCertificate;
    }

    /**
     * @return mixed
     */
    public function getMatricTranscript()
    {
        return $this->matricTranscript;
    }

    /**
     * @param mixed $matricTranscript
     */
    public function setMatricTranscript($matricTranscript)
    {
        $this->matricTranscript = $matricTranscript;
    }

    /**
     * @return mixed
     */
    public function getCertificateOfQualification()
    {
        return $this->certificateOfQualification;
    }

    /**
     * @param mixed $certificateOfQualification
     */
    public function setCertificateOfQualification($certificateOfQualification)
    {
        $this->certificateOfQualification = $certificateOfQualification;
    }

    /**
     * @return mixed
     */
    public function getAcademicTranscript()
    {
        return $this->academicTranscript;
    }

    /**
     * @param mixed $academicTranscript
     */
    public function setAcademicTranscript($academicTranscript)
    {
        $this->academicTranscript = $academicTranscript;
    }

    /**
     * @return mixed
     */
    public function getCreditCheck()
    {
        return $this->creditCheck;
    }

    /**
     * @param mixed $creditCheck
     */
    public function setCreditCheck($creditCheck)
    {
        $this->creditCheck = $creditCheck;
    }

    /**
     * @return mixed
     */
    public function getPayslip()
    {
        return $this->payslip;
    }

    /**
     * @param mixed $payslip
     */
    public function setPayslip($payslip)
    {
        $this->payslip = $payslip;
    }

    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param mixed $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * @return mixed
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @param mixed $video
     */
    public function setVideo($video)
    {
        $this->video = $video;
    }

    /**
     * @return mixed
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * @param mixed $percentage
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;
    }

    /**
     * @return mixed
     */
    public function getLooking()
    {
        return $this->looking;
    }

    /**
     * @param mixed $looking
     */
    public function setLooking($looking)
    {
        $this->looking = $looking;
    }

    /**
     * @return mixed
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param mixed $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * @return mixed
     */
    public function getPlay()
    {
        return $this->play;
    }

    /**
     * @param mixed $play
     */
    public function setPlay($play)
    {
        $this->play = $play;
    }

    /**
     * @return mixed
     */
    public function getLastDeactivated()
    {
        return $this->lastDeactivated;
    }

    /**
     * @param mixed $lastDeactivated
     */
    public function setLastDeactivated($lastDeactivated)
    {
        $this->lastDeactivated = $lastDeactivated;
    }

    /**
     * @return mixed
     */
    public function getFirstJob()
    {
        return $this->firstJob;
    }

    /**
     * @param mixed $firstJob
     */
    public function setFirstJob($firstJob)
    {
        $this->firstJob = $firstJob;
    }

    /**
     * @param array $parameters
     */
    public function update($parameters = array()){
        foreach($parameters as $key => $value) {
            if(property_exists($this,$key) && $key!='user'){
                if($key == 'dateOfBirth' || $key == 'beeCheck' || $key == 'employedDate' || $key == 'dateAvailability'){
                    $now = new \DateTime();
                    $newDate =(!empty($value) && $value != "null") ? ($value instanceof \DateTime ) ? $value : new \DateTime($value) : NULL;
                    if($newDate instanceof \DateTime){
                        $newDate->setTimezone($now->getTimezone());
                    }
                    $this->$key = $newDate;
                }
                elseif ($key == 'citiesWorking'){
                    if(!empty($value) && $value != "null" && $value != NULL){
                        if(is_array($value)){
                            $this->$key = $value;
                        }
                        else{
                            $cities = explode(',',$value);
                            if(is_array($cities)){
                                $this->$key = $cities;
                            }
                            else{
                                $this->$key = NULL;
                            }
                        }
                    }
                    else{
                        $this->$key = NULL;
                    }
                }
                elseif(is_bool($value)){
                    $this->$key = $value;
                }
                elseif ($key == 'mostSalary'){
                    if($value >= 0){
                        $this->$key = $value;
                    }
                    else{
                        $this->$key = null;
                    }
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
     * @param array $parameters
     */
    public function updateForm($parameters = array()) {
        foreach($parameters as $key => $value) {
            if(property_exists($this,$key) && $key!='user'){
                if($value === 'null' || $value === NULL){
                    $this->$key = NULL;
                }
                else{
                    if($key == 'dateOfBirth' || $key == 'beeCheck' || $key == 'employedDate' || $key == 'dateAvailability'){
                        $now = new \DateTime();
                        $newDate =(!empty($value) && $value != "null") ? ($value instanceof \DateTime ) ? $value : new \DateTime($value) : NULL;
                        if($newDate instanceof \DateTime){
                            $newDate->setTimezone($now->getTimezone());
                        }
                        $this->$key = $newDate;
                    }
                    elseif (
                        $key == 'criminal' || $key == 'credit' || $key == 'availability' || $key == 'employed'
                        || $key == 'driverLicense' || $key == 'universityExemption' || $key == 'firstJob')
                    {
                        if($value == "true" || $value === true){
                            $this->$key = true;
                        }
                        elseif ($value == "false" || $value === false){
                            $this->$key = false;
                        }
                        else{
                            $this->$key = NULL;
                        }
                    }
                    elseif ($key == 'citiesWorking'){
                        if(!empty($value)){
                            if(is_array($value)){
                                $this->$key = $value;
                            }
                            else{
                                $cities = explode(',',$value);
                                if(is_array($cities)){
                                    $this->$key = $cities;
                                }
                                else{
                                    $this->$key = NULL;
                                }
                            }
                        }
                        else{
                            $this->$key = NULL;
                        }
                    }
                    elseif ($key == 'mostSalary'){
                        if($value >= 0){
                            $this->$key = $value;
                        }
                        else{
                            $this->$key = null;
                        }
                    }
                    else{
                        $this->$key = $value;
                    }
                }
            }
        }
    }

}