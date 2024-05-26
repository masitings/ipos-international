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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download\Generator;

use Pimcore\Bundle\PortalEngineBundle\Enum\Download\Type;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableStructuredData;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\GenerationResult;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DownloadFormatHandler;
use Pimcore\Model\Element\ElementInterface;

class StructuredDataGenerator implements DownloadGeneratorInterface
{
    /**
     * @var DownloadFormatHandler
     */
    protected $downloadFormatHandler;

    /**
     * DataObjectStructuredDataGenerator constructor.
     *
     * @param DownloadFormatHandler $downloadFormatHandler
     */
    public function __construct(DownloadFormatHandler $downloadFormatHandler)
    {
        $this->downloadFormatHandler = $downloadFormatHandler;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($source, DownloadConfig $config): bool
    {
        return $source instanceof ElementInterface && $config->getType() === Type::STRUCTURED_DATA;
    }

    /**
     * {@inheritDoc}
     */
    public function createDownloadable($source, DownloadConfig $config): DownloadableInterface
    {
        return (new DownloadableStructuredData())
            ->setGenerator($this)
            ->setElement($source)
            ->setDownloadFormat($config->getFormat());
    }

    /**
     * @param DownloadableStructuredData $downloadable
     *
     * @return GenerationResult|null
     */
    public function generate(DownloadableInterface $downloadable): ?GenerationResult
    {
        if ($downloadFormat = $this->downloadFormatHandler->getDownloadFormatService($downloadable->getDownloadFormat())) {
            $downloadFormat->add($downloadable->getDownloadUniqid(), $downloadable->getElement());
        }

        return null;
    }

    /**
     * @param DownloadFormatHandler $downloadFormatHandler
     * @required;
     */
    public function setDownloadFormatHandler(DownloadFormatHandler $downloadFormatHandler)
    {
        $this->downloadFormatHandler = $downloadFormatHandler;
    }
}
