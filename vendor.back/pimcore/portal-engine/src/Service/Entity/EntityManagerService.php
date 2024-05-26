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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class EntityManagerService
{
    const PORTAL_ENGINE_MANAGER = 'portal_engine';

    protected $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @param string|null $name
     *
     * @return EntityManagerInterface
     */
    public function getManager(?string $name = self::PORTAL_ENGINE_MANAGER)
    {
        $manager = $this->managerRegistry->getManager($name);

        if (!$manager->isOpen()) {
            return $this->managerRegistry->resetManager($name);
        }

        return $manager;
    }

    /**
     * @param object $object
     * @param bool $flush
     *
     * @return $this
     */
    public function persist($object, bool $flush = true)
    {
        $this->getManager()->persist($object);

        if ($flush) {
            $this->flush();
        }

        return $this;
    }

    /**
     * @param $object
     * @param bool $flush
     *
     * @return $this
     */
    public function remove($object, bool $flush = true)
    {
        $this->getManager()->remove($object);

        if ($flush) {
            $this->flush();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function flush()
    {
        $this->getManager()->flush();

        return $this;
    }
}
