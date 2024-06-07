<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class DefaultController extends FrontendController
{

    public function defaultAction()
    {
        return [];
    }

    public function policyAction(Request $request)
    {
        return $this->render('layouts/policy-20230718.html.twig', [
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }
    public function privacyAction(Request $request)
    {
        return $this->render('layouts/privacy-20230718.html.twig', [
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }
    public function termsOfUseAction(Request $request)
    {
        return $this->render('layouts/terms-20230718.html.twig', [
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }
}
