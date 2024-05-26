/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useState} from "react";
import {connect} from "react-redux";
import {getLanguage} from "~portal-engine/scripts/utils/intl";
import {noop} from "~portal-engine/scripts/utils/utils";
import {getSelectedVersionIds} from "~portal-engine/scripts/features/asset/asset-selectors";
import {toggleVersionSelection, publishVersion} from "~portal-engine/scripts/features/asset/asset-actions";
import {ReactComponent as CheckIcon} from "~portal-engine/icons/check";
import {ReactComponent as VisibleIcon} from "~portal-engine/icons/eye";
import {Tooltip, OverlayTrigger} from "react-bootstrap";
import Trans from "~portal-engine/scripts/components/Trans";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";

export const mapStateToProps = (state, {version}) => ({
    isSelected: getSelectedVersionIds(state).includes(version.id)
});

export const mapDispatchToProps = (dispatch, {version}) => ({
    toggleSelection: (isSelected) => {
        dispatch(toggleVersionSelection({id: version.id, isSelected: !isSelected}));
    },
    publishVersion: () => dispatch(publishVersion({id: version.id}))
});

const mergeProps = (stateProps, dispatchProps, props) => ({
    ...props,
    ...stateProps,
    ...dispatchProps,
    toggleSelection: (event) => {
        event.preventDefault();
        event.stopPropagation();

        dispatchProps.toggleSelection(stateProps.isSelected);
    }
});

export function VersionHistoryRow({isSelected, version, toggleSelection, publishVersion, allowPublish}) {
    const [loading, setLoading] = useState(false);
    const language = getLanguage();

    return (
        <tr onClick={toggleSelection}>
            <td className="align-middle" width="20%">{version.id}</td>
            <td className="align-middle" width="30%">{version.note}</td>
            <td className="align-middle" width="20%">{version.date}</td>
            <td className="text-center align-middle">
                {version.published &&
                    <CheckIcon height={12}/>
                }
            </td>
            {allowPublish &&
                <td width="10%" className="text-center align-middle">
                    {!version.published &&
                    <OverlayTrigger placement="right"
                                    overlay={<Tooltip><Trans t="publish-version" domain="asset"/></Tooltip>}>
                        {loading ? (
                            <LoadingIndicator size="inline" showText={false}/>
                        ) : (
                            <button
                                type="button"
                                className="btn icon-btn"
                                onClick={(event) => {
                                    event.stopPropagation();
                                    publishVersion().finally(() => setLoading(false));
                                    setLoading(true);
                                }}
                            >
                                <VisibleIcon height={12} className="icon-btn__icon"/>
                            </button>
                        )
                        }
                    </OverlayTrigger>
                    }
                </td>
            }
            <td width="10%" className="text-center align-middle">
                <input type="checkbox" checked={isSelected} onChange={noop} onClick={toggleSelection} style={{pointerEvents: "none"}}/>
            </td>
        </tr>
    );
}

export default connect(mapStateToProps, mapDispatchToProps, mergeProps)(VersionHistoryRow);