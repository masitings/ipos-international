<?php

namespace App\Services;

use Pimcore\Model\DataObject;

class CourseServices{

    public function getCurses($academyCourseType = ''){


        $list =  new DataObject\Course\Listing();

        $ob = $list->getClass();
        $ret['topic'] = $ob->getFieldDefinition("topic")->getOptions();
        $ret['eventType'] = $ob->getFieldDefinition("eventType")->getOptions();
        $ret['level'] = $ob->getFieldDefinition("level")->getOptions();
	$ret['fee'] = $ob->getFieldDefinition("fee")->getOptions();



        return $ret;
    }


}
