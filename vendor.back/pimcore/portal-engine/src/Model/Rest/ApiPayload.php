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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Rest;

use Pimcore\Bundle\PortalEngineBundle\Exception\OutputErrorException;
use Pimcore\Bundle\PortalEngineBundle\Model\BasicJsonModel;

/**
 * Class ApiPayload
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Model\Rest
 */
class ApiPayload extends BasicJsonModel
{
    /**
     * ApiPayload constructor.
     *
     * @param array|\JsonSerializable $data
     * @param string|null $error
     */
    public function __construct($data, ?string $error = null)
    {
        parent::__construct([
            'success' => !$error,
            'data' => $data,
            'error' => $error
        ]);
    }

    /**
     * @param \Exception $exception
     *
     * @return $this
     */
    public function handleOutputErrorException($exception)
    {
        if ($exception instanceof OutputErrorException) {
            $this->replace([
                'success' => false,
                'data' => $this->get('data'),
                'error' => $exception->getMessage()
            ]);
        } elseif ($exception instanceof \Throwable) {
            $this->replace([
                'success' => false,
                'error' => \Pimcore::inDebugMode() ? $exception->getMessage() : 'An error has occured.'
            ]);
        }

        return $this;
    }

    /**
     * @param array|\JsonSerializable $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->replace([
            'success' => $this->get('success'),
            'data' => $data,
            'error' => $this->get('error')
        ]);

        return $this;
    }
}
