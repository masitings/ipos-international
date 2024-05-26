/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useEffect} from "react";
import {connect} from "react-redux";
import {getAssetId, getDirectEditStatus} from "~portal-engine/scripts/features/asset/asset-selectors";
import {cancelDirectEdit, updateDirectEditStatus} from "~portal-engine/scripts/features/asset/asset-actions";
import {ERROR, LISTENING, UPDATED, UPDATING} from "~portal-engine/scripts/consts/direct-edit-status";
import Trans from "~portal-engine/scripts/components/Trans";
import {getConfig} from "~portal-engine/scripts/utils/general";
import UpdateButton from "~portal-engine/scripts/features/asset/components/detail/direct-edit/UpdateButton";
import CancelButton from "~portal-engine/scripts/features/asset/components/detail/direct-edit/CancelButton";

export const mapStateToProps = (state) => ({
    directEditStatus: getDirectEditStatus(state)
});

export const mapDispatchToProps = (dispatch) => ({
    cancel: () => dispatch(cancelDirectEdit()),
    updated: (status, message) => dispatch(updateDirectEditStatus({status: status, message: message}))
});

export function DirectEditStatus(props) {
    const {
        directEditStatus,
        updated = () => {
        }
    } = props;

    useEffect(() => {

        const mercureUrl = getConfig("directEdit.mercureUrl");
        if(mercureUrl) {
            const url = new URL(mercureUrl);
            url.searchParams.append("topic", 'http://www.pimcore.com/direct-edit/client-upload/user/' + getConfig("directEdit.userId"));

            const eventSource = new EventSource(url);

            eventSource.onmessage = event => {
                const message = JSON.parse(event.data);

                if(message.successBtnActive) {
                    updated(UPDATED, message);
                } else {
                    updated(LISTENING, message);
                }
            }

            eventSource.onopen = event => {};
            eventSource.onabort = event => {};
            eventSource.onerror = event => {};
        }
    }, []);

    if (!directEditStatus) {
        return null;
    }

    let color = "light";
    let translationKey = "direct-edit.listening";
    let buttons = [(<CancelButton key={"cancel"}/>)];

    switch (directEditStatus) {
        case UPDATED:
            color = "info";
            translationKey = "direct-edit.updated";
            buttons.push((<UpdateButton key={"update"}/>));
            break;

        case UPDATING:
            translationKey = "direct-edit.updating";
            color = "info";
            buttons = [];
            break;

        case ERROR:
            color = "warning";
            translationKey = "direct-edit.could-not-open-direc-edit";
            break;
    }

    return (
        <div className={`alert alert-${color}`}>
            <div className={"d-flex align-items-center justify-content-between"}>
                <span>
                    <Trans t={translationKey} domain={"asset"}/>
                </span>

                <div className={"d-flex"}>
                    {buttons}
                </div>
            </div>
        </div>
    );
}

export default connect(mapStateToProps, mapDispatchToProps)(DirectEditStatus);