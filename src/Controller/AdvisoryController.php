<?php

namespace App\Controller;

use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;

class AdvisoryController extends BaseController
{
    public function overviewAction(Request $request)
    {
        // return $this->render('advisory/overview.html.twig');
        return $this->render('advisory/overview-20230717d.html.twig',[
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    public function indexAction(Request $request){
        return $this->redirect('advisory/overview');
    }

    public function ipChatAction(Request $request)
    {
        // return $this->render('advisory/ip-chat.html.twig');
        return $this->render('advisory/ip-chat-20230718b.html.twig',[
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }
    public function ipStartAction(Request $request)
    {
        // return $this->render('advisory/ip-chat.html.twig');
        return $this->render('advisory/ip-start-ggg.html.twig',[
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }
    public function ipStartActionTest(Request $request)
    {
        // return $this->render('advisory/ip-chat.html.twig');
        return $this->render('advisory/ip-start-fff.html.twig',[
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    public function patentAnalyticsAction(Request $request)
    {
        $interested = new DataObject\PatentAnalytic\Listing();

        $interested->setOrderKey('releaseDate');
        $interested->setOrder('DESC');
        $interested->getLimit(3);
        $interested->load();


        // return $this->render('advisory/patent-analytics.html.twig',[
        //     'list' => $interested
        // ]);
        return $this->render('advisory/patent-analytics-20230718b.html.twig',[
            'list' => $interested,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }




    public function discoverIntangibleAction(Request $request)
    {
        // return $this->render('advisory/discover-intangible-assets.html.twig');
        return $this->render('advisory/discover-intangible-assets-20230718b.html.twig',[
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }
}
