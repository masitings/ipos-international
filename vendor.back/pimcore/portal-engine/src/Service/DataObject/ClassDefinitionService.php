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

namespace Pimcore\Bundle\PortalEngineBundle\Service\DataObject;

use Pimcore\Db\Connection;

class ClassDefinitionService
{
    /**
     * @var Connection
     */
    protected $db;

    /**
     * ClassDefinitionService constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @return array
     */
    public function getClassDefinitionSelectStore(): array
    {
        $classes = $this->db->fetchAllAssociative('select id,name from classes order by name');

        $result = [];
        foreach ($classes as $row) {
            $result[] = [$row['id'], $row['name']];
        }

        return $result;
    }

    /**
     * @param string $classId
     *
     * @return string|null
     */
    public function getClassDefinitionNameById(string $classId): ?string
    {
        return $this->db->fetchOne('select name from classes where id = ?', $classId);
    }
}
