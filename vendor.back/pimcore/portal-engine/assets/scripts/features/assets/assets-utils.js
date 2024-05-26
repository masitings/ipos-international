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
import Trans from "~portal-engine/scripts/components/Trans";
import {ReactComponent as WarningIcon} from "~portal-engine/icons/exclamation-circle";
import SwitchToUploadFolderButton from "~portal-engine/scripts/features/upload/components/SwitchToUploadFolderButton";
import UploadButton from "~portal-engine/scripts/features/upload/components/UploadButton";
import CreateSubfolderButton from "~portal-engine/scripts/features/subfolder/components/CreateSubfolderButton";
import RenameSubfolderButton from "~portal-engine/scripts/features/subfolder/components/RenameSubfolderButton";
import DeleteSubfolderButton from "~portal-engine/scripts/features/subfolder/components/DeleteSubfolderButton";
import {Dropdown} from "react-bootstrap";
import ClearDropdownToggle from "~portal-engine/scripts/components/ClearDropdownToggle";
import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";
import {ReactComponent as ChevronDown} from "~portal-engine/icons/chevron-down";

export function getDropZoneLabel(uploadPermission) {
    return uploadPermission
        ? <Fragment>
            <Trans t="listing.drop-zone.label"/>
        </Fragment>
        : <Fragment>
            <WarningIcon className="icon-in-text mr-1" height="1em"/>
            <Trans t="listing.drop-zone.label.no-permission"/>
        </Fragment>
}

export function getFilterBarButtons({uploadFolder, fetching, permissions, btnProps}) {
    const buttons = [];

    if (uploadFolder) {
        buttons.push((<SwitchToUploadFolderButton href={uploadFolder} btnProps={btnProps}/>));
    }

    if (!fetching && permissions) {
        if (permissions.create) {
            buttons.push((<UploadButton btnProps={btnProps}/>))
        }

        const dropdownButtons = [];

        if (permissions.subfolder) {
            dropdownButtons.push((<CreateSubfolderButton btnProps={btnProps}/>))
        }

        if (permissions.update) {
            dropdownButtons.push((<RenameSubfolderButton btnProps={btnProps}/>))
        }

        if(permissions.delete) {
            dropdownButtons.push((<DeleteSubfolderButton elementType={"asset"} btnProps={btnProps}/>));
        }

        if (dropdownButtons.length) {
            buttons.push((
                <div className={"filter-bar__dropdown"}>
                    <Dropdown alignRight={true}>
                        <Dropdown.Toggle as={ClearDropdownToggle}>
                            <ButtonWithIcon
                                variant={"primary"}
                                {...btnProps}
                                Icon={<ChevronDown height={16}/>}
                            >
                                <Trans t={"folder"}/>
                            </ButtonWithIcon>
                        </Dropdown.Toggle>

                        <Dropdown.Menu>
                            {dropdownButtons.map((button, i) => (
                                <Dropdown.Item key={i}>
                                    {button}
                                </Dropdown.Item>
                            ))}
                        </Dropdown.Menu>
                    </Dropdown>
                </div>
            ));
        }
    }

    return buttons;
}