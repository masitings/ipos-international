<?php

namespace App\Controller;


use Container6Kjapc5\getCheckConsumerPermissionsServiceService;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Bundle\AdminBundle\Controller\Traits\AdminStyleTrait;
use Pimcore\Bundle\AdminBundle\Helper\GridHelperService;
use Pimcore\Config;
use Pimcore\Event\Admin\ElementAdminStyleEvent;
use Pimcore\Event\AdminEvents;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Element;
use Pimcore\Model\Search\Backend\Data;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SerachController extends BaseController{
    use AdminStyleTrait;


    /**
     * @Route("/api/quicksearch")
     *
     * @param Request $request
     * @param EventDispatcherInterface $eventDispatcher
     * @param Config $config
     *
     * @return JsonResponse
     */
    public function quicksearchAction(Request $request, PaginatorInterface $paginator,EventDispatcherInterface $eventDispatcher, Config $config)
    {
	try{
	
	    $query = $keyWord = addslashes($request->get('query'));
            if (!$query){
                // return  $this->render('search/search-result.html.twig',[]);
                return $this->render('search/search-result-20230719.html.twig',[
                    'template_layout_name' => 'layouts/layout-20230718.html.twig'
                ]);
            }
            $limit = is_numeric($request->get('limit')) ? intval($request->get('limit')) : 10;
            $page = is_numeric($request->get('page')) ? intval($request->get('page')) : 1 ;
            $offset = ($page - 1) * $limit;


            $filter = "COUNT(id) AS total";

            $querySql = "SELECT $filter FROM search_backend_data WHERE data LIKE '%{$query}%'
AND (type = 'object' OR type = 'page') AND  subtype != 'HomePage' AND subtype != 'Emails' AND subtype != 'BookChar' AND subtype != 'Shares' AND subtype != 'IposContact' AND published =1
ORDER BY creationDate DESC" ;

            $conn = $this->getDoctrine()->getConnection();

	    $total = $conn->fetchAll($querySql)[0]['total'];
	    
            /* dump($total);
             exit();*/


            $querySql = "SELECT * FROM search_backend_data WHERE data LIKE '%{$query}%'
AND (type = 'object' OR type = 'page') AND subtype != 'HomePage' AND subtype != 'Emails' AND subtype != 'BookChar' AND  subtype !='IposContact' AND subtype != 'Shares' AND published =1
ORDER BY creationDate DESC" ;
            
            $resultData = $conn->fetchAll($querySql." LIMIT ".$limit."
                    OFFSET ".$offset);
            $result = [];
            foreach ($resultData as  $key => $item){

                $maintype = $item['maintype'];
                $type = $item['type'];
                $className = $item['subtype'];

                if ($type == 'page'){

                    $pathArray = explode('/',$item['fullpath']);
                    $result[] = [
                        'id' => $item['id'],
                        'type' => $type,
                        'keyName' => end($pathArray),
                        'fullpath' => $item['fullpath'],
                        'coverImage' => ''
                    ];
                }else{
                    $element = Element\Service::getElementById($item['type'], $item['id']);

                    $result[] = [
                        'id' => $element->getId(),
                        'keyName' => method_exists($element,'getTitle') ? $element->getTitle() : $element->getKey(),
                        'fullpath' => $element->getFullPath().'_'.$element->getId(),
                        'type' => $type,

                    ];

                    if ($className == 'ClientsAndPartners'){

                        $result[$key]['coverImage'] = $element->getLogImage();

                        $result[$key]['fullpath'] = strstr('http://',$element->getWebsiteUrl()) ? $element->getWebsiteUrl() : 'http://'.$element->getWebsiteUrl();
                        //dump($element->getWebsiteUrl());
                        // dump($element);

                    }elseif (in_array($className,['CoursesDemand','faqs'])){
                        $result[$key]['coverImage'] = '';
                        if ($className == 'faqs'){
                            $result[$key]['fullpath'] = '/en/resources/faqs';
                        }
                    }elseif ($className == 'OurTeam'){
                        $result[$key]['coverImage'] = $element->getProfilePhoto();
                        $result[$key]['fullpath'] = '/en/about/our-team';
                    }else{
                        $result[$key]['coverImage'] = $element->getCoverImage();
                    }
                }


            }

            $totalPage  = intval(ceil($total / $limit));
		
            // return  $this->render('search/search-result.html.twig',[
            //     'total' => $total,
            //     'limit' => $limit,
            //     'totalPage' => $totalPage,
            //     'nowPage' => $page,
            //     'keyword' => $query,
            //     'data' => $result
            // ]);
            return $this->render('search/search-result-20230719.html.twig',[
                'total' => $total,
                'limit' => $limit,
                'totalPage' => $totalPage,
                'nowPage' => $page,
                'keyword' => $query,
                'data' => $result,
                'template_layout_name' => 'layouts/layout-20230718.html.twig'
            ]);
        }catch (\Throwable $exception){
            // return  $this->render('search/search-result.html.twig',[
            //     'total' => 0,
            //     'limit' => $limit,
            //     'totalPage' => 0,
            //     'nowPage' => $page,
            //     'keyword' => $query,
            //     'data' => []
            // ]);
            return $this->render('search/search-result-20230719.html.twig',[
                'total' => 0,
                'limit' => $limit,
                'totalPage' => 0,
                'nowPage' => $page,
                'keyword' => $query,
                'data' => [],
                'template_layout_name' => 'layouts/layout-20230718.html.twig'
            ]);
        }


    }



}
