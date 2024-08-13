<?php

namespace App\Controller;


use App\Services\CourseServices;
use App\Services\CurlServices;
use Pimcore\Model\DataObject;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EventsController extends BaseController
{


    public function indexAction(Request $request)
    {

        return $this->redirect('news/news-announcements');
    }

    public function upcomingAction(Request $request)
    {

        $list = new DataObject\Events\Listing();
        $ob = $list->getClass();
        $ret['topic'] = $ob->getFieldDefinition("topic")->getOptions();
        $ret['cost'] = $ob->getFieldDefinition("cost")->getOptions();
        $ret['venue'] = $ob->getFieldDefinition("venue")->getOptions();
        $ret['eventType'] = $ob->getFieldDefinition("eventType")->getOptions();
        $ret['audience'] = $ob->getFieldDefinition("audience")->getOptions();
        $ret['proficiency'] = $ob->getFieldDefinition("proficiency")->getOptions();
        $list->load();

        // return $this->render('events/upcoming.html.twig',[
        //     'ret' => $ret
        // ]);
        return $this->render('events/upcoming-20230718.html.twig', [
            'ret' => $ret,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }


    /**
     * @route ("/api/getEventsPicker")
     * @return JsonResponse
     */
    public function getDatePickerAction(Request $request)
    {

        $filter = $request->request->get('filter');
        $filterData = json_decode($filter, true);

        if ($filterData) {
            $filter = json_encode($filter, 256);
            $filter = ',filter : ' . $filter;
        } else {
            $filter = '';
        }


        $url = $_ENV['APP_URL'] . '/pimcore-graphql-webservices/events?apikey=af857163a5b2a05be63753beda3813a4';

        $ar = [
            'query' => '{
  getEventsListing(defaultLanguage : "en"' . $filter . '){
    edges{
      node{
        id

        planing{
          ... on fieldcollection_ProgramPlanning{
            startDate
            lastDate

          }
        }
      }
    }
  }
}'
        ];


        $curl = new CurlServices();
        $data = $curl->posturl($url, $ar);

        $result = [];
        $nowDay = date('Y-m-d');
        foreach ($data['data']['getEventsListing']['edges'] as $k => $v) {
            $id = $v['node']['id'];
            foreach ($v['node']['planing'] as $value) {
                if ($value['startDate'] && date('Y-m-d', strtotime($value['startDate'])) > $nowDay) {
                    $result[] = [
                        'id' => $id,
                        'start' => $value['startDate'],
                        'last'  => $value['lastDate']
                    ];
                }
            }
        }

        $ret = array_unique(array_column($result, 'start'));

        return new JsonResponse($ret);
    }

    public function planning($obj)
    {

        $result = [];
        $nowDay = date('Y-m-d');
        foreach ($obj as $value) {

            $planning = [];
            if ($value->getStartDate()) {
                if ($nowDay >= date('Y-m-d', strtotime($value->getStartDate()))) {
                    continue;
                }
            }


            $planning = [
                'startDate' => $value->getStartDate() ? date('d M, Y', strtotime($value->getStartDate())) : '',
                'lastDate' => $value->getLastDate() ? date('d M, Y', strtotime($value->getLastDate())) : '',
                'datePlanning' => $value->getDatePlaning() ?? "",
            ];

            $timePlanningArr = [];
            if ($value->getTeachingArrangement()) {
                foreach ($value->getTeachingArrangement() as $timePlanning) {

                    $timePlanningArr[] = [
                        'startTime' => $timePlanning['startTime']->getData() ? date('h:i a', strtotime($timePlanning['startTime']->getData())) : '',
                        'lastTime' => $timePlanning['lastTime']->getData() ? date('h:i a', strtotime($timePlanning['lastTime']->getData())) : '',
                        'timePlanning' => $timePlanning['timePlanning'] ?  $timePlanning['timePlanning']->getData() : '',
                    ];
                }
            }

            $planning['teachingArrangement'] = $timePlanningArr;

            $result[] = $planning;
        }

        return $result;
    }

    /**
     * @Route ("/api/getEvents")
     * @param Request $request
     * @return JsonResponse
     */
    public function getEventsAction(Request $request)
    {

        $filter = $request->request->get('filter');
        $filterDate = $request->request->get('filterDate');

        $filter = json_decode($filter, true);

        $eventsObj = new DataObject\Events\Listing();

        if (isset($filter['eventType'])) {
            $eventsObj->filterByEventType($filter['eventType']);
        }
        if (isset($filter['topic'])) {
            $eventsObj->filterByTopic($filter['topic']);
        }
        if (isset($filter['venue'])) {
            $eventsObj->filterByVenue($filter['venue']);
        }
        if (isset($filter['cost'])) {
            $eventsObj->filterByCost($filter['cost']);
        }
        if (isset($filter['audience'])) {
            $eventsObj->filterByAudience($filter['audience']);
        }
        if (isset($filter['proficiency'])) {
            $eventsObj->filterByProficiency($filter['proficiency']);
        }
        $eventsObj->load();

        $eventsData = [];
        foreach ($eventsObj as $event) {
            $planningArr = $this->planning($event->getPlaning());
            if (empty($planningArr)) {
                continue;
            }

            $eventsData[$event->getId()] = [
                'id'    => $event->getId(),
                'title' => $event->getTitle(),
                'planningStr' => $planningArr,
                'planning' => $event->getPlaning(),
                'eventType' => $event->getEventType(),
                'fullpath'  => $event->getFullPath(),
                'venue' => $event->getVenue(),
                'venueText' => $event->getVenueText(),
                'coverImage' => $event->getCoverImage() ? $event->getCoverImage()->getThumbnail('UpcomingEventsList')->getPath() : '',
            ];

            /*    dump($event->getCoverImage()->getThumbnail('coverImg')
                ->getHtml(['imgAttributes' => ["class" => "cover"]]));
            exit();*/
        }
        $nowDate = date('Y-m-d');
        $conditions = [];
        foreach ($eventsData as $k => $v) {
            $id = $v['id'];
            if (isset($v['planning']) && !empty($v['planning'])) {
                foreach ($v['planning'] as $value) {
                    if ($value->getStartDate()) {

                        if ($nowDate >= date('Y-m-d', strtotime($value->getStartDate()))) {
                            continue;
                        }


                        array_push($conditions, [
                            'id' => $id,
                            'date' => strtotime($value->getStartDate())
                        ]);
                    }
                }
            }
        }

        $last_names = array_column($conditions, 'date');
        array_multisort($last_names, SORT_ASC, $conditions);
        $ids = array_unique(array_column($conditions, 'id'));


        $filterDateIds = [];
        if ($filterDate) {
            $filterDate = $filterDate / 1000;
            foreach ($eventsData as $k => $v) {
                $id = $v['id'];
                if (isset($v['planning']) && !empty($v['planning'])) {
                    foreach ($v['planning'] as $value) {

                        if (strtotime($value->getStartDate()) == $filterDate) {

                            array_push($filterDateIds, $id);
                        }
                    }
                }
            }
        }

        $result = [];
        if ($filterDate) {
            foreach ($ids as $id) {
                if (in_array($id, $filterDateIds)) {
                    $result[] = $eventsData[$id];
                }
            }
        } else {
            foreach ($ids as $id) {
                if (isset($eventsData[$id])) {
                    $result[] = $eventsData[$id];
                    unset($eventsData[$id]);
                }
            }
            $result = array_merge($result, $eventsData);
        }



        return new JsonResponse(
            [
                'total' => count($result),
                'data' => $result
            ]
        );
    }



    /**
     *
     * @Route("/events/upcoming-events/{event}{id}",requirements={"id"="_\d+"} )
     * @param Request $request
     */
    public function detailAction(Request $request, $id)
    {
        $id = trim($id, '_');
        $event = DataObject\Events::getById($id);
        if (!$event->getPublished()) {
            return $this->redirect('/en/error-page/404');
        }
        $title = $request->attributes->get('event');
        $objTitle = $event->get('key');

        if ($title != $objTitle) {
            return $this->redirect('/en/error-page/404');
        }

        $data = [
            'title' => $event->getTitle(),
            'content' => $event->getContent(),
            'planning' => $event->getPlaning(),
            'venue' => $event->getVenue(),
            'venueText' => $event->getVenueText(),
            'eventType' => $event->getEventType(),
            'paneList' => $event->getGuestsData(),
            'interestedList' => $event->getInterestedList(),
            'email' => $event->getEmail(),
            'registerUrl' => $event->getRegsiterUrl(),
            'seoTitle' => !empty($event->getSeoTitle()) ? $event->getSeoTitle() : $event->getTitle(),
            'seoDescription' => $event->getSeoDescription() ?? substr(strip_tags($event->getContent()), 0, 200),
            'tags' => $event->getTags(),
            'interestedTitle' => $event->getInterestedTitle()
        ];
        // return $this->render('events/detail.html.twig',[
        //     'detail' => $data
        // ]);
        return $this->render('events/detail-20230718.html.twig', [
            'detail' => $data,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    /**
     *
     * @Route("/news/news-announcements/{event}{id}",requirements={"id"="_\d+"} )
     * @param Request $request
     */
    public function newsDetailAction(Request $request, $id)
    {
        $id = trim($id, '_');
        $newData = DataObject\News::getById($id);
        if (!$newData->getPublished()) {
            return $this->redirect('/en/error-page/404');
        }
        $title = $request->attributes->get('event');
        $objTitle = $newData->get('key');

        if ($title != $objTitle) {
            return $this->redirect('/en/error-page/404');
        }

        $data = [
            'title' => $newData->getTitle(),
            'content' => $newData->getContent(),
            'author'  => $newData->getAuthor(),
            'authorIcon' => $newData->getAuthorImage(),
            'coverImage' => $newData->getCoverImage(),
            'interested' => $newData->getInterestedList(),
            'file'       => $newData->getFile(),
            'date'       => $newData->getReleaseDate(),
            'seoTitle' => $newData->getSeoTitle(),
            'seoDescription' => $newData->getSeoDescription(),
            'tags' => $newData->getTags(),
            'interestedTitle' => $newData->getInterestedTitle(),
            'video' => $newData->getDetailVideo() ? $newData->getDetailVideo() : '',
        ];

        // return $this->render('events/news-detail.html.twig',[
        //     'data' => $data
        // ]);
        return $this->render('events/news-detail-20230905c.html.twig', [
            'data' => $data,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    /**
     * @Route ("/api/news")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newsAnnoucementsAction(Request $request)
    {
        $dataType = $request->request->get('dataType');
        $num = $request->request->get('num');

        $news = new DataObject\News\Listing();
        $news->setOrderKey('releaseDate');
        $news->setOrder('DESC');

        if ($dataType == 'more') {
            $news->setOffset($num);
            $news->setLimit(6);
            $newsData   = $news->filterByCoverView(false)->load();

            $result = [];
            foreach ($newsData as $value) {
                $result[] = [
                    'title' => $value->getTitle(),
                    'date'  => $value->getReleaseDate(),
                    'id'    => $value->getId(),
                    'fullPath' => $value->getFullPath(),
                    'coverImage' => $value->getCoverImage() ? $value->getCoverImage()
                        ->getThumbnail('NewsAnnouncementsMore')
                        ->getPath() : ''
                ];
            }

            return new JsonResponse($result);
        }

        $news->filterByCoverView(true)->load();

        // return $this->render('events/news-annoucements.html.twig',[
        //     'list' => $news
        // ]);
        return $this->render('events/news-annoucements-20230718.html.twig', [
            'list' => $news,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }
}
