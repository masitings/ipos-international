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

class AcademyController extends BaseController
{

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
            foreach ($coursesData as $k => $v) {
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
        $courseDemand = new DataObject\Course\Listing();

        $courseDemand->load();

        $data = [
            'professionals' => [],
            'officers' => [],
            'enterprises' => [],
            'studies' => [],
        ];
        foreach ($courseDemand as $item) {

            if (count($data['professionals']) < 3) {
                if ($item->getAcademyType() == 'IP Professionals') {
                    $data['professionals'][] = $item;
                }
            }

            if (count($data['officers']) < 3) {
                if ($item->getAcademyType() == 'Public Agencies / Officers') {
                    $data['officers'][] = $item;
                }
            }

            if (count($data['enterprises']) < 3) {
                if ($item->getAcademyType() == 'Enterprises / Individuals') {
                    $data['enterprises'][] = $item;
                }
            }

            if (count($data['studies']) < 3) {
                if ($item->getAcademyType() == 'Graduate Studies') {
                    $data['studies'][] = $item;
                }
            }
        }

        // return $this->render('academy/overview.html.twig',[
        //     'list' => $data
        // ]);
        return $this->render('academy/overview-20230731b.html.twig', [
            'list' => $data,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }



    public function enterpriseAction(Request $request)
    {

        $academyType = 'Enterprises / Individuals';
        $result = (new CourseServices())->getCurses($academyType);
        $result['academyType'] = $academyType;
        // return $this->render('academy/courses.html.twig',[
        //     'ret' => $result
        // ]);
        return $this->render('academy/courses-20231213.html.twig', [
            'ret' => $result,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }


    /**
     * @route ("/api/getPicker")
     * @return mixed
     */
    public function getDatePickerAction(Request $request)
    {

        $filter = $request->request->get('filter');

        $filter = json_encode($filter, 256);
        $url = 'https://stage-v2.iposinternational.com/pimcore-graphql-webservices/academy?apikey=5a89ba4bda8d412501814dee4e6cbaf5';
        $str = '{  getCourseListing(defaultLanguage: "en", filter: ' . $filter . ') {    edges {      node {        id planing {          ... on fieldcollection_ProgramPlanning {            startDate            lastDate          }        }      }    }  }}';

        $ar = [
            'query' => $str
        ];
        $curl = new CurlServices();
        $data = $curl->posturl($url, $ar);

        $nowDate = date('Y-m-d');
        $result = [];

        return $data;
        die();

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

        $academyType = 'IP Professionals';
        $result = (new CourseServices())->getCurses($academyType);
        $result['academyType'] = $academyType;
        // return $this->render('academy/courses.html.twig',[
        //     'ret' => $result
        // ]);
        return $this->render('academy/courses-20231213.html.twig', [
            'ret' => $result,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }

    public function publicAgenciesAction(Request $request)
    {

        $academyType = 'Public Agencies / Officers';
        $result = (new CourseServices())->getCurses($academyType);
        $result['academyType'] = $academyType;
        // return $this->render('academy/courses.html.twig',[
        //     'ret' => $result
        // ]);
        return $this->render('academy/courses-20231213.html.twig', [

            'ret' => $result,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }


    public function graduateStudiesAction(Request $request)
    {

        $academyType = 'Graduate Studies';
        $result = (new CourseServices())->getCurses($academyType);
        $result['academyType'] = $academyType;
        // return $this->render('academy/courses.html.twig',[
        //     'ret' => $result
        // ]);
        return $this->render('academy/courses-20231213.html.twig', [
            'ret' => $result,
            'template_layout_name' => 'layouts/layout-20230718.html.twig'
        ]);
    }
}
