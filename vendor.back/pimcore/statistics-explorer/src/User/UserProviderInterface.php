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

namespace Pimcore\Bundle\StatisticsExplorerBundle\User;

interface UserProviderInterface
{
    /**
     * Return ID of current user
     *
     * @return int|null
     */
    public function getCurrentUserId(): ?int;

    /**
     * Returns array of conditions for filtering saved configurations for that user in the following schema
     * [['<TYPE>', <ID>], ['user', 34], ['role', 35], ['role', 38], , ['role', 40]]
     *
     * @return array|null
     */
    public function getSharedWithCurrentUserCondition(): ?array;

    /**
     * Returns array of other users of system (for selection to share configurations with) in the following schema
     * [
     *   'users' => [
     *      ['label' => 'Jon Doe', 'value' => <ID>],
     *      ['label' => 'Jone Doe', 'value' => 123]
     *   ],
     *   'roles' => [
     *      ['label' => 'Editors', 'value' => 33],
     *      ['label' => 'External Users', 'value' => 55]
     *   ]
     *
     * @return array
     */
    public function getOtherUsers(): array;
}
