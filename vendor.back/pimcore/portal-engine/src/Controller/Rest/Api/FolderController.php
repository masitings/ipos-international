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

namespace Pimcore\Bundle\PortalEngineBundle\Controller\Rest\Api;

use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\ApiPayload;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\TranslatorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\UrlExtractorService;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/folder", condition="request.attributes.get('isPortalEngineSite')")
 */
class FolderController extends AbstractRestApiController
{
    protected function getCurrentFolderFromRequest(Request $request)
    {
        $elementType = $request->get('elementType');

        if (!in_array($elementType, ['asset', 'object'])) {
            throw $this->createNotFoundException('Given element type is not valid');
        }

        $currentFolder = str_replace('//', '/', $request->get('currentFolder'));
        $name = Service::getValidKey($request->get('name'), $elementType);

        $folder = Service::getElementByPath($elementType, $currentFolder);

        if (!$folder || $folder->getType() !== 'folder') {
            throw $this->createNotFoundException('Current folder does not exist');
        }

        return [$elementType, $name, $folder];
    }

    /**
     * @Route(
     *     "/create",
     *     name = "pimcore_portalengine_rest_api_folder_create"
     * )
     */
    public function createAction(Request $request, DataPoolConfigService $dataPoolConfigService, UrlExtractorService $urlExtractorService, TranslatorService $translatorService)
    {
        $dataPoolConfigService->setCurrentDataPoolConfigById($request->get('dataPoolId'));

        /**
         * @var string $elementType ,
         * @var string $name
         * @var ElementInterface $folder
         */
        list($elementType, $name, $folder) = $this->getCurrentFolderFromRequest($request);

        $this->denyAccessUnlessGranted(Permission::SUBFOLDER, $folder);

        $path = rtrim($folder->getRealFullPath(), '/');
        $path = "{$path}/{$name}";
        $createdFolder = null;

        switch ($elementType) {
            case 'asset':
                $createdFolder = \Pimcore\Model\Asset\Service::createFolderByPath($path);
                break;

            case 'object':
                $createdFolder = \Pimcore\Model\DataObject\Service::createFolderByPath($path);
                break;
        }

        return $this->json(new ApiPayload([
            'url' => $createdFolder ? $urlExtractorService->extractUrl($createdFolder) : null
        ], !$createdFolder ? $translatorService->translate('could-not-create-folder') : null));
    }

    /**
     * @Route(
     *     "/rename",
     *     name = "pimcore_portalengine_rest_api_folder_rename"
     * )
     */
    public function renameAction(Request $request, DataPoolConfigService $dataPoolConfigService, UrlExtractorService $urlExtractorService, TranslatorService $translatorService)
    {
        $dataPoolConfigService->setCurrentDataPoolConfigById($request->get('dataPoolId'));

        /**
         * @var string $elementType ,
         * @var string $name
         * @var ElementInterface $folder
         */
        list($elementType, $name, $folder) = $this->getCurrentFolderFromRequest($request);

        $this->denyAccessUnlessGranted(Permission::UPDATE, $folder);

        $error = false;

        try {
            $folder->setKey($name)->save();
        } catch (\Exception $e) {
            $error = true;
        }

        return $this->json(new ApiPayload([
            'url' => $urlExtractorService->extractUrl($folder)
        ], $error ? $translatorService->translate('could-not-rename-folder') : null));
    }

    /**
     * @Route(
     *     "/delete",
     *     name = "pimcore_portalengine_rest_api_folder_delete"
     * )
     */
    public function deleteAction(Request $request, DataPoolConfigService $dataPoolConfigService, UrlExtractorService $urlExtractorService, TranslatorService $translatorService)
    {
        $dataPoolConfigService->setCurrentDataPoolConfigById($request->get('dataPoolId'));

        /**
         * @var string $elementType
         * @var string $name
         * @var ElementInterface $folder
         */
        list($elementType, $name, $folder) = $this->getCurrentFolderFromRequest($request);

        $this->denyAccessUnlessGranted(Permission::DELETE, $folder);

        $error = false;
        $parent = $folder->getParent();

        try {
            $folder->delete();
        } catch (\Exception $e) {
            $error = true;
        }

        if (!$this->isGranted(Permission::VIEW, $parent)) {
            $parent = null;
        }

        return $this->json(new ApiPayload([
            'url' => $parent ? $urlExtractorService->extractUrl($parent) : $dataPoolConfigService->getCurrentDataPoolConfig()->getDocument()
        ], $error ? $translatorService->translate('could-not-delete-folder') : null));
    }
}
