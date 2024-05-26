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
import {cancelDirectEdit} from "~portal-engine/scripts/features/asset/asset-actions";
import Trans from "~portal-engine/scripts/components/Trans";
import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";
import {ReactComponent as Icon} from "~portal-engine/icons/close";

export const mapDispatchToProps = (dispatch) => ({
    cancel: () => dispatch(cancelDirectEdit())
});

export function UpdateButton(props) {
    const {
        cancel = () => {}
    } = props;

    return (
        <ButtonWithIcon variant={"light"} onClick={cancel} className={"mr-2"} Icon={<Icon height={16}/>}>
            <Trans t={"direct-edit.cancel"} domain={"asset"}/>
        </ButtonWithIcon>
    );
}

export default connect(null, mapDispatchToProps)(UpdateButton);