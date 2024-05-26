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
    getCartDownloadMessageText, getCartDownloadMessageTmpStoreKey, getCartDownloadMessageType,
} from "~portal-engine/scripts/features/download/download-selectors";
import {cancelCartDownload, downloadCart} from "~portal-engine/scripts/features/download/download-actions";
import DownloadMessageModal
    from "~portal-engine/scripts/features/download/components/DownloadConfig/DownloadMessageModal";

export function mapStateToProps(state) {
    return {
        isOpen: !!getCartDownloadMessageType(state.download),
        type: getCartDownloadMessageType(state.download),
        text: getCartDownloadMessageText(state.download),
        tmpStoreKey: getCartDownloadMessageTmpStoreKey(state.download)
    }
}

export const mapDispatchToProps = {
    onConfirm: downloadCart,
    onCancel: cancelCartDownload,
};

export default connect(mapStateToProps, mapDispatchToProps)(DownloadMessageModal);