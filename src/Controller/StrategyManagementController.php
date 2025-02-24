<?php

namespace App\Controller;

use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;

class StrategyManagementController extends BaseController
{
    public function indexAction(Request $request)
    {
        return $this->render('advisory/overview-20230717d.html.twig', [
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }
    public function ipStartAction(Request $request)
    {
        return $this->render('strategyManagement/ip-start.html.twig', [
            'template_layout_name' => 'layouts/layout-str-management.html.twig'
        ]);
    }
    public function ipCapabilitiesAction(Request $request)
    {
        return $this->render('strategyManagement/ip-capabilities.html.twig', [
            'template_layout_name' => 'layouts/layout-str-management.html.twig'
        ]);
    }
    public function freeConsultationAction(Request $request)
    {
        return $this->render('strategyManagement/free-consultation.html.twig', [
            'template_layout_name' => 'layouts/layout-str-management.html.twig'
        ]);
    }
}
