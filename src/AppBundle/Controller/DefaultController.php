<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        $mobileDetector = $this->get('mobile_detect.mobile_detector');
        if(($mobileDetector->isiOS() || (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') && !strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome'))) && $_SERVER['REQUEST_URI'] == '/'){
            return $this->redirect('/home');
        }
        return $this->render('default/index.html.twig');
    }

    public function handleOptionsAction(){

        $response = new Response();
        $response->headers->set('Access-Control-Allow-Methods','GET, POST, PUT, DELETE, PATCH, OPTIONS');
        $response->headers->set('Access-Control-Allow-Credentials', false);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, *');
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }
}
