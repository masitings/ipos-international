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
import {ReactComponent as PlusIcon} from "~portal-engine/icons/plus";

export const mapDispatchToProps = (dispatch) => ({
    openModal: () => dispatch(openSubfolderModal({modalOpen: "create"}))
});

export function CreateSubfolderButton(props) {
    const {
        openModal,
        btnProps = {}
    } = props;

    return (
        <ButtonWithIcon
            type="button"
            variant={"primary"}
            {...btnProps}
            Icon={<PlusIcon width="16" height="16"/>}
            onClick={openModal}
        >
            <Trans t="create-subfolder"/>
        </ButtonWithIcon>
    );
}

export default connect(null, mapDispatchToProps)(CreateSubfolderButton);