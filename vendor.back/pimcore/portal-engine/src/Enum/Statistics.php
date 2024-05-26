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

namespace Pimcore\Bundle\PortalEngineBundle\Enum;

use MyCLabs\Enum\Enum;

/**
 * Class UserConfig
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Enum
 */
class Statistics extends Enum
{
    const ASSETS_TYPES = 'assets_types';
    const ASSET_STORAGE_BY_TYPES = 'asset_storage_by_types';
    const MOST_DOWNLOADED_ASSETS = 'most_downloaded_assets';
    const MOST_DOWNLOADED_ASSETS_USER = 'most_downloaded_assets_of_user';
    const DOWNLOADS_OVER_TIME = 'downloads_over_time';
    const DOWNLOADS_OVER_TIME_CONTEXT = 'downloads_over_time_context';
    const DOWNLOADS_OVER_TIME_THUMBNAIL = 'downloads_over_time_thumbnail';
    const CLASSDEFINITIONS_CLASS_NAMES = 'classdefintions_class_names';
    const ALL_LOGINS_LAST_SIX_MONTHS = 'all_logins_last_six_months';
    const MOST_RECENT_DOWNLOADS = 'most_recent_downloads';
    const MOST_RECENT_DOWNLOADS_USER = 'most_recent_downloads_user';
    const MOST_RECENT_UPDATES = 'most_recent_updates';
    const MOST_RECENT_UPDATES_USER = 'most_recent_updates_user';

    const ASSET_STATISTICS = [
        self::ASSETS_TYPES,
        self::ASSET_STORAGE_BY_TYPES,
        self::MOST_DOWNLOADED_ASSETS,
        self::MOST_DOWNLOADED_ASSETS_USER,
        self::DOWNLOADS_OVER_TIME_CONTEXT,
        self::DOWNLOADS_OVER_TIME_THUMBNAIL,
        self::DOWNLOADS_OVER_TIME,
        self::MOST_RECENT_DOWNLOADS,
        self::MOST_RECENT_DOWNLOADS_USER,
        self::MOST_RECENT_UPDATES,
        self::MOST_RECENT_UPDATES_USER,
    ];

    const DATA_OBJECT_STATISTICS = [
        self::CLASSDEFINITIONS_CLASS_NAMES
    ];

    const ADD_6_MONTHS_CONDITION = [
        self::ALL_LOGINS_LAST_SIX_MONTHS,
        self::DOWNLOADS_OVER_TIME,
        self::DOWNLOADS_OVER_TIME_CONTEXT,
        self::DOWNLOADS_OVER_TIME_THUMBNAIL,

    ];

    const ADD_USER_CONDITION = [
        self::MOST_DOWNLOADED_ASSETS_USER,
        self::MOST_RECENT_DOWNLOADS_USER,
        self::MOST_RECENT_UPDATES_USER,
    ];

    const FORMAT_TIMESTAMP_STATISTICS = [
        self::MOST_RECENT_DOWNLOADS,
        self::MOST_RECENT_DOWNLOADS_USER,
        self::MOST_RECENT_UPDATES,
        self::MOST_RECENT_UPDATES_USER,
    ];

    const RENDER_AS_ASSET_TABLE = [
        self::MOST_DOWNLOADED_ASSETS,
        self::MOST_DOWNLOADED_ASSETS_USER,
        self::MOST_RECENT_DOWNLOADS,
        self::MOST_RECENT_DOWNLOADS_USER,
        self::MOST_RECENT_UPDATES,
        self::MOST_RECENT_UPDATES_USER
    ];
}
