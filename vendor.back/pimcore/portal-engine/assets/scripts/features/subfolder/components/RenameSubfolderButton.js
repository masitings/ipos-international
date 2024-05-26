/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from "react";
import {connect} from "react-redux";
import {openSubfolderModal} from "~portal-engine/scripts/features/subfolder/subfolder-actions";
import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";
import Trans from "~portal-engine/scripts/components/Trans";
import {ReactComponent as EditIcon} from "~portal-engine/icons/edit";
import {getSelectedFolderPath} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-selectors";

export const mapStateToProps = (state) => ({
    currentFolder: getSelectedFolderPath(state)
});

export const mapDispatchToProps = (dispatch) => ({
    openModal: (folder) => dispatch(openSubfolderModal({
        modalOpen: "rename",
        modalState: {
            name: folder
        }
    }))
});

export function RenameSubfolderButton(props) {
    const {
        currentFolder,
        openModal,
        btnProps = {}
    } = props;

    let folder = "";

    if (currentFolder) {
        const folderParts = currentFolder.split("/");

        folder = folderParts[folderParts.length - 1];
    }

    return (
        <ButtonWithIcon
            type="button"
            variant={"primary"}
            {...btnProps}
            Icon={<EditIcon width="16" height="16"/>}
            onClick={() => {
                openModal(folder);
            }}
        >
            <Trans t="rename-subfolder"/>
        </ButtonWithIcon>
    );
}

export default connect(mapStateToProps, mapDispatchToProps)(RenameSubfolderButton);