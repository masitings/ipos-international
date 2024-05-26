/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment} from "react";
import {connect} from "react-redux";
import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";
import Trans from "~portal-engine/scripts/components/Trans";
import {ReactComponent as TrashIcon} from "~portal-engine/icons/trash-alt";
import {useConfirmModal} from "~portal-engine/scripts/components/modals/ConfirmModal";
import {getSelectedFolderPath} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";
import {deleteSubfolder} from "~portal-engine/scripts/features/subfolder/subfolder-api";
import {showError} from "~portal-engine/scripts/utils/general";

export const mapStateToProps = (state) => ({
    currentFolder: getSelectedFolderPath(state)
});

export function DeleteSubfolderButton(props) {
    const {
        elementType,
        currentFolder,
        btnProps = {}
    } = props;

    const {confirm, confirmModal} = useConfirmModal(() => {
        deleteSubfolder(elementType, currentFolder)
            .then(({data}) => {
                window.location = data.url;
            })
            .catch(() => {
                showError();
            })
    }, {
        title: <Trans t="delete-subfolder.title"/>,
        message: <Trans t="delete-subfolder.text"/>,
        cancelText: <Trans t="delete-subfolder.cancel"/>,
        confirmText: <Trans t="delete-subfolder.confirm"/>,
        confirmStyle: "danger",
    });

    return (
        <Fragment>
            <ButtonWithIcon
                type="button"
                variant={"primary"}
                {...btnProps}
                Icon={<TrashIcon width="16" height="16"/>}
                onClick={confirm}
            >
                <Trans t="delete-subfolder"/>
            </ButtonWithIcon>

            {confirmModal}
        </Fragment>
    );
}

export default connect(mapStateToProps)(DeleteSubfolderButton);