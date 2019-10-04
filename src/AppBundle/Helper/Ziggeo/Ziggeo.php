<?php


namespace AppBundle\Helper\Ziggeo;


Class Ziggeo {

    private $token;
    private $private_key;
    private $encryption_key;

    function __construct($token, $private_key, $encryption_key = NULL) {
        $this->token = $token;
        $this->private_key = $private_key;
        $this->encryption_key = $encryption_key;
    }

    function token() {
        return $this->token;
    }

    function private_key() {
        return $this->private_key;
    }

    function encryption_key() {
        return $this->encryption_key;
    }

    private $config = NULL;

    function config() {
        if (!@$this->config)
            $this->config = new ZiggeoConfig();
        return $this->config;
    }

    private $connect = NULL;

    function connect() {
        if (!@$this->connect)
            $this->connect = new ZiggeoConnect($this);
        return $this->connect;
    }


    private $videos = NULL;

    function videos() {
        if (!@$this->videos)
            $this->videos = new ZiggeoVideos($this);
        return $this->videos;
    }



}
