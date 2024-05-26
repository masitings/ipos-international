<?php

namespace App\Services;

use Pimcore\Model\DataObject;

class ResourcesServices{

    public function getResources($resourcesType = '')
    {

        $list = new DataObject\Resources\Listing();
        if ($resourcesType != ''){
            $list->filterByResourceType($resourcesType);
        }

        $list->setOrderKey('sort');
        $list->setOrder('ASC');
        $list->load();


        return $list;
    }

    public function getDetail($id) : array
    {
        $obj = DataObject\Resources::getById($id);

        if (empty($obj)){
            return [];
        }

        $list['title'] = $obj->getTitle();
        $list['releaseDate'] = $obj->getReleaseDate();
        $list['author'] = $obj->getAuthor();
        $list['authorIcon'] = $obj->getAuthorIcon();
        $list['overImage'] = $obj->getDetailCoverImage();
        $list['content'] = $obj->getContent();
        $list['videoTime'] = $obj->getVideoTime();
        $list['also'] = $obj->getAlsoList();
        $list['file'] = $obj->getFile();
        $list['video'] = $obj->getDetailVideo();
        $list['checkList'] = $obj->getCheckIndustry();
        $list['fullGuide'] = $obj->getFullGuide();
        $list['listType'] = $obj->getListType();

        return $list;
    }

    public function objectToJson($dataObj)
    {
        $result = [];

        if ($dataObj)
        {
            foreach ($dataObj as $v)
            {
                $result = [
                    'id' => $v->getId(),

                ];
            }
        }

    }

}
