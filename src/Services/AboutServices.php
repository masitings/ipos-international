<?php

namespace App\Services;

use Pimcore\Model\DataObject;

class AboutServices{

    public function getData($type = ''){

        $data = new DataObject\ClientsAndPartners\Listing();

        if ($type != ''){
            $data->filterByDataType($type);
        }

        $data->load();

        return $data;
    }
}
