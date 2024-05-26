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
import ActionBar, {defaultActionConfig} from "~portal-engine/scripts/components/actions/ActionBar";
import {
    publicShareDownloadClicked
} from "~portal-engine/scripts/features/download/download-actions";
import {NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import {Dropdown} from "react-bootstrap";
import Trans from "~portal-engine/scripts/components/Trans";
import {ReactComponent as DownloadIcon} from "~portal-engine/icons/download";
import {addParamsObjectToURL, noop} from "~portal-engine/scripts/utils/utils";
import {useFetch} from "~portal-engine/scripts/utils/hooks";
import PublicShareDownloadMessageModal
    from "~portal-engine/scripts/features/download/components/DownloadConfig/PublicShareDownloadMessageModal";

export const mapDispatchToProps = (dispatch) => ({
    onDownload: (dataPoolId) => dispatch(publicShareDownloadClicked({
        dataPoolId,
    }))
});

export function PublicShareListActions({
    publicShareHash,
    onDownload = noop
}) {
    let {
        status: actionsFetchingState,
        payload: actionsPayload,
    } = useFetch(addParamsObjectToURL('/_portal-engine/api/public-share/detail-actions', {
        publicShareHash
    }));

    let actions = (actionsPayload && actionsPayload.data && actionsPayload.data.actions) || {};

    if (actionsFetchingState === NOT_ASKED || !actions) {
        return null;
    }

    let actionHandler = {};
    if (actions.download && actions.download.length) {
        actionHandler.onDownload = () => onDownload(actions.download[0].dataPoolId)
    }

    let transformedActions = (actions.download && actions.download.length > 1)
        ? defaultActionConfig.map(action => action.id === 'download'
            ? {
                ...action,
                Component: () => (
                    <Dropdown className={'action-bar__item'}>
                        <Dropdown.Toggle className={`btn icon-btn action-bar__button`}>
                        <span className="action-bar__item__title text-nowrap">
                            <Trans t="download" domain="action-bar"/>
                        </span>
                            <DownloadIcon className="icon-btn__icon"/>
                        </Dropdown.Toggle>
                        <Dropdown.Menu>
                            {actions.download.map(item => (
                                <Dropdown.Item key={item.dataPoolId}
                                               onClick={() => onDownload(item.dataPoolId)}>{item.name}</Dropdown.Item>
                            ))}
                        </Dropdown.Menu>
                    </Dropdown>
                )
            }
            : action
        )
        : defaultActionConfig
    ;

    return (
        <Fragment>
            <ActionBar actions={transformedActions} actionHandler={actionHandler}/>

            <PublicShareDownloadMessageModal/>
        </Fragment>
    )
}

export default connect(null, mapDispatchToProps)(PublicShareListActions);