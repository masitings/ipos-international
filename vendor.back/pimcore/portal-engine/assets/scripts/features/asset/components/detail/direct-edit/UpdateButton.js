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
import {updateFromDirectEdit} from "~portal-engine/scripts/features/asset/asset-actions";
import Trans from "~portal-engine/scripts/components/Trans";
import {ReactComponent as Icon} from "~portal-engine/icons/arrow-alt-circle-up";
import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";

export const mapDispatchToProps = (dispatch) => ({
    update: () => dispatch(updateFromDirectEdit())
});

export function UpdateButton(props) {
    const {
        update
    } = props;

    return (
        <ButtonWithIcon variant={"primary"} onClick={update} Icon={<Icon height={16}/>}>
            <Trans t={"direct-edit.update"} domain={"asset"}/>
        </ButtonWithIcon>
    );
}

export default connect(null, mapDispatchToProps)(UpdateButton);