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

class CareerController extends BaseController
{

    public function indexAction(Request $request)
    {
        return $this->redirect('resources/overview');
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
        }

        // return $this->render('resources/business-guides/detail.html.twig',[
        //     'list' => $list,
        //     'sharePage' => $request->getUri()
        // ]);
        return $this->render('resources/business-guides/detail-20230718.html.twig', [
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

        ];

        // return $this->render('resources/case-studies/detail.html.twig',[
        //     'list' => $list,
        //     'sharePage' => $request->getUri()
        // ]);
        return $this->render('resources/case-studies/detail.html.twig', [
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


        $data['latest'] = $list->filterByLatest(true)->load();
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
     *
     * @Route("/resources/articles/{articleTile}{id}" ,name="article-detail", defaults={"path"=""},requirements={"id"="_\d+"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function articleDetailAction(Request $request, $id)
    {
        $id = trim($id, '_');
        $articles = DataObject\Articles::getById($id);

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
            'tags' => $articles->getTags(),
            'interestedTitle' => $articles->getInterestedTitle(),

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
        return $this->render('resources/articles/detail-20230718.html.twig', [
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
        return $this->render('resources/faqs/index-20230718.html.twig', [
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

        $list = [
            'title' => $patent->getTitle(),
            'releaseDate' => $patent->getReleaseDate(),
            'author' => $patent->getAuthor(),
            'authorIcon' => $patent->getAuthorIcon(),
            'coverImage' => $patent->getCoverImage(),
            'content' => $patent->getContent(),
            'interestedList' => $patent->getInterestedList(),
            'shares' => $patent->getShares(),
            'tags' => $patent->getTags(),
            'file' => $patent->getFile(),
            'interestedTitle' => $patent->getInterestedTitle(),
            'seoTitle' => $patent->getTitle(),
            'seoDescription' => substr(strip_tags($patent->getContent()), 0, 200)
        ];


        // return $this->render('resources/patentAnalyticReports/detail.html.twig',[
        //     'list' => $list,
        //     'sharePage' => $request->getUri()
        // ]);
        return $this->render('resources/patentAnalyticReports/detail-20230718.html.twig', [
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
            'tags' => $webinar->getTags(),
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
}
