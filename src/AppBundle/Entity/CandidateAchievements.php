<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CandidateAchievements
 *
 * @ORM\Table(name="candidate_achievements")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CandidateAchievementsRepository")
 */
class CandidateAchievements
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
     * @var string
     * @Assert\NotBlank(
     *     message="description should not be blank",
     *     groups={"validateAchievements"}
     * )
     * @Assert\Length(max="50", maxMessage="description Max 50 Characters",groups={"validateAchievements"})
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * CandidateAchievements constructor.
     * @param $user
     * @param string $description
     */
    public function __construct($user, $description)
    {
        $this->user = $user;
        $this->description = $description;
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
     * Set description.
     *
     * @param string $description
     *
     * @return CandidateAchievements
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
}
