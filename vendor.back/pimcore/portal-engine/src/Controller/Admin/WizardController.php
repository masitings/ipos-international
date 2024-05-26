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

namespace Pimcore\Bundle\PortalEngineBundle\Controller\Admin;

use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\ThumbnailService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\ClassDefinitionService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\CustomLayoutService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DownloadFormatHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Wizard\WizardService;
use Pimcore\Bundle\PortalEngineBundle\Twig\EditmodeExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/portal-engine/wizard")
 */
class WizardController extends AdminController
{
    /**
     * @Route("/get-class-definitions", name="pimcore_portalengine_admin_wizard_get_class_definitions")
     *
     * @param ClassDefinitionService $classDefinitionService
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getClassDefinitionsAction(ClassDefinitionService $classDefinitionService)
    {
        try {
            return $this->adminJson(
                [
                    'success' => true,
                    'data' => $this->transformSelectStoreToAdminStoreData(
                        $classDefinitionService->getClassDefinitionSelectStore()
                    )
                ]
            );
        } catch (\Exception $e) {
            return $this->adminJson(['success' => false]);
        }
    }

    /**
     * @Route("/get-available-download-thumbnails", name="pimcore_portalengine_admin_wizard_get_available_download_thumbnails")
     *
     * @param ThumbnailService $thumbnailService
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getAvailableDownloadThumbnailsAction(ThumbnailService $thumbnailService)
    {
        try {
            return $this->adminJson(
                [
                    'success' => true,
                    'data' => $this->transformSelectStoreToAdminStoreData(
                        $thumbnailService->getImageThumbnailSelectStore()
                    )
                ]
            );
        } catch (\Exception $e) {
            return $this->adminJson(['success' => false]);
        }
    }

    /**
     * @Route("/get-available-download-formats", name="pimcore_portalengine_admin_wizard_get_available_download_formats")
     *
     * @param DownloadFormatHandler $downloadFormatHandler
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getAvailableDownloadFormatsAction(DownloadFormatHandler $downloadFormatHandler)
    {
        try {
            return $this->adminJson(
                [
                    'success' => true,
                    'data' => $this->transformSelectStoreToAdminStoreData(
                        $downloadFormatHandler->getDownloadFormatServicesSelectStore(false)
                    )
                ]
            );
        } catch (\Exception $e) {
            return $this->adminJson(['success' => false]);
        }
    }

    /**
     * @Route("/get-object-layouts", name="pimcore_portalengine_admin_wizard_get_object_layouts")
     *
     * @param Request $request
     * @param CustomLayoutService $customLayoutService
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getObjectLayoutsAction(Request $request, CustomLayoutService $customLayoutService)
    {
        try {
            return $this->adminJson(
                [
                    'success' => true,
                    'data' => $this->transformSelectStoreToAdminStoreData(
                        $customLayoutService->getCustomLayoutsSelectStore((string) $request->get('classDefinition'))
                    )
                ]
            );
        } catch (\Exception $e) {
            return $this->adminJson(['success' => false]);
        }
    }

    /**
     * @Route("/get-icons", name="pimcore_portalengine_admin_wizard_get_icons")
     *
     * @param EditmodeExtension $editmodeExtension
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getIconsAction(EditmodeExtension $editmodeExtension)
    {
        try {
            return $this->adminJson(
                [
                    'success' => true,
                    'data' => $this->transformSelectStoreToAdminStoreData(
                        $editmodeExtension->getIconStore()
                    )
                ]
            );
        } catch (\Exception $e) {
            return $this->adminJson(['success' => false]);
        }
    }

    /**
     * @Route("/create-portal", name="pimcore_portalengine_admin_wizard_create_portal")
     *
     * @param Request $request
     * @param WizardService $wizardService
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function createPortalAction(Request $request, WizardService $wizardService)
    {
        $data = json_decode($request->get('data', ''), true);

        if (!is_array($data) || !sizeof($data)) {
            throw new BadRequestHttpException('invalid data sent');
        }

        try {
            return $this->adminJson(
                [
                    'success' => true,
                    'tmpStoreKey' => $wizardService->startWizard($data)
                ]
            );
        } catch (\Exception $e) {
            return $this->adminJson(['success' => false]);
        }
    }

    /**
     * @Route("/create-portal-status", name="pimcore_portalengine_admin_wizard_create_portal_status")
     *
     * @param Request $request
     * @param WizardService $wizardService
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function createPortalStatusAction(Request $request, WizardService $wizardService)
    {
        $tmpStoreKey = $request->get('tmpStoreKey');

        if (!$tmpStoreKey) {
            throw new BadRequestHttpException('no tmpStoreKey given');
        }

        try {
            return $this->adminJson(
                [
                    'success' => true,
                    'isWizardFinished' => $wizardService->isFinished($tmpStoreKey),
                    'isWizardSuccess' => $wizardService->isSuccess($tmpStoreKey),
                    'portalDocumentId' => $wizardService->getPortalDocumentId($tmpStoreKey),
                    'statusMessage' => $wizardService->getStatusMessage($tmpStoreKey)
                ]
            );
        } catch (\Exception $e) {
            return $this->adminJson(['success' => false]);
        }
    }

    /**
     * @return array
     */
    protected function transformSelectStoreToAdminStoreData(array $data): array
    {
        $result = [];
        foreach ($data as $row) {
            $result[] = [
                'id' => $row[0],
                'name' => $row[1],
            ];
        }

        return $result;
    }
}
