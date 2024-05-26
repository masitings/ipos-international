/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {ReactComponent as UploadIcon} from "~portal-engine/icons/arrow-alt-circle-up";
import Trans from "~portal-engine/scripts/components/Trans";
import React, {useCallback} from "react";
import {modalOpened} from "~portal-engine/scripts/features/upload/upload-actions";
import {noop} from "~portal-engine/scripts/utils/utils";
import {connect} from "react-redux";
import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";

export function UploadButton({btnProps = {}, onClick = noop}) {
    const handleClick = useCallback(() => onClick());

    return (
        <ButtonWithIcon type="button"
                        variant={"primary"}
                        {...btnProps}
                        Icon={<UploadIcon width="16" height="16"/>}
                        onClick={handleClick}>
            <Trans t="upload.cta"/>
        </ButtonWithIcon>
    )
}

export const mapDispatchToProps = {
    onClick: modalOpened
};

export default connect(null, mapDispatchToProps)(UploadButton)