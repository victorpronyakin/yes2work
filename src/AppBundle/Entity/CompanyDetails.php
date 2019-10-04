<?php
/**
 * Created by PhpStorm.
 * Date: 28.02.18
 * Time: 15:49
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompanyDetailsRepository")
 * @ORM\Table(name="company_details")
 */
class CompanyDetails
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
     * @Assert\NotBlank(
     *     message="companyName should not be blank",
     *     groups={"registerClient","updateCompany"}
     * )
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $address;

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
     * @Assert\GreaterThan(
     *     message="companySize Invalid value",
     *     value="0",
     *     groups={"updateCompany"}
     * )
     * @Assert\LessThan(
     *     message="companySize Invalid value",
     *     value="6",
     *     groups={"updateCompany"}
     * )
     * @ORM\Column(type="integer", nullable=true)
     * 1= 1-10E
     * 2= 11-50E
     * 3= 50-200E
     * 4= 200-1000E
     * 5= >1000E
     */
    private $companySize;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $jse;

    /**
     * @Assert\Type(
     *     type="array",
     *     message="Industry invalid value",
     *     groups={"updateCompany"}
     * )
     * @ORM\Column(type="array", nullable=true)
     *
     */
    private $industry;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $firstPopUp;

    /**
     * CompanyDetails constructor.
     * @param $user
     * @param $name
     */
    public function __construct($user, $name)
    {
        $this->user = $user;
        $this->name = $name;
        $this->firstPopUp = false;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
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
     * @param mixed $addressCity
     */
    public function setAddressCity($addressCity)
    {
        $this->addressCity = $addressCity;
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
    public function getCompanySize()
    {
        return $this->companySize;
    }

    /**
     * @param mixed $companySize
     */
    public function setCompanySize($companySize)
    {
        $this->companySize = $companySize;
    }

    /**
     * @return mixed
     */
    public function getJse()
    {
        return $this->jse;
    }

    /**
     * @param mixed $jse
     */
    public function setJse($jse)
    {
        $this->jse = $jse;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getFirstPopUp()
    {
        return $this->firstPopUp;
    }

    /**
     * @param mixed $firstPopUp
     */
    public function setFirstPopUp($firstPopUp)
    {
        $this->firstPopUp = $firstPopUp;
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