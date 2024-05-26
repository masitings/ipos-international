<?php

/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */

namespace Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data;

use Pimcore\AssetMetadataClassDefinitionsBundle\Helper;

class User extends Data
{
    /**
     * @var string
     */
    public $fieldtype = 'user';

    public $options = [];

    /**
     * Add dynamic layout options
     */
    public function enrichDefinition()
    {
        $this->generateOptions();
    }

    public function generateOptions()
    {
        $list = new \Pimcore\Model\User\Listing();
        $list->setOrder('asc');
        $list->setOrderKey('name');
        $users = $list->load();

        $options = [];
        if (is_array($users) and count($users) > 0) {
            foreach ($users as $user) {
                if ($user instanceof \Pimcore\Model\User) {
                    $value = $user->getName();
                    $first = $user->getFirstname();
                    $last = $user->getLastname();
                    if (!empty($first) or !empty($last)) {
                        $value .= ' (' . $first . ' ' . $last . ')';
                    }
                    $options[] = [
                        'value' => $user->getId(),
                        'key' => $value,
                    ];
                }
            }
        }
        $this->setOptions($options);
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function addListFolderConfig(&$item)
    {
        $this->addGridConfig($item);
    }

    public function addGridConfig(&$item)
    {

        /** @var User $fieldDefinition */
        $fieldDefinition = Helper::getFieldDefinition($item['name']);

        if ($fieldDefinition) {
            $fieldDefinition->generateOptions();
            $config = json_encode($fieldDefinition);
            $item['config'] = $config;
        }
    }

    /**
     * @param mixed $data
     * @param array $params
     *
     * @return mixed
     */
    public function getDataForListfolderGrid($data, $params = [])
    {

        /** @var User $fieldDefinition */
        $fieldDefinition = Helper::getFieldDefinition($params['name']);
        if ($data instanceof \Pimcore\Model\User) {
            $data = $data->getId();
        }

        if ($fieldDefinition) {
            $this->generateOptions();

            return [
                'value' => $data,
                'options' => $this->options
            ];
        }

        return $data;
    }

    /**
     * @param mixed $data
     * @param array $params
     *
     * @return mixed
     */
    public function transformGetterData($data, $params = [])
    {
        if ($data) {
            $user = \Pimcore\Model\User::getById($data);

            return $user;
        }

        return null;
    }

    /**
     * @param mixed $data
     * @param array $params
     *
     * @return mixed
     */
    public function transformSetterData($data, $params = [])
    {
        if ($data instanceof \Pimcore\Model\User) {
            $userId = $data->getId();

            return $userId;
        }

        return $data;
    }

    /** @inheritDoc */
    public function getDataForEditMode($data, $params = [])
    {
        if ($data instanceof \Pimcore\Model\User) {
            return $data->getId();
        }

        return $data;
    }

    /** @inheritDoc */
    public function getVersionPreview($data, $params = [])
    {
        if ($data instanceof \Pimcore\Model\User) {
            return $data->getName();
        }
    }
}
