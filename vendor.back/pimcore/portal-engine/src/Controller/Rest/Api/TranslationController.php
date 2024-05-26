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

use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Bundle\PortalEngineBundle\Controller\AbstractSiteController;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\TranslatorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\LanguagesService;
use Pimcore\Model\Translation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/translation", condition="request.attributes.get('isPortalEngineSite')")
 */
class TranslationController extends AbstractSiteController
{
    /**
     * @Route("/valid-languages",
     *     name="pimcore_portalengine_rest_api_translation_valid_languages"
     * )
     */
    public function validLanguagesAction(Request $request, LanguagesService $languagesService, DataPoolConfigService $dataPoolConfigService)
    {
        if ($dataPoolId = (int)$request->get('dataPoolId')) {
            $dataPoolConfigService->setCurrentDataPoolConfigById($dataPoolId);
        }

        return new JsonResponse(
            [
                'success' => true,
                'data' => [
                    'visible' => $languagesService->getVisibleLanguages(),
                    'editable' => $languagesService->getEditableLanguages(),
                    'config' => $languagesService->getLanguageConfig()
                ]
            ]
        );
    }

    /**
     * @Route("/load-catalogue/{_locale}/{domain}", name="pimcore_portalengine_rest_api_translation_load_catalogue")
     */
    public function loadCatalogueAction($_locale, $domain, TranslatorService $translatorService)
    {
        // todo this should be replaced with the new translation system provided by pimcore when only one sort of translation keys exist
        // todo later we can use $translator->getCatalogue($_locale)->all($domain)

        $catalogue = [];
        $prefix = $translatorService->getDomainPrefix($domain);

        $translations = new Translation\Listing();
        $translations->setDomain('messages');
        $translations->addConditionParam('`key` like :key', ['key' => "{$prefix}%"]);

        foreach ($translations->load() as $translation) {
            if ($translation) {
                $key = $translation->getKey();

                // remove prefixes so that the api works as it will later
                $key = str_replace($prefix, '', $key);

                if ($translation->hasTranslation($_locale)) {
                    $catalogue[$key] = $translation->getTranslation($_locale);
                }
            }
        }

        return $this->json([
            'success' => true,
            'data' => $catalogue
        ]);
    }

    /**
     * @Route("/add-keys", name="pimcore_portalengine_rest_api_translation_add_keys")
     */
    public function addKeysAction(Request $request, TranslatorService $translatorService)
    {
        try {
            $content = json_decode($request->getContent(), true);

            foreach ($content['keys'] as $add) {
                if (!$add['key']) {
                    continue;
                }

                // todo adapt to new translations later on
                $key = $translatorService->getDomainPrefix($add['domain']) . $add['key'];
                Translation::getByKey($key, 'messages', true);
            }

            return $this->json([
                'success' => true,
                'data' => []
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'data' => []
            ]);
        }
    }
}
