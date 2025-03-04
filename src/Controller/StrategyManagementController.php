<?php

namespace App\Controller;

use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;

class StrategyManagementController extends BaseController
{
    public function overviewAction(Request $request)
    {
        // return $this->render('advisory/overview.html.twig');
        return $this->render('strategyManagement/overview.html.twig', [
            'template_layout_name' => 'layouts/layout-str-management.html.twig'
        ]);
    }

    public function indexAction(Request $request)
    {
        return $this->redirect('ip-strategy/overview');
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
        $shares = DataObject\Shares::getList();
        $list['shares'] = $shares;
        dd($list['shares']);
        return $this->render('strategyManagement/free-consultation.html.twig', [
            'template_layout_name' => 'layouts/layout-str-management.html.twig',
            'shares' => $list['shares']
        ]);
    }
}
