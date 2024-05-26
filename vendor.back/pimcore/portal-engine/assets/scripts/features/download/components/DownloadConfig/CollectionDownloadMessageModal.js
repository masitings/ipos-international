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
    cancelCollectionDownload,
    downloadCollection
} from "~portal-engine/scripts/features/download/download-actions";
import DownloadMessageModal
    from "~portal-engine/scripts/features/download/components/DownloadConfig/DownloadMessageModal";
import {
    getCollectionDownloadMessageText, getCollectionDownloadMessageTmpStoreKey,
    getCollectionDownloadMessageType,
} from "~portal-engine/scripts/features/download/download-selectors";

export function mapStateToProps(state) {
    return {
        isOpen: !!getCollectionDownloadMessageType(state.download),
        type: getCollectionDownloadMessageType(state.download),
        text: getCollectionDownloadMessageText(state.download),
        tmpStoreKey: getCollectionDownloadMessageTmpStoreKey(state.download)
    }
}

export const mapDispatchToProps = {
    onConfirm: downloadCollection,
    onCancel: cancelCollectionDownload,
};

export default connect(mapStateToProps, mapDispatchToProps)(DownloadMessageModal);