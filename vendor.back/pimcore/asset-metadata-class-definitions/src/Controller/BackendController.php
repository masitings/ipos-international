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

namespace Pimcore\AssetMetadataClassDefinitionsBundle\Controller;

use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration\Dao;
use Pimcore\AssetMetadataClassDefinitionsBundle\Service;
use Pimcore\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend")
 */
class BackendController extends \Pimcore\Bundle\AdminBundle\Controller\AdminController
{
    const CONFIG_PERMISSION = 'classes';

    /**
     * @Route("/list-configurations")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listConfigurationsAction(Request $request): JsonResponse
    {
        // check permissions
        //$this->checkPermission(self::CONFIG_PERMISSION);
        $forEditor = (int)$request->get('forEditor');

        $list = Dao::getList($forEditor);
        $tree = [];

        // add configurations to their corresponding folder
        foreach ($list as $configuration) {
            $config = $forEditor ? $configuration : $this->buildItem($configuration);

            if ($forEditor) {
                $definitions = [];
                $localizedDefinitions = [];
                $layoutDefinitions = $configuration->getLayoutDefinitions();
                Service::extractDataDefinitions($layoutDefinitions, false, $definitions, $localizedDefinitions);
                Service::enrichDefinitions($definitions);
                Service::enrichDefinitions($localizedDefinitions);
                $tree[$configuration->getName()] = $config;
            } else {
                $tree[] = $config;
            }
        }

        if ($forEditor) {
            return $this->adminJson([
                    'configurations' => $tree
                ]
            );
        } else {
            return $this->adminJson($tree);
        }
    }

    /**
     * @param Configuration $configuration
     *
     * @return array
     */
    private function buildItem($configuration): array
    {
        return [
            'id' => $configuration->getName(),
            'name' => $configuration->getName(),
            'text' => $configuration->getName(),
            'type' => 'config',
            'iconCls' => 'pimcore_icon_settings',
            'expandable' => false,
            'leaf' => true,
        ];
    }

    /**
     * @Route("/configuration-update")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request): JsonResponse
    {
        try {
            $key = $request->get('name');

            $def = Dao::getByName($key);

            if ($request->get('task') == 'add') {
                // check for existing configuration  with same name

                if ($def) {
                    throw new \Exception('Configuration with the same name already exists.');
                }
                $def = new Configuration();
                $def->setName($key);
                $def->setPrefix($key);
            } else {
                $values = $request->get('values');
                $values = $this->decodeJson($values);

                if (empty($values['prefix'])) {
                    throw new \Exception('prefix must not be empty');
                }

                $def->setTitle($values['title']);
                $def->setIcon($values['icon']);

                $list = Dao::getList();

                /** @var Configuration $item */
                foreach ($list as $item) {
                    if ($item->getPrefix() === $values['prefix'] && $item->getName() !== $key) {
                        throw new \Exception('prefix [' . $values['prefix'] . '] already used by configuration ' . $item->getName());
                    }
                }

                $def->setPrefix($values['prefix']);
            }

            if ($request->get('configuration')) {
                $configuration = $this->decodeJson($request->get('configuration'));

                $configuration['datatype'] = 'layout';
                $configuration['fieldtype'] = 'panel';

                $layout = Service::generateLayoutTreeFromArray($configuration, true);
                $def->setLayoutDefinitions($layout);
            }

            $def->save();

            return $this->adminJson(['success' => true, 'id' => $def->getName()]);
        } catch (\Exception $e) {
            Logger::error($e->getMessage());

            return $this->adminJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @Route("/configuration-delete", methods={"DELETE"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function configurationDeleteAction(Request $request)
    {
        $configuration = Dao::getByName($request->get('name'));
        if ($configuration) {
            $configuration->delete();
        }

        return $this->adminJson(['success' => true]);
    }

    /**
     * @Route("/configuration-get", methods={"GET"})
     *
     * @param Request $request
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function configurationGetAction(Request $request)
    {
        $configuration = Dao::getByName($request->get('name'));

        return $this->adminJson($configuration);
    }

    /**
     * @Route("/get-metadata-for-column-config", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getMetadataForColumnConfigAction(Request $request)
    {
        $result = [];

        $list = Dao::getList(true);
        foreach ($list as $configuration) {
            $metadataItems = [];

            $definitions = [];
            $localizedDefinitions = [];
            $layoutDefinitions = $configuration->getLayoutDefinitions();
            $name = $configuration->getName();
            Service::extractDataDefinitions($layoutDefinitions, false, $definitions, $localizedDefinitions);

            $keys = array_keys($definitions);

            for ($i = 0; $i < count($keys); $i++) {
                $key = $keys[$i];

                /** @var \Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Data $def */
                $def = $definitions[$key];

                $item = [
                    'title' => $key,
                    'copyTitle' => $configuration->getPrefix() . '.' . $key,
                    'name' => $configuration->getPrefix() . '.' . $key,
                    'subtype' => null,
                    'datatype' => 'data',
                    'fieldtype' => $def->getFieldtype(),
                    'isUnlocalized' => true
                ];
                Service::enrichListfolderDefinition($item, $def);
                $metadataItems[] = $item;
            }

            $keys = array_keys($localizedDefinitions);

            if ($keys) {
                $childs = [];

                for ($i = 0; $i < count($keys); $i++) {
                    $key = $keys[$i];

                    /** @var \Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Data $def */
                    $def = $localizedDefinitions[$key];

                    $item = [
                        'title' => $key,
                        'copyTitle' => $configuration->getPrefix() . '.' . $key,
                        'name' => $configuration->getPrefix() . '.' . $key,
                        'subtype' => null,
                        'datatype' => 'data',
                        'fieldtype' => $def->getFieldtype(),
                        'isUnlocalized' => false
                    ];
                    Service::enrichListfolderDefinition($item, $def);

                    $childs[] = $item;
                }

                $metadataItems[] = [
                    'childs' => $childs,
                    'nodeType' => 'metadata',
                    'title' => 'localizedfields',
                    'name' => 'localizedfields',
                     'subtype' => null,
                    'datatype' => 'data',
                    'fieldtype' => 'localizedfields'
                ];
            }

            $result[$name]['childs'] = $metadataItems;
            $result[$name]['nodeLabel'] = $name;
            $result[$name]['nodeType'] = 'metadata';
        }

        //system columns
        $systemColumnNames = \Pimcore\Model\Asset\Service::GRID_SYSTEM_COLUMNS;
        $systemColumns = [];
        foreach ($systemColumnNames as $systemColumn) {
            $systemColumns[] = ['title' => $systemColumn, 'name' => $systemColumn, 'datatype' => 'data', 'fieldtype' => 'system'];
        }
        $result['systemColumns']['nodeLabel'] = 'system_columns';
        $result['systemColumns']['nodeType'] = 'system';
        $result['systemColumns']['childs'] = $systemColumns;

        return $this->adminJson($result);
    }
}
