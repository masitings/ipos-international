<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document\Page;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends FrontendController
{
    /**
     * @Template
     * @param Request $request
     * @return array
     */
    public function defaultAction(Request $request)
    {
       
/*
        return $this->render('default/default.html.twig',[
            'list' => $list
        ]);*/

        return [];
    }
    public function policyAction(Request $request)
    {
        return $this->render('layouts/policy-20230718.html.twig',[
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }
    public function privacyAction(Request $request)
    {
        return $this->render('layouts/privacy-20230718.html.twig',[
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }
    public function termsOfUseAction(Request $request)
    {
        return $this->render('layouts/terms-20230718.html.twig',[
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }
}
