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
import {connect} from "react-redux";
import {cancelMultiDownload, downloadMultipleByTmpStoreKey} from "~portal-engine/scripts/features/download/download-actions";
import DownloadMessageModal
    from "~portal-engine/scripts/features/download/components/DownloadConfig/DownloadMessageModal";
import {
    getMultiDownloadMessageText, getMultiDownloadMessageTmpStoreKey,
    getMultiDownloadMessageType
} from "~portal-engine/scripts/features/download/download-selectors";

export function mapStateToProps(state) {
    return {
        isOpen: !!getMultiDownloadMessageType(state.download),
        type: getMultiDownloadMessageType(state.download),
        text: getMultiDownloadMessageText(state.download),
        tmpStoreKey: getMultiDownloadMessageTmpStoreKey(state.download)
    }
}

export const mapDispatchToProps = {
    onConfirm: downloadMultipleByTmpStoreKey,
    onCancel: cancelMultiDownload,
};

export default connect(mapStateToProps, mapDispatchToProps)(DownloadMessageModal);