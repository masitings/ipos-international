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
        $facebook = null;
        $twitter = null;
        $email = null;
        $linkedin = null;
        foreach ($shares as $item) {
            if ($item->getShare()->getClass() == 'share-icon facebook') {
                $facebook = $item;
            }
            if ($item->getShare()->getClass() == 'share-icon linkedin') {
                $linkedin = $item;
            }
            if ($item->getShare()->getClass() == 'share-icon email') {
                $email = $item;
            }
            if ($item->getShare()->getClass() == 'share-icon twitter') {
                $twitter = $item;
            }
        }

        return $this->render('strategyManagement/overview.html.twig', [
            'template_layout_name' => 'layouts/layout-str-management-overview.html.twig',
            'shares' => $list['shares'],
            'facebook' => $facebook,
            'twitter' => $twitter,
            'email' => $email,
            'linkedin' => $linkedin
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
        $facebook = null;
        $twitter = null;
        $email = null;
        $linkedin = null;
        foreach ($shares as $item) {
            if ($item->getShare()->getClass() == 'share-icon facebook') {
                $facebook = $item;
            }
            if ($item->getShare()->getClass() == 'share-icon linkedin') {
                $linkedin = $item;
            }
            if ($item->getShare()->getClass() == 'share-icon email') {
                $email = $item;
            }
            if ($item->getShare()->getClass() == 'share-icon twitter') {
                $twitter = $item;
            }
        }
        return $this->render('strategyManagement/ip-start.html.twig', [
            'template_layout_name' => 'layouts/layout-str-management.html.twig',
            'shares' => $list['shares'],
            'facebook' => $facebook,
            'twitter' => $twitter,
            'email' => $email,
            'linkedin' => $linkedin
        ]);
    }
    public function ipCapabilitiesAction(Request $request)
    {
        $shares = DataObject\Shares::getList();
        $list['shares'] = $shares;
        $facebook = null;
        $twitter = null;
        $email = null;
        $linkedin = null;
        foreach ($shares as $item) {
            if ($item->getShare()->getClass() == 'share-icon facebook') {
                $facebook = $item;
            }
            if ($item->getShare()->getClass() == 'share-icon linkedin') {
                $linkedin = $item;
            }
            if ($item->getShare()->getClass() == 'share-icon email') {
                $email = $item;
            }
            if ($item->getShare()->getClass() == 'share-icon twitter') {
                $twitter = $item;
            }
        }
        return $this->render('strategyManagement/ip-capabilities.html.twig', [
            'template_layout_name' => 'layouts/layout-str-management.html.twig',
            'shares' => $list['shares'],
            'facebook' => $facebook,
            'twitter' => $twitter,
            'email' => $email,
            'linkedin' => $linkedin
        ]);
    }
    public function freeConsultationAction(Request $request)
    {
        $shares = DataObject\Shares::getList();
        $list['shares'] = $shares;
        $facebook = null;
        $twitter = null;
        $email = null;
        $linkedin = null;
        foreach ($shares as $item) {
            if ($item->getShare()->getClass() == 'share-icon facebook') {
                $facebook = $item;
            }
            if ($item->getShare()->getClass() == 'share-icon linkedin') {
                $linkedin = $item;
            }
            if ($item->getShare()->getClass() == 'share-icon email') {
                $email = $item;
            }
            if ($item->getShare()->getClass() == 'share-icon twitter') {
                $twitter = $item;
            }
        }
        return $this->render('strategyManagement/free-consultation.html.twig', [
            'template_layout_name' => 'layouts/layout-str-management.html.twig',
            'shares' => $list['shares'],
            'facebook' => $facebook,
            'twitter' => $twitter,
            'email' => $email,
            'linkedin' => $linkedin
        ]);
    }
}
