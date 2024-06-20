<?php

namespace App\Controller;

use App\Services\ResourcesServices;
use App\Website\LinkGenerator\ArticleLinkGenerator;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use function Matrix\trace;

class ResourceController extends BaseController
{

    public function indexAction(Request $request)
    {
        return $this->redirect('resources/overview');
    }

    public function overviewAction(Request $request)
    {
        $data = [
            'articles' => (new DataObject\Articles\Listing())
                ->filterByCoverView(true)
                ->setOrderKey('releaseDate')
                ->setOrder('DESC')
                ->load(),
            'business' => (new DataObject\Business\Listing())
                ->filterByCoverView(true)
                ->setOrderKey('releaseDate')
                ->setOrder('DESC')
                ->load(),
            'patent' => (new DataObject\PatentAnalytic\Listing())
                ->filterByCoverView(true)
                ->setOrderKey('releaseDate')
                ->setOrder('DESC')
                ->load(),
            'studies' => (new DataObject\CaseStudy\Listing())
                ->filterByCoverView(true)
                ->setOrderKey('releaseDate')
                ->setOrder('DESC')
                ->load()

        ];

        $webinar = (new DataObject\WebinarRecordings\Listing());
        $webinar->filterByCoverView(true);
        $webinar->setOrderKey('releaseDate');
        $webinar->setOrder('DESC');
        $webinar->load();


        // return $this->render('resources/index/index.html.twig',[
        //     'list' => $data,
        //     'webinar' => $webinar
        // ]);
        return $this->render('resources/index/index-20230718.html.twig', [
            'list' => $data,
            'webinar' => $webinar,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }
    /**
     * @Route ("/api/business")
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function businessGuidesAction(Request $request)
    {

        $dataType = $request->request->get('dataType');
        $num = $request->request->get('num');

        $businessType = 'Business Guides';

        $list = new DataObject\Business\Listing();

        $list->setOrderKey('releaseDate');
        $list->setOrder('DESC');

        $ob = $list->getClass();
        $guidesType = $ob->getFieldDefinition("listType")->getOptions();
        $data['latest'] = $list->filterByLatest(true)->load();


        $data['watchCount'] = $list->filterByLatest(false)->filterByListType('Watch List')->getTotalCount();
        $data['readCount'] = $list->filterByLatest(false)->filterByListType('Read List')->getTotalCount();


        if ($dataType == 'watchList') {

            $list->filterByLatest(false)->filterByListType('Watch List');
            $list->setOffset($num);
            $list->setLimit(6);
            $watch   = $list->load();

            $result = [];
            foreach ($watch as $value) {
                $result[] = [
                    'type' => 'watchList',
                    'title' => $value->getTitle(),
                    'date'  => $value->getReleaseDate(),
                    'id'    => $value->getId(),
                    'fullPath' => $value->getFullPath(),
                    'videoTime' => $value->getVideoTime(),
                    'coverImage' => $value->getCoverImage() ? $value->getCoverImage()
                        ->getThumbnail('BusinessGuidesMore')
                        ->getPath() : ''
                ];
            }

            return new JsonResponse($result);
        } elseif ($dataType == 'readList') {
            $list->filterByLatest(false)->filterByListType('Read List');
            $list->setOffset($num);
            $list->setLimit(6);
            $read   = $list->load();

            $result = [];
            foreach ($read as $value) {
                $result[] = [
                    'type' => 'readList',
                    'title' => $value->getTitle(),
                    'date'  => $value->getReleaseDate(),
                    'id'    => $value->getId(),
                    'videoTime' => $value->getVideoTime(),
                    'fullPath' => $value->getFullPath(),
                    'coverImage' => $value->getCoverImage() ? $value->getCoverImage()
                        ->getThumbnail('BusinessGuidesMore')
                        ->getPath() : ''
                ];
            }

            return new JsonResponse($result);
        }


        /*dump($list->filterByLatest(false)->filterByListType('Watch List')->getTotalCount());
        dump($list->filterByLatest(false)->filterByListType('Read List')->getTotalCount());
        exit();*/



        $guidesTypeData = [];
        foreach ($guidesType as $k => $v) {
            $guidesTypeData[$v['key']] = $list->filterByListType($v['value'])->getTotalCount();
        }

        // return $this->render('resources/business-guides/index.html.twig',[
        //     'list' => $data
        // ]);
        return $this->render('resources/business-guides/index-20230718.html.twig', [
            'list' => $data,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }



    /**
     * @Route("/resources/business-guides/{articleTile}{id}" ,name="guides-detail", defaults={"path"=""},requirements={"id"="_\d+"})
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function businessDetailAction(Request $request, $id)
    {
        $id = trim($id, '_');
        $list = [];
        $obj = DataObject\Business::getById($id);

        if (!$obj->getPublished()) {
            return $this->redirect('/en/error-page/404');
        }
        $title = $request->attributes->get('articleTile');
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
            $list['listType'] = $obj->getListType();
            $list['resourceType'] = $obj->getResourceType();
            $list['shares'] = $obj->getShares();
            $list['moreContent'] = $obj->getMoreContent();
            $list['interestedTitle'] = $obj->getInterestedTitle();
            $list['industryTitle'] = $obj->getIndustryTitle();
            $list['chineseGuide'] = $obj->getChineseGuide();
            $list['guideTitle'] = $obj->getGuideTitle();
            $list['seoTitle'] = !empty($obj->getSeoTitle()) ? $obj->getSeoTitle() : $obj->getTitle();
            $list['seoDescription'] = $obj->getSeoDescription() ?? substr(strip_tags($obj->getContent()), 0, 200);
            $list['tags'] = $obj->getTags() ? implode(',', $obj->getTags()) : '';
        }

        // return $this->render('resources/business-guides/detail.html.twig',[
        //     'list' => $list,
        //     'sharePage' => $request->getUri()
        // ]);
        return $this->render('resources/business-guides/detail-20230731.html.twig', [
            'list' => $list,
            'sharePage' => $request->getUri(),
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }


    /**
     * @Route ("/api/case-studies")
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function caseStudiesAction(Request $request)
    {
        $dataType = $request->request->get('dataType');
        $num = $request->request->get('num');
        $resourceType = 'Case Studies';

        $list = new DataObject\CaseStudy\Listing();

        $list->setOrderKey('releaseDate');
        $list->setOrder('DESC');
        /*$list->load();*/
        $data['latest'] = $list->filterByLatest(true)->load();

        if ($dataType == 'more') {
            $list->setOffset($num);
            $list->setLimit(6);
            $studies   = $list->filterByLatest(false)->load();

            $result = [];
            foreach ($studies as $value) {
                $result[] = [
                    'title' => $value->getTitle(),
                    'date'  => $value->getReleaseDate(),
                    'id'    => $value->getId(),
                    'fullPath' => $value->getFullPath(),
                    'video' => $value->getDetailVideo() ? $value->getDetailVideo()->getData() : '',
                    'videoTime' => $value->getVideoTime(),
                    'coverImage' => $value->getCoverImage() ? $value->getCoverImage()
                        ->getThumbnail('CaseStudiesMore')->getPath() : ''
                ];
            }

            return new JsonResponse($result);
        }




        // return $this->render('resources/case-studies/index.html.twig',[
        //     'list'   => $data
        // ]);
        return $this->render('resources/case-studies/index-20230718.html.twig', [
            'list'   => $data,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }


    /**
     * @Route("/resources/case-studies/{guidTile}{id}" ,name="guid-detail", defaults={"path"=""},requirements={"id"="_\d+"})
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function caseDetailAction(Request $request, $id)
    {
        $id = trim($id, '_');
        $caseDetail = DataObject\CaseStudy::getById($id);
        if (!$caseDetail->getPublished()) {
            return $this->redirect('/en/error-page/404');
        }

        $title = $request->attributes->get('guidTile');
        $objTitle = $caseDetail->get('key');

        if ($title != $objTitle) {
            return $this->redirect('/en/error-page/404');
        }

        $list = [
            'title' => $caseDetail->getTitle(),
            'releaseDate' => $caseDetail->getReleaseDate(),
            'author' => $caseDetail->getAuthor(),
            'authorIcon' => $caseDetail->getAuthorIcon(),
            'video' => $caseDetail->getDetailVideo(),
            'videoTime' => $caseDetail->getVideoTime(),
            'coverImage' => $caseDetail->getCoverImage(),
            'content' => $caseDetail->getContent(),
            'interestedList' => $caseDetail->getInterestedList(),
            'shares' => $caseDetail->getShares(),
            'bookChat' => $caseDetail->getBookChat(),
            'tags' => $caseDetail->getTags(),
            'interestedTitle' => $caseDetail->getInterestedTitle(),
            'seoTitle' => $caseDetail->getSeoTitle(),
            'seoDescription' => $caseDetail->getSeoDescription(),


        ];

        // return $this->render('resources/case-studies/detail.html.twig',[
        //     'list' => $list,
        //     'sharePage' => $request->getUri()
        // ]);
        return $this->render('resources/case-studies/detail-20230718.html.twig', [
            'list' => $list,
            'sharePage' => $request->getUri(),
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    public function usefulResourcesAction(Request $request)
    {
        // return $this->render('resources/useful-resources/index.html.twig');
        return $this->render('resources/useful-resources/index-20230718.html.twig', [
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    /**
     * @Route ("/api/articles")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function articlesAction(Request $request, PaginatorInterface $paginator)
    {

        $type = $request->request->get('articleType');
        $num  = $request->request->get('num');
        $articlesType = 'Articles';

        $list = new DataObject\Articles\Listing();

        $list->setOrderKey('releaseDate');
        $list->setOrder('DESC');
        //$list->load();

        if ($type) {
            if ($type == 'All Topics') {
                $list->filterByLatest(false);
            } else {
                $list->filterByArticleType($type);
            }
            $list->filterByResourceType('Articles')->load();
            $list->setOffset($num);
            $articles = $list->setLimit(6)->filterByLatest(false)->load();


            $result = [];
            foreach ($articles as $value) {
                $result[] = [
                    'title' => $value->getTitle(),
                    'date'  => $value->getReleaseDate(),
                    'id'    => $value->getId(),
                    'fullPath' => $value->getFullPath(),
                    'coverImage' => $value->getCoverImage() ? $value->getCoverImage()
                        ->getThumbnail('ArticlesMore')
                        ->getPath() : '',
                    'test' => true,
                ];
            }

            return new JsonResponse($result);
        }


        $ob = $list->getClass();
        $articleType = $ob->getFieldDefinition("articleType")->getOptions();


        $data['latest'] = $list->filterByLatest(true)->filterByResourceType('Articles')->load();
        //$data['more']   = $list->filterByLatest(false)->load();
        $data['uri'] = $request->getUri();

        // return $this->render('resources/articles/index.html.twig',[
        //     'list' => $data,
        //     'articleType'  => $articleType
        // ]);
        return $this->render('resources/articles/index-20230718.html.twig', [
            'list' => $data,
            'articleType'  => $articleType,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }


    /**
     * @Route ("/api/ask-our-experts")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function askAction(Request $request, PaginatorInterface $paginator)
    {

        $type = $request->request->get('articleType');
        $num  = $request->request->get('num');
        $articlesType = 'Articles';

        $list = new DataObject\Articles\Listing();

        $list->setOrderKey('releaseDate');
        $list->setOrder('DESC');
        //$list->load();

        if ($type) {
            if ($type == 'All Topics') {
                $list->filterByLatest(false);
            } else {
                $list->filterByArticleType($type);
            }
            $list->filterByResourceType('Ask-Our-Experts');
            $list->setOffset($num);
            $articles = $list->setLimit(6)->filterByLatest(false)->load();


            $result = [];
            foreach ($articles as $value) {
                $result[] = [
                    'title' => $value->getTitle(),
                    'date'  => $value->getReleaseDate(),
                    'id'    => $value->getId(),
                    'fullPath' => $value->getFullPath(),
                    'coverImage' => $value->getCoverImage() ? $value->getCoverImage()
                        ->getThumbnail('ArticlesMore')
                        ->getPath() : ''
                ];
            }

            return new JsonResponse($result);
        }


        $ob = $list->getClass();
        $articleType = $ob->getFieldDefinition("articleType")->getOptions();


        $data['latest'] = $list->filterByLatest(true)->filterByResourceType('Ask-Our-Experts')->load();
        //$data['more']   = $list->filterByLatest(false)->load();
        $data['uri'] = $request->getUri();

        // return $this->render('resources/ask-our-experts/index.html.twig',[
        //     'list' => $data,
        //     'articleType'  => $articleType
        // ]);
        return $this->render('resources/ask-our-experts/index-20230718.html.twig', [
            'list' => $data,
            'articleType'  => $articleType,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    /**
     *
     * @Route("/resources/articles/{articleTile}{id}" ,name="article-detail", defaults={"path"=""},requirements={"id"="_\d+"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function articleDetailAction(Request $request, $id)
    {
        $id = trim($id, '_');
        $articles = DataObject\Articles::getById($id);
        if (!$articles->getPublished()) {
            return $this->redirect('/en/error-page/404');
        }

        $title = $request->attributes->get('articleTile');
        $objTitle = $articles->get('key');

        if ($title != $objTitle) {
            return $this->redirect('/en/error-page/404');
        }


        $list = [
            'title' => $articles->getTitle(),
            'releaseDate' => $articles->getReleaseDate(),
            'author' => $articles->getAuthor(),
            'authorIcon' => $articles->getAuthorIcon(),
            'coverImage' => $articles->getCoverImage(),
            'content' => $articles->getContent(),
            'relatedArticles' => $articles->getRelatedArticles(),
            'shares' => $articles->getShares(),
            'bookChat' => $articles->getBookChat(),
            'tags' => $articles->getTags() ? implode(',', $articles->getTags()) : "",
            'seoTitle' => $articles->getSeoTitle(),
            'seoDescription' => $articles->getSeoDescription(),
            'interestedTitle' => $articles->getInterestedTitle(),
            'video' => $articles->getDetailVideo() ? $articles->getDetailVideo() : '',

        ];
        /*$list['title'] = $obj->getTitle();
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
        $list['listType'] = $obj->getListType();
        $list['resourceType'] = $obj->getResourceType();
        $list['shares'] = $obj->getShares();*/

        // return $this->render('resources/articles/detail.html.twig',[
        //     'list' => $list,
        //     'sharePage' => $request->getUri()
        // ]);
        return $this->render('resources/articles/detail-20230731.html.twig', [
            'list' => $list,
            'sharePage' => $request->getUri(),
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }


    /**
     *
     * @Route("/resources/ask-our-experts/{articleTile}{id}" ,name="ask-detail", defaults={"path"=""},requirements={"id"="_\d+"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function askDetailAction(Request $request, $id)
    {
        $id = trim($id, '_');
        $articles = DataObject\Articles::getById($id);
        if (!$articles->getPublished()) {
            return $this->redirect('/en/error-page/404');
        }

        $title = $request->attributes->get('articleTile');
        $objTitle = $articles->get('key');
        /*var_dump($objTitle);
	var_dump($title);
	exit();*/
        if ($title != $objTitle) {
            return $this->redirect('/en/error-page/404');
        }

        $list = [
            'title' => $articles->getTitle(),
            'releaseDate' => $articles->getReleaseDate(),
            'author' => $articles->getAuthor(),
            'authorIcon' => $articles->getAuthorIcon(),
            'coverImage' => $articles->getCoverImage(),
            'content' => $articles->getContent(),
            'relatedArticles' => $articles->getRelatedArticles(),
            'shares' => $articles->getShares(),
            'bookChat' => $articles->getBookChat(),
            'tags' => $articles->getTags() ? implode(',', $articles->getTags()) : "",
            'seoTitle' => $articles->getSeoTitle(),
            'seoDescription' => $articles->getSeoDescription(),
            'interestedTitle' => $articles->getInterestedTitle(),
            'video' => $articles->getDetailVideo() ? $articles->getDetailVideo() : '',
        ];

        // return $this->render('resources/ask-our-experts/detail.html.twig',[
        //     'list' => $list,
        //     'sharePage' => $request->getUri()
        // ]);
        return $this->render('resources/ask-our-experts/detail-20230731.html.twig', [
            'list' => $list,
            'sharePage' => $request->getUri(),
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    public function faqsAction(Request $request)
    {
        $list = new DataObject\Faqs\Listing();

        $ob = $list->getClass();
        $faqType = $ob->getFieldDefinition("questionType")->getOptions();

        $list->load();

        // return $this->render('resources/faqs/index.html.twig',[
        //     'list' => $list,
        //     'faqType' => $faqType
        // ]);
        return $this->render('resources/faqs/index-20231130a.html.twig', [
            'list' => $list,
            'faqType' => $faqType,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    /**
     * @Route ("/api/patents")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function patentAnalyticReportsAction(Request $request)
    {
        $resourceType = 'Patent Analytic Reports';

        $dataType = $request->request->get('dataType');
        $num = $request->request->get('num');

        $list = new DataObject\PatentAnalytic\Listing();

        $list->setOrderKey('releaseDate');
        $list->setOrder('DESC');

        $data['latest'] = $list->filterByLatest(true)->load();

        if ($dataType == 'more') {
            $list->setOffset($num);
            $list->setLimit(6);
            $patents = $list->filterByLatest(false)->load();

            $result = [];
            foreach ($patents as $value) {

                $result[] = [
                    'title' => $value->getTitle(),
                    'date'  => $value->getReleaseDate(),
                    'id'    => $value->getId(),
                    'fullPath' => $value->getFullPath(),

                    'coverImage' => $value->getCoverImage() ? $value->getCoverImage()
                        ->getThumbnail('coverImg')
                        ->getPath() : ''
                ];
            }

            return new JsonResponse($result);
        }

        // return $this->render('resources/patentAnalyticReports/index.html.twig',[
        //     'list'   => $data
        // ]);
        return $this->render('resources/patentAnalyticReports/index-20230718.html.twig', [
            'list'   => $data,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    /**
     * @Route ("/resources/patent-analytics-report/{articleTile}{id}",requirements={"id"="_\d+"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function patentDetailAction(Request $request, $id)
    {
        $id = trim($id, '_');
        $patent = DataObject\PatentAnalytic::getById($id);
        if (!$patent->getPublished()) {
            return $this->redirect('/en/error-page/404');
        }
        $title = $request->attributes->get('articleTile');
        $objTitle = $patent->get('key');

        if ($title != $objTitle) {
            return $this->redirect('/en/error-page/404');
        }

        $list = [
            'title' => $patent->getTitle(),
            'releaseDate' => $patent->getReleaseDate(),
            'author' => $patent->getAuthor(),
            'authorIcon' => $patent->getAuthorIcon(),
            'coverImage' => $patent->getCoverImage(),
            'content' => $patent->getContent(),
            'interestedList' => $patent->getInterestedList(),
            'shares' => $patent->getShares(),
            'tags' => $patent->getTags() ? implode(',', $patent->getTags()) : '',
            'seoTitle' => $patent->getSeoTitle(),
            'seoDescription' => $patent->getSeoDescription(),
            'file' => $patent->getFile(),
            'interestedTitle' => $patent->getInterestedTitle(),
            'video' => $patent->getDetailVideo() ? $patent->getDetailVideo() : '',
        ];


        // return $this->render('resources/patentAnalyticReports/detail.html.twig',[
        //     'list' => $list,
        //     'sharePage' => $request->getUri()
        // ]);
        return $this->render('resources/patentAnalyticReports/detail-20230731.html.twig', [
            'list' => $list,
            'sharePage' => $request->getUri(),
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    /**
     * @Route ("api/webinar")
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function webinarRecordingsAction(Request $request)
    {
        $articlesType = 'Webinar Recordings';

        $dataType = $request->request->get('dataType');
        $num = $request->request->get('num');

        $list = new DataObject\WebinarRecordings\Listing();
        $list->setOrderKey('releaseDate');
        $list->setOrder('DESC');

        $data['latest'] = $list->filterByLatest(true)->load();

        if ($dataType == 'more') {

            $list->setOffset($num);
            $list->setLimit(6);
            $webinar = $list->filterByLatest(false)->load();

            $result = [];
            foreach ($webinar as $value) {

                $result[] = [
                    'title' => $value->getTitle(),
                    'date'  => $value->getReleaseDate(),
                    'id'    => $value->getId(),
                    'videoTime' => $value->getVideoTime(),
                    'video' => $value->getDetailVideo() ? $value->getDetailVideo()->getData() : '',
                    'fullPath' => $value->getFullPath(),
                    'coverImage' => $value->getCoverImage() ? $value->getCoverImage()
                        ->getThumbnail('WebinarRecordingsMore')->getPath() : ''
                ];
            }

            return new JsonResponse($result);
        }

        // return $this->render('resources/webinar-recordings/index.html.twig',[
        //     'list' => $data,

        // ]);
        return $this->render('resources/webinar-recordings/index-20230718.html.twig', [
            'list' => $data,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    /**
     * @Route("/resources/webinar-recordings/{webinarTile}{id}" ,name="patent-detail", defaults={"path"=""},requirements={"id"="_\d+"})
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function webinarDetailAction(Request $request, $id)
    {
        $id = trim($id, '_');
        $webinar = DataObject\WebinarRecordings::getById($id);
        if (!$webinar->getPublished()) {
            return $this->redirect('/en/error-page/404');
        }
        $title = $request->attributes->get('webinarTile');
        $objTitle = $webinar->get('key');

        if ($title != $objTitle) {
            return $this->redirect('/en/error-page/404');
        }

        $list = [
            'title' => $webinar->getTitle(),
            'releaseDate' => $webinar->getReleaseDate(),
            'author' => $webinar->getAuthor(),
            'authorIcon' => $webinar->getAuthorIcon(),
            'content' => $webinar->getContent(),
            'coverImage' => $webinar->getCoverImage(),
            'video'   => $webinar->getDetailVideo(),
            'videoTime' => $webinar->getVideoTime(),
            'interestedList' => $webinar->getInterestedList(),
            'full'  => $webinar->getFullGuide(),
            'shares' => $webinar->getShares(),
            'tags' => $webinar->getTags() ? implode(',', $webinar->getTags()) : '',
            'seoTitle' => $webinar->getSeoTitle(),
            'seoDescription' => $webinar->getSeoDescription(),
            'file' => $webinar->getFile(),
            'interestedTitle' => $webinar->getInterestedTitle(),
        ];

        // return $this->render('resources/webinar-recordings/detail.html.twig',[
        //     'list' => $list,
        //     'sharePage' => $request->getUri()
        // ]);
        return $this->render('resources/webinar-recordings/detail-20230718.html.twig', [
            'list' => $list,
            'sharePage' => $request->getUri(),
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }


    public function smesAction(Request $request)
    {
        /*$resourceType = 'Search Made Easy for SMEs';

        $list = (new ResourcesServices())->getResources($resourceType);*/

        // return $this->render('resources/search-made-easy-for-smes/index.html.twig');
        return $this->render('resources/search-made-easy-for-smes/index-20230718.html.twig', [
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    public function techAction(Request $request)
    {
        return $this->render('resources/tech-insights-through-patents/index.html.twig', [
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }
}
