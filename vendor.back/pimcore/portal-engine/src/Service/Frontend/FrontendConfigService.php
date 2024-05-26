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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Frontend;

class FrontendConfigService
{
    protected $config = [];

    public function setupDataPool()
    {
    }

    /**
     * @param string $path
     * @param $value
     *
     * @return $this
     */
    public function setConfig(string $path, $value)
    {
        $parts = explode('.', $path);

        if (empty($parts)) {
            return $this;
        }

        $current = &$this->config;
        $last = array_pop($parts);

        if (!empty($parts)) {
            foreach ($parts as $part) {
                if (!array_key_exists($part, $current)) {
                    $current[$part] = [];
                }

                $current = &$current[$part];
            }
        }

        if ($last === '[]') {
            $current[] = $value;
        } else {
            $current[$last] = $value;
        }

        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function __toString()
    {
        if (empty($this->config)) {
            return '';
        }

        return sprintf('<script type="application/json" id="js-frontend-config">%s</script>', json_encode($this->config));
    }
}
