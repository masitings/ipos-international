<?php

namespace App\AppBundle\EventListener;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;



class AcmeExceptionListener {


    public function onKernelException(RequestEvent $requestEvent){

        $requestUri = $requestEvent->getRequest()->getRequestUri();
        $data = file_get_contents('static/links.json');
        $links = json_decode($data,true);


        foreach ($links as $link){
            if (trim(trim($link['i1_link']),'/') == trim($requestUri,'/')){

               

                header("Location: ".trim($link['i2_link']));
                exit();
            }
        }

    }
}
