/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from 'react';
import {FILE_SIZE_TO_BIG, FILE_SIZE_WARNING} from "~portal-engine/scripts/consts/download-message-types";
import ConfirmModal from "~portal-engine/scripts/components/modals/ConfirmModal";

export default function DownloadMessageModal({isOpen, text, type, onCancel, onConfirm, tmpStoreKey}) {
    let showConfirm = type !== FILE_SIZE_TO_BIG;

    return (
        <ConfirmModal title={getTitleTranslationKeyByType(type)}
                      isOpen={isOpen}
                      cancelText={getCancelTranslationKeyByType(type)}
                      onCancel={() => onCancel(tmpStoreKey)}
                      {...(showConfirm ? ({
                          onConfirm: () => onConfirm({tmpStoreKey}),
                          confirmText: getConfirmTranslationKeyByType(type)
                      }) : null)}>
            {text}
        </ConfirmModal>
    )
}

const modeTranslationKeyByType = {
    [FILE_SIZE_TO_BIG]: 'download.filesize-too-big',
    [FILE_SIZE_WARNING]: 'download.filesize-warning',
};

const getTitleTranslationKeyByType = (type) => `${modeTranslationKeyByType[type]}.title`;
const getConfirmTranslationKeyByType = (type) => `${modeTranslationKeyByType[type]}.confirm`;
const getCancelTranslationKeyByType = (mode) => `${modeTranslationKeyByType[mode]}.cancel`;
