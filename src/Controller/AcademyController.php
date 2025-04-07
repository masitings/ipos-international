<?php

namespace App\Controller;

use App\Services\CourseServices;
use App\Services\CurlServices;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Navigation\Container;
use Pimcore\Twig\Extension\Templating\Placeholder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class AcademyController extends BaseController
{
    public function listAction(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 6);
        $search = $request->get('search', '');
        $type = $request->get('type', '');
        $level = $request->get('level', '');
        $topic = $request->get('topic', '');
        $programme = $request->get('programme', '');
        
        $list = new DataObject\Course\Listing();
        
        // Apply search filter (on title and content)
        if (!empty($search)) {
            $list->addConditionParam("(title LIKE ? OR Content LIKE ?)", ["%$search%", "%$search%"]);
        }
        
        // Apply other filters
        if (!empty($type)) {
            $list->filterByEventType($type);
        }
        
        if (!empty($level)) {
            $list->filterByLevel($level);
        }
        
        if (!empty($topic)) {
            $list->filterByTopic($topic);
        }
        // dd($programme);
        if (!empty($programme)) {
            $list->filterByAcademyType($programme);
        }
        
        // Calculate pagination
        $list->setLimit($limit);
        $list->setOffset(($page - 1) * $limit);
        
        // Get total count before loading data
        $totalCount = $list->getTotalCount();
        
        // Load the data
        $courses = $list->load();
        $db = \Pimcore\Db::get();
        $queryBuilder = $list->getQueryBuilder();
        $sql = $queryBuilder->getSQL();
        $params = $queryBuilder->getParameters();
        // dd($sql);
        // Format the data for the view
        $formattedCourses = [];
        foreach ($courses as $course) {
            $formattedCourses[] = [
                'id' => $course->getId(),
                'title' => $course->getTitle(),
                'eventType' => $course->getEventType(),
                'level' => $course->getLevel(),
                'topic' => $course->getTopic(),
                'academyType' => $course->getAcademyType(),
                'coverImage' => $course->getCoverImage() ? $course->getCoverImage()->getThumbnail()->getPath() : '',
                'viewUrl' => $course->getViewUrl(),
                'planing' => $course->getPlaning(),
                'baseData' => $course
                // Add other fields as needed
            ];
        }
        return $this->render('academy/new/academy-list.html.twig', [
            'template_layout_name' => 'academy/new/layouts/layout-20250327.html.twig',
            'courses' => $formattedCourses,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => ceil($totalCount / $limit),
                'totalItems' => $totalCount,
                'limit' => $limit
            ],
            'filters' => [
                'search' => $search,
                'type' => $type,
                'level' => $level,
                'topic' => $topic,
                'programme' => $programme
            ]
        ]);
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
                        'venue' => $timePlanning['venue']->getData(),
                        'venueText' => $timePlanning['venueText']->getData(),
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
     * @Route ("/api/getCourses")
     * @param Request $request
     * @return JsonResponse
     */
    public function getCoursesAction(Request $request)
    {

        $filter = $request->request->get('filter');
        $filterDate = $request->request->get('filterDate');

        $filter = json_decode($filter, true);


        $coursesObj = new DataObject\Course\Listing();
        if (isset($filter['academyType'])) {
            $coursesObj->filterByAcademyType($filter['academyType']);
        }
        if (isset($filter['eventType'])) {
            $coursesObj->filterByEventType($filter['eventType']);
        }
        if (isset($filter['topic'])) {
            $coursesObj->filterByTopic($filter['topic']);
        }
        if (isset($filter['level'])) {
            $coursesObj->filterByLevel($filter['level']);
        }

        if (isset($filter['fee'])) {
            $coursesObj->filterByFee($filter['fee']);
        }
        $coursesObj->load();

        $coursesData = [];
        foreach ($coursesObj as $course) {
            $planningArr = $this->planning($course->getPlaning());
            if (empty($planningArr)) {
                continue;
            }
            $coursesData[$course->getId()] = [
                'id'    => $course->getId(),
                'title' => $course->getTitle(),
                'planningStr' => $planningArr,
                'planning' => $course->getPlaning(),
                'level' => $course->getLevel(),
                'venue' => $course->getVenue(),
                'venueText' => $course->getVenueText(),
                'eventType' => $course->getEventType(),
                'fullpath'  => $course->getFullPath(),
                /*'video'    => $course->getVideo(),*/
                /*'pendant'  => $course->getTextData(),*/
                'viewUrl'  => $course->getViewUrl(),
                'coverImage' => $course->getCoverImage() ? $course->getCoverImage()->getThumbnail()->getPath() : '',
            ];
        }


        $nowDate = date('Y-m-d');
        $conditions = [];
        foreach ($coursesData as $k => $v) {
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
            $arr = [];
            foreach ($coursesData as $k => $v) {
                $id = $v['id'];
                if (isset($v['planning']) && !empty($v['planning'])) {
                    foreach ($v['planning'] as $value) {
                        if (date('Y-m-d', strtotime($value->getStartDate())) == date('Y-m-d', $filterDate)) {
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
                    $result[] = $coursesData[$id];
                }
            }
        } else {
            foreach ($ids as $id) {
                if (isset($coursesData[$id])) {
                    $result[] = $coursesData[$id];
                    unset($coursesData[$id]);
                }
            }
            $result = array_merge($result, $coursesData);
        }

        return new JsonResponse([
            'total' => count($result),
            'data' => $result
        ]);
    }
    public function indexAction(Request $request)
    {

        return $this->redirect('academy/overview');
    }

    /**
     *
     * @Route("/academy/{menuType}/{course}{id}",requirements={"id"="_\d+"} )
     * @param Request $request
     */
    public function couserDetailAction(Request $request, $menuType, $id)
    {
        $id = trim($id, '_');
        //dump(Document::getById(1)->getFullPath());
        $event = DataObject\Course::getById($id);
        if (!$event->getPublished()) {
            return $this->redirect('/en/error-page/404');
        }
        $title = $request->attributes->get('course');
        $objTitle = $event->get('key');

        if ($title != $objTitle) {
            return $this->redirect('/en/error-page/404');
        }

        $data = [
            'title' => $event->getTitle(),
            'content' => $event->getContent(),
            'planning' => $event->getPlaning(),
            'level' => $event->getLevel(),
            'venue' => $event->getVenue(),
            'venueText' => $event->getVenueText(),
            'eventType' => $event->getEventType(),
            'program' => $event->getProgrammeDetails(),
            'courseFee' => $event->getCourseFeesData(),
            'contacts' => $event->getContact(),
            'objects'  => $event->getLerningObjects(),
            'crowds'   => $event->getCrowdData(),
            'manual'   => $event->getManual(),
            'comments' => $event->getComments(),
            'video'    => $event->getVideo(),
            'videoTitle' => $event->getVideoTitle(),
            'register' => $event->getRegisterLinks(),
            'speaker'  => $event->getSpeakerData(),
            'interestList' => $event->getInterestedList(),
            'pendant'  => $event->getTextData(),
            'viewUrl'  => $event->getViewUrl(),
            'coverImage' => $event->getCoverImage(),/* ? $event->getCoverImage()->getThumbnail(),*/
            'backGround' => $event->getBackground(),/* ? $event->getBackground()->getThumbnail(),*/
            'academyType' => $event->getAcademyType(),
            'partner'    => $event->getLogos(),
            'interestedTitle' => $event->getInterestedTitle(),
            'seoTitle' => !empty($event->getSeoTitle()) ? $event->getSeoTitle() : $event->getTitle(),
            'seoDescription' => $event->getSeoDescription() ?? substr(strip_tags($event->getContent()), 0, 200),
            'tags'  => $event->getTags() ?  implode(',', $event->getTags()) : '',
            'interestedRegister' => $event->getInterestedRegister(),
            'urlType'    => $menuType,
            'otherInfo'    => $event->getotherInfo(),
            /*'paneList' => $event->get(),
            'interestedList' => $event->getInterestedList(),*/
        ];

        if (isset($_GET['debg'])) {
            // var_dump($data);
            echo "<pre>";
            // print_r($data);
            print_r($data['backGround']);
            echo "</pre>";
            // die();
            // return "";
        }
        // return $this->render('academy/detail.html.twig',[
        //     'detail' => $data,
        //     /* 'bread'  => $bread*/
        // ]);
        return $this->render('academy/detail-20230918.html.twig', [
            'detail' => $data,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function overviewAction(Request $request)
    {
        
        // $courseDemand = new DataObject\Course\Listing();

        // $courseDemand->load();

        // $data = [
        //     'professionals' => [],
        //     'officers' => [],
        //     'enterprises' => [],
        //     'studies' => [],
        // ];
        // foreach ($courseDemand as $item) {

        //     if (count($data['professionals']) < 3) {
        //         if ($item->getAcademyType() == 'IP Professionals') {
        //             $data['professionals'][] = $item;
        //         }
        //     }

        //     if (count($data['officers']) < 3) {
        //         if ($item->getAcademyType() == 'Public Agencies / Officers') {
        //             $data['officers'][] = $item;
        //         }
        //     }

        //     if (count($data['enterprises']) < 3) {
        //         if ($item->getAcademyType() == 'Enterprises / Individuals') {
        //             $data['enterprises'][] = $item;
        //         }
        //     }

        //     if (count($data['studies']) < 3) {
        //         if ($item->getAcademyType() == 'Graduate Studies') {
        //             $data['studies'][] = $item;
        //         }
        //     }
        // }

        // // return $this->render('academy/overview.html.twig',[
        // //     'list' => $data
        // // ]);
        return $this->render('academy/new/academy-overview.html.twig', [
            'template_layout_name' => 'academy/new/layouts/layout-20250325.html.twig'
        ]);
    }



    public function enterpriseAction(Request $request)
    {
        return $this->redirect('/en/academy/programmes?programme=Enterprises%20%2F%20Individuals');
    }
    // {

    //     $academyType = 'Enterprises / Individuals';
    //     $result = (new CourseServices())->getCurses($academyType);
    //     $result['academyType'] = $academyType;
    //     // return $this->render('academy/courses.html.twig',[
    //     //     'ret' => $result
    //     // ]);
    //     return $this->render('academy/courses-20231213.html.twig', [
    //         'ret' => $result,
    //         'template_layout_name' => 'layouts/layout-20230718.html.twig'
    //     ]);
    // }


    /**
     * @route ("/api/getPicker")
     * @return JsonResponse
     */
    public function getDatePickerAction(Request $request)
    {

        $filter = $request->request->get('filter');

        $filter = json_encode($filter, 256);

        $url = $_ENV['APP_URL'] . '/pimcore-graphql-webservices/academy?apikey=5a89ba4bda8d412501814dee4e6cbaf5';
        $str = '{  getCourseListing(defaultLanguage: "en", filter: ' . $filter . ') {    edges {      node {        id planing {          ... on fieldcollection_ProgramPlanning {            startDate            lastDate          }        }      }    }  }}';

        $ar = [
            'query' => $str
        ];
        $curl = new CurlServices();
        $data = $curl->posturl($url, $ar);

        $nowDate = date('Y-m-d');
        $result = [];

        foreach ($data['data']['getCourseListing']['edges'] as $k => $v) {
            $id = $v['node']['id'];
            if (isset($v['node']['planing']) && !empty($v['node']['planing'])) {
                foreach ($v['node']['planing'] as $value) {
                    if ($value['startDate']) {
                        if ($nowDate < date('Y-m-d', strtotime($value['startDate']))) {
                            $result[] = [
                                'id' => $id,
                                'start' => $value['startDate'],
                                'last'  => $value['lastDate']
                            ];
                        }
                    }
                }
            }
        }

        $ret = array_unique(array_column($result, 'start'));
        return new JsonResponse($ret);
    }


    public function ipProfessionalAction(Request $request)
    {
        return $this->redirect('/en/academy/programmes?programme=IP+Professionals');
    }
    // {

    //     $academyType = 'IP Professionals';
    //     $result = (new CourseServices())->getCurses($academyType);
    //     $result['academyType'] = $academyType;
    //     // return $this->render('academy/courses.html.twig',[
    //     //     'ret' => $result
    //     // ]);
    //     return $this->render('academy/courses-20231213.html.twig', [
    //         'ret' => $result,
    //         'template_layout_name' => 'layouts/layout-20230718.html.twig'
    //     ]);
    // }

    public function publicAgenciesAction(Request $request)
    {
        return $this->redirect('/en/academy/programmes?programme=Public%20Agencies%20%2F%20Officers');
    }
    // {

    //     $academyType = 'Public Agencies / Officers';
    //     $result = (new CourseServices())->getCurses($academyType);
    //     $result['academyType'] = $academyType;
    //     // return $this->render('academy/courses.html.twig',[
    //     //     'ret' => $result
    //     // ]);
    //     return $this->render('academy/courses-20231213.html.twig', [

    //         'ret' => $result,
    //         'template_layout_name' => 'layouts/layout-20230718.html.twig'
    //     ]);
    // }


    public function graduateStudiesAction(Request $request)
    {
        return $this->redirect('/en/academy/programmes?programme=Graduate%20Studies');
    }
    // {

    //     $academyType = 'Graduate Studies';
    //     $result = (new CourseServices())->getCurses($academyType);
    //     $result['academyType'] = $academyType;
    //     // return $this->render('academy/courses.html.twig',[
    //     //     'ret' => $result
    //     // ]);
    //     return $this->render('academy/courses-20231213.html.twig', [
    //         'ret' => $result,
    //         'template_layout_name' => 'layouts/layout-20230718.html.twig'
    //     ]);
    // }
}
