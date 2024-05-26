/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";
import {ReactComponent as TrashIcon} from "~portal-engine/icons/trash-alt";
import React, {Fragment} from "react";
import Trans from "~portal-engine/scripts/components/Trans";
import {useConfirmModal} from "~portal-engine/scripts/components/modals/ConfirmModal";
import {noop} from "~portal-engine/scripts/utils/utils";
import {clearCart,} from "~portal-engine/scripts/features/download/download-actions";
import {connect} from "react-redux";

export function DownloadClearCartButton({
    onClick = noop,
    ...props
}) {
    const {confirm, confirmModal} = useConfirmModal(onClick, {
        title: <Trans t="download.clear-cart.confirm.title"/>,
        message: <Trans t="download.clear-cart.confirm.text"/>,
        cancelText: <Trans t="download.clear-cart.confirm.cancel"/>,
        confirmText: <Trans t="download.clear-cart.confirm.cta"/>,
        confirmStyle: "danger",
    });

    return (
        <Fragment>
            <ButtonWithIcon type="button"
                            variant="danger"
                            Icon={<TrashIcon width="16" height="16"/>}
                            onClick={confirm}
                            {...props}>
                <Trans t="download.clear-cart.cta"/>
            </ButtonWithIcon>

            {confirmModal}
        </Fragment>
    );
}

export const mapDispatchToProps = {
    onClick: clearCart
};

export default connect(null, mapDispatchToProps)(DownloadClearCartButton);