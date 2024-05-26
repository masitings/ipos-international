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

class FieldsCollection
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * @var array
     */
    protected $operators;

    /**
     * @param array $fields
     * @param array $operators
     */
    public function __construct(array $fields, array $operators)
    {
        $this->fields = $fields;
        $this->operators = $operators;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return array
     */
    public function getOperators(): array
    {
        return $this->operators;
    }
}
