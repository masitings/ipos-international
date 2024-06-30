<?php

namespace App\Controller;

use App\Services\ResourcesServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{

    /**
     * @Route("/api/show")
     * @return string
     */
    public function show(): Response
    {

        $list = (new ResourcesServices())->getResources();



        return new Response('jkj');
    }
}
