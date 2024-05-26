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
import {
    cancelPublicShareDownload,
    downloadPublicShareByTmpStoreKey
} from "~portal-engine/scripts/features/download/download-actions";
import DownloadMessageModal
    from "~portal-engine/scripts/features/download/components/DownloadConfig/DownloadMessageModal";
import {
    getPublicShareDownloadMessageText,
    getPublicShareDownloadMessageTmpStoreKey,
    getPublicShareDownloadMessageType,
} from "~portal-engine/scripts/features/download/download-selectors";

export function mapStateToProps(state) {
    return {
        isOpen: !!getPublicShareDownloadMessageType(state.download),
        type: getPublicShareDownloadMessageType(state.download),
        text: getPublicShareDownloadMessageText(state.download),
        tmpStoreKey: getPublicShareDownloadMessageTmpStoreKey(state.download)
    }
}

export const mapDispatchToProps = {
    onConfirm: downloadPublicShareByTmpStoreKey,
    onCancel: cancelPublicShareDownload,
};

export default connect(mapStateToProps, mapDispatchToProps)(DownloadMessageModal);