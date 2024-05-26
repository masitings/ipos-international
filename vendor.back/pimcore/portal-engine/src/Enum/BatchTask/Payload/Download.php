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

namespace Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Payload;

class Download
{
    const ZIP_ID = 'zipId';
    const SINGLE_FILE = 'singleFile';
    const SINGLE_FILE_POTENTIAL_PATH_IN_ZIP = 'singleFilePotentialPathInZipFile';
    const BUNDLE_STRUCTURED_DATA = 'bundleStructuredData';
}
