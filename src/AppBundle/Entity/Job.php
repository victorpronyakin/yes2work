<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Job
 *
 * @ORM\Table(name="job")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JobRepository")
 */
class Job
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
     *     message="jobTitle should not be blank",
     *     groups={"Jobs"}
     * )
     * @ORM\Column(type="string")
     */
    private $jobTitle;

    /**
     * @Assert\Type(
     *     type="array",
     *     message="Industry invalid value",
     *     groups={"updateCompany"}
     * )
     * @ORM\Column(type="array")
     */
    private $industry;

    /**
     * @Assert\Type(
     *     type="array",
     *     message="Second industry invalid value",
     *     groups={"updateCompany"}
     * )
     * @ORM\Column(type="array", nullable=true)
     */
    private $industrySecondary;

    /**
     *  @Assert\NotBlank(
     *     message="companyName should not be blank",
     *     groups={"Jobs"}
     * )
     * @ORM\Column(type="string")
     */
    private $companyName;

    /**
     *  @Assert\NotBlank(
     *     message="companyAddress should not be blank",
     *     groups={"Jobs"}
     * )
     * @ORM\Column(type="string")
     */
    private $companyAddress;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $addressCountry;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $addressState;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $addressZipCode;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $addressCity;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $addressSuburb;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $addressStreet;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $addressStreetNumber;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $addressBuildName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $addressUnit;

    /**
     * @Assert\Length(
     *     max="300",
     *     maxMessage="Maximum 300 Characters",
     *     min="50",
     *     minMessage="Minimum 50 Characters"
     * )
     * @Assert\NotBlank(
     *     message="companyDescription should not be blank",
     *     groups={"Jobs"}
     * )
     * @ORM\Column(type="text")
     */
    private $companyDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $companyDescriptionChange;

    /**
     * @Assert\Length(
     *     max="400",
     *     maxMessage="Maximum 400 Characters"
     * )
     * @Assert\NotBlank(
     *     message="roleDescription should not be blank",
     *     groups={"Jobs"}
     * )
     * @ORM\Column(type="text")
     */
    private $roleDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $roleDescriptionChange;

    /**
     * @Assert\NotBlank(
     *     message="closureDate should not be blank",
     *     groups={"Jobs"}
     * )
     * @Assert\Date(
     *     message="closureDate should be date type",
     *     groups={"Jobs"}
     * )
     * @ORM\Column(type="datetime")
     */
    private $closureDate;

    /**
     * @Assert\NotBlank(
     *     message="jobClosureDate should not be blank",
     *     groups={"Jobs"}
     * )
     * @Assert\Date(
     *     message="jobClosureDate should be date type",
     *     groups={"Jobs"}
     * )
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $jobClosureDate;

    /**
     * @Assert\GreaterThanOrEqual(
     *     message="salaryRange Invalid value",
     *     value="0",
     *     groups={"Jobs"}
     * )
     * @Assert\LessThanOrEqual(
     *     message="salaryRange Invalid value",
     *     value="3",
     *     groups={"Jobs"}
     * )
     * @ORM\Column(type="integer", nullable=true)
     * 0 = None
     * 1 = 700K
     * 2 = 700K-1 million
     * 3 = >1 million
     */
    private $salaryRange;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $approve;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $started;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $spec;
	/**
	 * @Assert\Length(
	 *     max="10",
	 *     maxMessage="Maximum 10 Characters"
	 * )
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $jobReference; //Step 1

	/**
	 * @Assert\NotBlank(
	 *     message="typeOfEmployment should not be blank",
	 *     groups={"Jobs"}
	 * )
	 * @Assert\Choice(
	 *     {"Contract","Permanent","Temporary"},
	 *     message="typeOfEmployment is invalid. should be Contract or Permanent or Temporary",
	 *     groups={"Jobs"}
	 * )
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $typeOfEmployment;//Step 1

	/**
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $timePeriod;//Step 1

	/**
	 * @Assert\GreaterThanOrEqual(
	 *     message="salaryFrom Invalid value",
	 *     value="0",
	 *     groups={"Jobs"}
	 * )
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $salaryFrom = 0;//Step 1

	/**
	 * @Assert\GreaterThanOrEqual(
	 *     message="salaryTo Invalid value",
	 *     value="0",
	 *     groups={"Jobs"}
	 * )
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $salaryTo = 0;//Step 1

	// STEP 2
	/**
	 * @Assert\NotBlank(
	 *     message="eligibility should not be blank",
	 *     groups={"Jobs"}
	 * )
	 * @Assert\Choice(
	 *     {"All","applicable"},
	 *     message="eligibility is invalid. should be All or applicable",
	 *     groups={"Jobs"}
	 * )
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $eligibility;
	/**
	 * @Assert\NotBlank(
	 *     message="gender should not be blank",
	 *     groups={"Jobs"}
	 * )
	 * @Assert\Choice(
	 *     {"All","Male","Female"},
	 *     message="gender is invalid. should be All or Male or Female",
	 *     groups={"Jobs"}
	 * )
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $gender;
	/**
	 * @Assert\NotBlank(
	 *     message="ethnicity should not be blank",
	 *     groups={"Jobs"}
	 * )
	 * None OR Black OR White Or Coloured Or Indian Or Oriental
	 *
	 * @ORM\Column(type="array", nullable=true)
	 */
	private $ethnicity;
	/**
	 * @Assert\NotBlank(
	 *     message="location should not be blank",
	 *     groups={"Jobs"}
	 * )
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $location;
	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $highestQualification;
	/**
	 * @ORM\Column(type="array", nullable=true)
	 */
	private $field;
	/**
	 *
	 * 0 All
	 * 1 0
	 * 2 0-1
	 * 3 1-2
	 * 4 3-5
	 * 5 5+
	 *
	 * @ORM\Column(type="array", nullable=true)
	 */
	private $yearsOfWorkExperience;
	/**
	 * @Assert\NotBlank(
	 *     message="availability should not be blank",
	 *     groups={"Jobs"}
	 * )
	 * 0 = All Candidates
	 * 1 = Immediately
	 * 2 = Within 1 month
	 * 3 = Within 3 months
	 * @ORM\Column(type="array", nullable=true)
	 */
	private $availability;
	/**
	 * @Assert\Choice(choices={0, 1, 2}, message="Choose a valid Video.", groups={"Jobs"})
	 * 0 = All Candidates
	 * 1 = With Video
	 * 2 = Without Video
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $video = 0;
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $monthSalaryFrom;
	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $monthSalaryTo;

    /**
     * All - 1
     * Yes - 2
     * No - 3
     * @ORM\Column(type="string", nullable=true)
     */
    private $assessment;

    /**
     * Job constructor.
     * @param $user
     * @param array $params
     * @throws \Exception
     */
    public function __construct($user, $params=array())
    {
        $this->user = $user;
        $this->approve = null;
        $this->status = true;
        $this->created = new \DateTime();
        foreach($params as $key => $value) {
            if(property_exists($this,$key) && $key!='user'){
                if($key == 'jobClosureDate' || $key == 'closureDate' || $key == 'started' || $key == 'filled'){
                    if($value != null && $value != 'null'){
                        $this->$key = new \DateTime($value);
                    }
                    else{
                        $this->$key = null;
                    }
                }
                elseif ($key == 'availability' || $key == 'industry' ||
					$key == 'industrySecondary' || $key == 'field' ||
					$key == 'ethnicity' || $key == 'yearsOfWorkExperience'){
                    if($key == 'industrySecondary' && empty($value)){
                        $this->$key = [];
                    }
                    else{
                        if(is_array($value)){
                            $this->$key = $value;
                        }
                        else{
                            $array = explode(',', $value);
                            $this->$key = $array;
                        }
                    }
                }
                else{
                    if(is_null($value) || $value == 'null'){
                        $this->$key = null;
                    }
                    else{
                        $this->$key = $value;
                    }
                }
            }
        }
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
    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    /**
     * @param mixed $jobTitle
     */
    public function setJobTitle($jobTitle)
    {
        $this->jobTitle = $jobTitle;
    }

    /**
     * @return mixed
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * @param mixed $industry
     */
    public function setIndustry($industry)
    {
        $this->industry = $industry;
    }

    /**
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param mixed $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     * @return mixed
     */
    public function getCompanyAddress()
    {
        return $this->companyAddress;
    }

    /**
     * @param mixed $companyAddress
     */
    public function setCompanyAddress($companyAddress)
    {
        $this->companyAddress = $companyAddress;
    }

    /**
     * @return mixed
     */
    public function getAddressCountry()
    {
        return $this->addressCountry;
    }

    /**
     * @param mixed $addressCountry
     */
    public function setAddressCountry($addressCountry)
    {
        $this->addressCountry = $addressCountry;
    }

    /**
     * @return mixed
     */
    public function getAddressState()
    {
        return $this->addressState;
    }

    /**
     * @param mixed $addressState
     */
    public function setAddressState($addressState)
    {
        $this->addressState = $addressState;
    }

    /**
     * @return mixed
     */
    public function getAddressZipCode()
    {
        return $this->addressZipCode;
    }

    /**
     * @param mixed $addressZipCode
     */
    public function setAddressZipCode($addressZipCode)
    {
        $this->addressZipCode = $addressZipCode;
    }

    /**
     * @return mixed
     */
    public function getAddressCity()
    {
        return $this->addressCity;
    }

    /**
     * @return mixed
     */
    public function getAddressSuburb()
    {
        return $this->addressSuburb;
    }

    /**
     * @param mixed $addressSuburb
     */
    public function setAddressSuburb($addressSuburb)
    {
        $this->addressSuburb = $addressSuburb;
    }

    /**
     * @param mixed $addressCity
     */
    public function setAddressCity($addressCity)
    {
        $this->addressCity = $addressCity;
    }

    /**
     * @return mixed
     */
    public function getAddressStreet()
    {
        return $this->addressStreet;
    }

    /**
     * @param mixed $addressStreet
     */
    public function setAddressStreet($addressStreet)
    {
        $this->addressStreet = $addressStreet;
    }

    /**
     * @return mixed
     */
    public function getAddressStreetNumber()
    {
        return $this->addressStreetNumber;
    }

    /**
     * @param mixed $addressStreetNumber
     */
    public function setAddressStreetNumber($addressStreetNumber)
    {
        $this->addressStreetNumber = $addressStreetNumber;
    }

    /**
     * @return mixed
     */
    public function getAddressBuildName()
    {
        return $this->addressBuildName;
    }

    /**
     * @param mixed $addressBuildName
     */
    public function setAddressBuildName($addressBuildName)
    {
        $this->addressBuildName = $addressBuildName;
    }

    /**
     * @return mixed
     */
    public function getAddressUnit()
    {
        return $this->addressUnit;
    }

    /**
     * @param mixed $addressUnit
     */
    public function setAddressUnit($addressUnit)
    {
        $this->addressUnit = $addressUnit;
    }

    /**
     * @return mixed
     */
    public function getCompanyDescription()
    {
        return $this->companyDescription;
    }

    /**
     * @param mixed $companyDescription
     */
    public function setCompanyDescription($companyDescription)
    {
        $this->companyDescription = $companyDescription;
    }

    /**
     * @return mixed
     */
    public function getCompanyDescriptionChange()
    {
        return $this->companyDescriptionChange;
    }

    /**
     * @param mixed $companyDescriptionChange
     */
    public function setCompanyDescriptionChange($companyDescriptionChange)
    {
        $this->companyDescriptionChange = $companyDescriptionChange;
    }

    /**
     * @return mixed
     */
    public function getRoleDescription()
    {
        return $this->roleDescription;
    }

    /**
     * @param mixed $roleDescription
     */
    public function setRoleDescription($roleDescription)
    {
        $this->roleDescription = $roleDescription;
    }

    /**
     * @return mixed
     */
    public function getClosureDate()
    {
        return $this->closureDate;
    }

    /**
     * @param mixed $closureDate
     */
    public function setClosureDate($closureDate)
    {
        $this->closureDate = $closureDate;
    }

    /**
     * @return mixed
     */
    public function getJobClosureDate()
    {
        return $this->jobClosureDate;
    }

    /**
     * @param mixed $jobClosureDate
     */
    public function setJobClosureDate($jobClosureDate)
    {
        $this->jobClosureDate = $jobClosureDate;
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
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @param mixed $video
     */
    public function setVideo($video)
    {
//        $this->video = $video;
        $this->video = 0;
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
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getSalaryRange()
    {
        return $this->salaryRange;
    }

    /**
     * @param mixed $salaryRange
     */
    public function setSalaryRange($salaryRange)
    {
        $this->salaryRange = $salaryRange;
    }

    /**
     * @return mixed
     */
    public function getApprove()
    {
        return $this->approve;
    }

    /**
     * @param mixed $approve
     */
    public function setApprove($approve)
    {
        $this->approve = $approve;
    }

    /**
     * @return mixed
     */
    public function getRoleDescriptionChange()
    {
        return $this->roleDescriptionChange;
    }

    /**
     * @param mixed $roleDescriptionChange
     */
    public function setRoleDescriptionChange($roleDescriptionChange)
    {
        $this->roleDescriptionChange = $roleDescriptionChange;
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
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * @param mixed $started
     */
    public function setStarted($started)
    {
        $this->started = $started;
    }

    /**
     * @return mixed
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * @param mixed $spec
     */
    public function setSpec($spec)
    {
        $this->spec = $spec;
    }

	/**
	 * @return mixed
	 */
	public function getJobReference()
	{
		return $this->jobReference;
	}

	/**
	 * @param mixed $jobReference
	 */
	public function setJobReference($jobReference)
	{
		$this->jobReference = $jobReference;
	}

	/**
	 * @return mixed
	 */
	public function getTypeOfEmployment()
	{
		return $this->typeOfEmployment;
	}

	/**
	 * @param mixed $typeOfEmployment
	 */
	public function setTypeOfEmployment($typeOfEmployment)
	{
		$this->typeOfEmployment = $typeOfEmployment;
	}

	/**
	 * @return mixed
	 */
	public function getIndustrySecondary()
	{
		return $this->industrySecondary;
	}

	/**
	 * @param mixed $industrySecondary
	 */
	public function setIndustrySecondary($industrySecondary)
	{
		$this->industrySecondary = $industrySecondary;
	}

	/**
	 * @return mixed
	 */
	public function getSalaryFrom()
	{
		return $this->salaryFrom;
	}

	/**
	 * @param mixed $salaryFrom
	 */
	public function setSalaryFrom($salaryFrom)
	{
		$this->salaryFrom = $salaryFrom;
	}

	/**
	 * @return mixed
	 */
	public function getSalaryTo()
	{
		return $this->salaryTo;
	}

	/**
	 * @param mixed $salaryTo
	 */
	public function setSalaryTo($salaryTo)
	{
		$this->salaryTo = $salaryTo;
	}

	/**
	 * @return mixed
	 */
	public function getTimePeriod()
	{
		return $this->timePeriod;
	}

	/**
	 * @param mixed $timePeriod
	 */
	public function setTimePeriod($timePeriod)
	{
		$this->timePeriod = $timePeriod;
	}

	/**
	 * @return mixed
	 */
	public function getYearsOfWorkExperience()
	{
		return $this->yearsOfWorkExperience;
	}

	/**
	 * @param mixed $yearsOfWorkExperience
	 */
	public function setYearsOfWorkExperience($yearsOfWorkExperience)
	{
		$this->yearsOfWorkExperience = $yearsOfWorkExperience;
	}

	/**
	 * @return mixed
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * @param mixed $field
	 */
	public function setField($field)
	{
		$this->field = $field;
	}

	/**
	 * @return mixed
	 */
	public function getMonthSalaryFrom()
	{
		return $this->monthSalaryFrom;
	}

	/**
	 * @param mixed $monthSalaryFrom
	 */
	public function setMonthSalaryFrom($monthSalaryFrom)
	{
		$this->monthSalaryFrom = $monthSalaryFrom;
	}

	/**
	 * @return mixed
	 */
	public function getMonthSalaryTo()
	{
		return $this->monthSalaryTo;
	}

	/**
	 * @param mixed $monthSalaryTo
	 */
	public function setMonthSalaryTo($monthSalaryTo)
	{
		$this->monthSalaryTo = $monthSalaryTo;
	}

	/**
	 * @return mixed
	 */
	public function getHighestQualification()
	{
		return $this->highestQualification;
	}

	/**
	 * @param mixed $highestQualification
	 */
	public function setHighestQualification($highestQualification)
	{
		$this->highestQualification = $highestQualification;
	}

	/**
	 * @return mixed
	 */
	public function getEligibility()
	{
		return $this->eligibility;
	}

	/**
	 * @param mixed $eligibility
	 */
	public function setEligibility($eligibility)
	{
		$this->eligibility = $eligibility;
	}

    /**
     * @return mixed
     */
    public function getAssessment()
    {
        return $this->assessment;
    }

    /**
     * @param mixed $assessment
     */
    public function setAssessment($assessment)
    {
        $this->assessment = $assessment;
    }

    /**
     * @param array $params
     * @throws \Exception
     */
	public function update($params=array())
	{
		$this->approve = NULL;
		foreach($params as $key => $value) {
			if(property_exists($this,$key) && $key!='user' && $key != 'status' && $key != 'approve' && $key != 'created'){
				if($key == 'jobClosureDate' || $key == 'closureDate' || $key == 'started' || $key == 'filled'){
					if($value != null && $value != 'null'){
						$this->$key = new \DateTime($value);
					}
					else{
						$this->$key = null;
					}
				}
				elseif ($key == 'availability' || $key == 'industry' || $key == 'industrySecondary'
					|| $key == 'field' || $key == 'ethnicity' || $key == 'yearsOfWorkExperience'){
					if(is_array($value)){
						$this->$key = $value;
					}
					else{
						$array = explode(',', $value);
						$this->$key = $array;
					}
				}
				elseif ($key == 'highestQualification' || $key == 'location'){
				    if(is_array($value)){
                        $this->$key = implode(',', $value);
                    }
				    else{
                        $this->$key = $value;
                    }
                }
				else{
                    if(is_null($value) || $value == 'null'){
                        $this->$key = null;
                    }
                    else{
                        $this->$key = $value;
                    }
				}
			}
		}
	}
}
