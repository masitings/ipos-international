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

namespace Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask;

class State
{
    const PREPARING = 'preparing'; //initial state
    const RUNNING = 'running'; //items are currently in process queue
    const FINISHED = 'finished'; //all items are processed
}
