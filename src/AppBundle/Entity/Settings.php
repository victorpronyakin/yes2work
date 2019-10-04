<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Settings
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingsRepository")
 */
class Settings
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
     * @ORM\Column(type="boolean", nullable=true, options={"default":true})
     */
    private $allowVideo;

    /**
     * Settings constructor.
     * @param $allowVideo
     */
    public function __construct($allowVideo = false)
    {
        $this->allowVideo = $allowVideo;
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
    public function getAllowVideo()
    {
        return $this->allowVideo;
    }

    /**
     * @param mixed $allowVideo
     */
    public function setAllowVideo($allowVideo)
    {
        $this->allowVideo = $allowVideo;
    }
}
