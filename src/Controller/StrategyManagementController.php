<?php

namespace App\Controller;

use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;

class StrategyManagementController extends BaseController
{
    public function overviewAction(Request $request)
    {
        // return $this->render('advisory/overview.html.twig');
        $shares = DataObject\Shares::getList();
        $list['shares'] = $shares;
        return $this->render('strategyManagement/overview.html.twig', [
            'template_layout_name' => 'layouts/layout-str-management.html.twig',
            'shares' => $list['shares']
        ]);
    }

    public function indexAction(Request $request)
    {
        return $this->redirect('ip-strategy/overview');
    }

    public function ipStartAction(Request $request)
    {
        $shares = DataObject\Shares::getList();
        $list['shares'] = $shares;
        return $this->render('strategyManagement/ip-start.html.twig', [
            'template_layout_name' => 'layouts/layout-str-management.html.twig',
            'shares' => $list['shares']
        ]);
    }
    public function ipCapabilitiesAction(Request $request)
    {
        $shares = DataObject\Shares::getList();
        $list['shares'] = $shares;
        return $this->render('strategyManagement/ip-capabilities.html.twig', [
            'template_layout_name' => 'layouts/layout-str-management.html.twig',
            'shares' => $list['shares']
        ]);
    }
    public function freeConsultationAction(Request $request)
    {
        $shares = DataObject\Shares::getList();
        $list['shares'] = $shares;
        return $this->render('strategyManagement/free-consultation.html.twig', [
            'template_layout_name' => 'layouts/layout-str-management.html.twig',
            'shares' => $list['shares']
        ]);
    }
}
