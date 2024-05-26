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

namespace Pimcore\Bundle\StatisticsExplorerBundle\Model;

class StatisticsResult
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $columnHeaders;

    /**
     * @var array
     */
    protected $rowHeaders;

    /**
     * @param array $data
     * @param array $columnHeaders
     * @param array $rowHeaders
     */
    public function __construct(array $data, array $columnHeaders, array $rowHeaders)
    {
        $this->data = $data;
        $this->columnHeaders = $columnHeaders;
        $this->rowHeaders = $rowHeaders;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getColumnHeaders(): array
    {
        return $this->columnHeaders;
    }

    /**
     * @return array
     */
    public function getRowHeaders(): array
    {
        return $this->rowHeaders;
    }
}
