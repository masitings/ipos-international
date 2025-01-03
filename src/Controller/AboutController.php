<?php

namespace App\Controller;

use App\Services\AboutServices;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;

use App\Services\CourseServices;

use Pimcore\Model\Document;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use function Matrix\trace;

class  AboutController extends BaseController
{
    public function overviewAction(Request $request)
    {
        // return $this->render('about/index.html.twig');
        return $this->render('about/index-20230718.html.twig', [
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    public function tenderQuotationAction(Request $request)
    {
        // FAQ Content
        $list = new DataObject\TenderQuotationFaq\Listing();
        $ob = $list->getClass();
        $faqType = $ob->getFieldDefinition("questionType")->getOptions();
        $list->load();

        return $this->render('about/tender-quotation/index-20231130h.html.twig', [
            'list' => $list,
            'faqType' => $faqType,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    public function careerAction(Request $request)
    {
        // return $this->render('about/career/index.html.twig');

        return $this->render('about/career/index-20241230.html.twig', [
            'template_layout_name' => 'layouts/layout-20241230.html.twig'
        ]);
        // return $this->render('about/career/index-20230718.html.twig', [
        //     'template_layout_name' => 'layouts/layout-20230718.html.twig'
        // ]);
    }

    public function jobOpeningAction(Request $request)
    {
        return $this->render('about/career/job-openings.html.twig', [
            'template_layout_name' => 'layouts/layout-20250101.html.twig'
        ]);
    }

    public function indexAction(Request $request)
    {
        return $this->redirect('about/overview');
    }

    public function ourPartnersAction(Request $request)
    {
        $type = 'Our Partners';

        //        $list = new AboutServices();
        //        $data = $list->getData($type);
        $data = [];
        // return $this->render('about/ourPartners/index.html.twig',[
        //     'list' => $data
        // ]);
        return $this->render('about/ourPartners/index-20230718.html.twig', [
            'list' => $data,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    public function ourClientsAction(Request $request)
    {

        // return $this->render('about/ourClients/index.html.twig');
        return $this->render('about/ourClients/index-20230718.html.twig', [
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    public function teamAction(Request $request)
    {
        $dataObj = new DataObject\OurTeam\Listing();
        $dataObj->load();

        $list['Faculty'] = $dataObj->filterByTeamType('Faculty')->load();
        $list['Strategists'] = $dataObj->filterByTeamType('IP Strategists')->load();

        // return $this->render('about/team/index.html.twig',[
        //     'list' => $list
        // ]);
        return $this->render('about/team/index-20230718.html.twig', [
            'list' => $list,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    public function contactAction(Request $request)
    {
        $stateObj = new DataObject\NatureOfEnquire\Listing();

        $ob = $stateObj->getClass();

        $states = $ob->getFieldDefinition('state')->getOptions();


        // return $this->render('about/contact/contact-d.html.twig',[
        //     'states' => $states
        // ]);
        return $this->render('about/contact/contact-20230918b.html.twig', [
            'states' => $states,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    public function testTemplate(Request $request)
    {
        $stateObj = new DataObject\NatureOfEnquire\Listing();

        $ob = $stateObj->getClass();

        $states = $ob->getFieldDefinition('state')->getOptions();


        return $this->render('about/contact/contact.html.twig', [
            'states' => $states
        ]);
        // return $this->render('about/contact/contact-20230718.html.twig',[
        // 'states' => $states,
        //     'template_layout_name' => 'layouts/layout-20230718.html.twig'
        // ]);
    }


    /**
     * @Route("/about/careers/{careerTitle}{id}" ,name="craeer-detail", defaults={"path"=""},requirements={"id"="_\d+"})
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function careerDetailAction(Request $request, $id)
    {
        $id = trim($id, '_');
        $list = [];
        $obj = DataObject\Career::getById($id);
        if (!$obj) {
            return $this->redirect('/en/error-page/404');
        }
        if (!$obj->getPublished()) {
            return $this->redirect('/en/error-page/404');
        }
        $title = $request->attributes->get('careerTitle');
        $objTitle = $obj->get('key');

        if ($title != $objTitle) {
            return $this->redirect('/en/error-page/404');
        }
        if ($obj) {
            $list['title'] = $obj->getTitle();
            $list['releaseDate'] = $obj->getReleaseDate();
            $list['author'] = $obj->getAuthor();
            $list['authorIcon'] = $obj->getAuthorIcon();
            $list['coverImage'] = $obj->getCoverImage();
            $list['content'] = $obj->getContent();
            $list['videoTime'] = $obj->getVideoTime();
            $list['also'] = $obj->getAlsoList();
            $list['file'] = $obj->getFile();
            $list['video'] = $obj->getDetailVideo();
            $list['checkList'] = $obj->getCheckIndustry();
            $list['fullGuide'] = $obj->getFullGuide();
            /*$list['listType'] = $obj->getListType();*/
            /*$list['resourceType'] = $obj->getResourceType();*/
            $list['shares'] = $obj->getShares();
            $list['moreContent'] = $obj->getMoreContent();
            $list['interestedTitle'] = $obj->getInterestedTitle();
            $list['industryTitle'] = $obj->getIndustryTitle();
            $list['chineseGuide'] = $obj->getChineseGuide();
            $list['guideTitle'] = $obj->getGuideTitle();
            $list['releaseDate'] = $obj->getReleaseDate();
        }

        // return $this->render('about/career/detail.html.twig',[
        //     'list' => $list,
        //     'sharePage' => $request->getUri()
        // ]);

        return $this->render('about/career/detail-20250102.html.twig', [
            'list' => $list,
            'sharePage' => $request->getUri(),
            'template_layout_name' => 'layouts/layout-20250102.html.twig'
        ]);
        // return $this->render('about/career/detail-20230730.html.twig', [
        //     'list' => $list,
        //     'sharePage' => $request->getUri(),
        //     'template_layout_name' => 'layouts/layout-20230718.html.twig'
        // ]);
    }
}
