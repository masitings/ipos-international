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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download;

use Pimcore\Bundle\PortalEngineBundle\Entity\DownloadCart;
use Pimcore\Bundle\PortalEngineBundle\Entity\DownloadCartItem;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Entity\EntityManagerService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Bundle\PortalEngineBundle\Traits\LoggerAware;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;
use Pimcore\Model\Site;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DownloadCartService
{
    use LoggerAware;

    protected $entityManagerService;
    protected $securityService;
    protected $dataPoolConfigService;
    protected $authorizationChecker;

    public function __construct(
        EntityManagerService $entityManagerService,
        SecurityService $securityService,
        DataPoolConfigService $dataPoolConfigService,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->entityManagerService = $entityManagerService;
        $this->securityService = $securityService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @return DownloadCart
     */
    public function getDownloadCart(): DownloadCart
    {
        $user = $this->securityService->getPortalUser();

        $downloadCart = $this->entityManagerService->getManager()->getRepository(DownloadCart::class)->findOneBy([
            'userId' => $user->getId()
        ]);

        if (!$downloadCart) {
            $downloadCart = new DownloadCart();
            $downloadCart->setUserId($user->getId());

            $this->entityManagerService->persist($downloadCart);
        }

        return $downloadCart;
    }

    /**
     * @parma DataPoolConfigInterface $dataPoolConfig
     *
     * @param ElementInterface $element
     * @param DownloadConfig[] $configs
     * @param bool $save
     */
    public function addItemToDownloadCart(DataPoolConfigInterface $dataPoolConfig, ElementInterface $element, array $configs, bool $save = true)
    {
        if (empty($configs)) {
            return;
        }

        $item = $this->entityManagerService->getManager()->getRepository(DownloadCartItem::class)->findOneBy([
            'cart' => $this->getDownloadCart(),
            'dataPoolId' => $dataPoolConfig->getId(),
            'elementId' => $element->getId(),
            'elementType' => Service::getElementType($element)
        ]);

        if (!$item) {
            $item = (new DownloadCartItem())
                ->setDataPoolId($dataPoolConfig->getId())
                ->setCart($this->getDownloadCart())
                ->setElementId($element->getId())
                ->setElementType(Service::getElementType($element))
                ->setElementSubType($element->getType());
        }

        $item->setConfigs($configs);

        $this->entityManagerService->persist($item, $save);
    }

    public function clearDownloadCart()
    {
        foreach ($this->getDownloadCartItems() as $item) {
            $this->entityManagerService->remove($item, false);
        }

        $this->entityManagerService->flush();
    }

    /**
     * @return DownloadCartItem[]
     */
    public function getDownloadCartItems()
    {
        return $this->entityManagerService->getManager()->getRepository(DownloadCartItem::class)->findBy([
            'cart' => $this->getDownloadCart()
        ]);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     *
     * @throws \Exception
     */
    public function getDownloadCartItemsQuery()
    {
        return $builder = $this->entityManagerService->getManager()->createQueryBuilder()
            ->select('dci')
            ->from(DownloadCartItem::class, 'dci')
            ->where('dci.cart = :cart')
            ->andWhere('dci.dataPoolId in(:dataPoolIds)')
            ->setParameter('cart', $this->getDownloadCart())
            ->setParameter('dataPoolIds', $this->dataPoolConfigService->getDataPoolDocumentIdsFromSite())
            ->orderBy('dci.createdAt', 'ASC');
    }

    /**
     * @param string|DownloadCartItem $itemKeyOrItem
     * @param bool $save
     */
    public function removeItemFromDownloadCart($itemKeyOrItem, bool $save = true)
    {
        if (!$itemKeyOrItem instanceof DownloadCartItem) {
            $itemKeyOrItem = $this->getItemByItemKey($itemKeyOrItem);
        }

        if (!$itemKeyOrItem) {
            return;
        }

        $this->entityManagerService->remove($itemKeyOrItem, $save);
    }

    /**
     * @param string $itemKey
     *
     * @return DownloadCartItem|null
     */
    public function getItemByItemKey(string $itemKey)
    {
        $baseParams = ['cart' => $this->getDownloadCart()];
        $itemKeyParams = $this->getParamsFromItemKey($itemKey);

        return $this->entityManagerService->getManager()->getRepository(DownloadCartItem::class)->findOneBy(array_merge(
            $baseParams,
            $itemKeyParams
        ));
    }

    /**
     * @param string $itemKey
     *
     * @return array
     */
    public function getParamsFromItemKey(string $itemKey)
    {
        list($dataPoolId, $elementType, $elementId) = explode('_', $itemKey);

        return [
            'dataPoolId' => $dataPoolId,
            'elementType' => $elementType,
            'elementId' => $elementId
        ];
    }

    /**
     * @param DownloadCartItem $downloadCartItem
     *
     * @return string
     */
    public function createItemKey(DownloadCartItem $downloadCartItem)
    {
        return implode('_', [
            $downloadCartItem->getDataPoolId(),
            $downloadCartItem->getElementType(),
            $downloadCartItem->getElementId()
        ]);
    }

    /**
     * @throws \Exception
     */
    public function cleanupInvalidDownloadCartItems()
    {
        $sites = new Site\Listing;
        foreach ($sites as $site) {
            $items = $this->entityManagerService->getManager()->createQueryBuilder()
                ->select('dci')
                ->from(DownloadCartItem::class, 'dci')
                ->where('dci.dataPoolId not in(:dataPoolIds)')
                ->setParameter('dataPoolIds', $this->dataPoolConfigService->getDataPoolDocumentIdsFromSite($site))
                ->getQuery()
                ->getResult();

            /**
             * @var DownloadCartItem $item
             */
            foreach ($items as $item) {
                $this->logger->info(
                    sprintf(
                        'Cleanup download item as it is not in a valid data pool anymore (cartId: %s, elementId: %s, elementType: %s, elementSubType: %s, dataPoolId: %s).',
                        $item->getCart()->getId(),
                        $item->getElementId(),
                        $item->getElementType(),
                        $item->getElementSubType(),
                        $item->getDataPoolId()
                    )
                );
                $this->entityManagerService->remove($item);
            }
        }
    }
}
