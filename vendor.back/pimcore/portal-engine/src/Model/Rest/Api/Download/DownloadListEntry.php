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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\Download;

use Pimcore\Bundle\PortalEngineBundle\Model\BasicJsonModel;

class DownloadListEntry extends BasicJsonModel
{
    public function __construct()
    {
        parent::__construct([
            'id' => null,
            'dataPoolId' => null,
            'dataPoolName' => null,
            'name' => null,
            'thumbnail' => null,
            'detailLink' => null,
            'configs' => [],
            'messages' => []
        ]);
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->get('id');
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId(?string $id)
    {
        $this->set('id', $id);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDataPoolId(): ?int
    {
        return $this->get('dataPoolId');
    }

    /**
     * @param int $dataPoolId
     *
     * @return $this
     */
    public function setDataPoolId(?int $dataPoolId)
    {
        $this->set('dataPoolId', $dataPoolId);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDataPoolName(): ?string
    {
        return $this->get('dataPoolName');
    }

    /**
     * @param string|null $dataPoolName
     *
     * @return $this
     */
    public function setDataPoolName(?string $dataPoolName)
    {
        $this->set('dataPoolName', $dataPoolName);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->get('name');
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setName(?string $type)
    {
        $this->set('name', $type);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getThumbnail(): ?string
    {
        return $this->get('thumbnail');
    }

    /**
     * @param string $thumbnail
     *
     * @return $this
     */
    public function setThumbnail(?string $thumbnail)
    {
        $this->set('thumbnail', $thumbnail);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDetailLink(): ?string
    {
        return $this->get('detailLink');
    }

    /**
     * @param string $detailLink
     *
     * @return $this
     */
    public function setDetailLink(?string $detailLink)
    {
        $this->set('detailLink', $detailLink);

        return $this;
    }

    /**
     * @return array[]
     */
    public function getConfigs(): array
    {
        return $this->get('configs', []);
    }

    /**
     * @param array[] $configs
     *
     * @return $this
     */
    public function setConfigs(array $configs)
    {
        $this->set('configs', $configs);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->get('messages', []);
    }

    /**
     * @param string[] $messages
     *
     * @return $this
     */
    public function setMessages(array $messages)
    {
        $this->set('messages', $messages);

        return $this;
    }
}
