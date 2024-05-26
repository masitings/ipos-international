/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useEffect, Fragment} from "react";
import {connect} from "react-redux";
import Card from "~portal-engine/scripts/components/Card";
import Trans from "~portal-engine/scripts/components/Trans";
import {
    getWorkflowData,
    getWorkflowFetchingState,
    getWorkflowStatusInfo,
    getWorkflowPanelCollapsed
} from "~portal-engine/scripts/features/asset/asset-selectors";
import {
    applyWorkflowTransition,
    fetchWorkflow, openWorkflowHistoryModal,
    openWorkflowTransitionModal
} from "~portal-engine/scripts/features/asset/asset-actions";
import {NOT_ASKED, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import WorkflowModal from "~portal-engine/scripts/features/asset/components/detail/workflow/WorkflowModal";
import WorkflowHistoryModal from "~portal-engine/scripts/features/asset/components/detail/workflow/WorkflowHistoryModal";

export const mapStateToProps = (state) => ({
    workflowFetchingState: getWorkflowFetchingState(state),
    workflow: getWorkflowData(state),
    statusInfo: getWorkflowStatusInfo(state),
    workflowPanelCollapsed: getWorkflowPanelCollapsed(state)
});

export const mapDispatchToProps = (dispatch) => ({
    fetchWorkflow: () => dispatch(fetchWorkflow()),
    applyWorkflowTransition: (payload) => {
        if(payload.transition.notes && payload.transition.notes.commentEnabled) {
            dispatch(openWorkflowTransitionModal(payload));
        } else {
            dispatch(applyWorkflowTransition(payload))
        }
    },
    showHistory: () => dispatch(openWorkflowHistoryModal())
});

export function Workflow({workflowFetchingState, fetchWorkflow, workflow, statusInfo, applyWorkflowTransition, showHistory, workflowPanelCollapsed, className=''}) {

    useEffect(() => {
        if (workflowFetchingState === NOT_ASKED) {
            fetchWorkflow();
        }
    }, [workflowFetchingState]);

    if (workflowFetchingState !== SUCCESS) {
        return (
            <LoadingIndicator className="my-4"/>
        );
    }

    if(workflow.length < 1) {
        return null;
    }

    return (
        <Fragment>
            <Card
                title={(<Trans t="workflow" domain="asset"/>)}
                id="Workflow"
                className={className}
                collapsed={workflowPanelCollapsed}
            >
                <div>
                    <div className={`workflow-label`}>
                        <Trans t="current-state" domain="workflow"/>:
                    </div>
                    <div>
                        <div dangerouslySetInnerHTML={{__html: statusInfo}}></div>
                    </div>
                </div>


                {workflow.map(workflowItem => (

                    <div className={`mt-2`} key={workflowItem.name}>
                        <div className={`workflow-label`}>
                            { workflow.length > 1 ? <Trans t={workflowItem.label} domain="workflow-label"/> : null }
                            { workflow.length > 1 ? (<span>&nbsp;</span>) : null }

                            <Trans t="actions" domain="workflow"/>:
                        </div>

                        <div>
                            {workflowItem.allowedTransitions.map(transition => (
                                <button key={transition.name} className={`btn btn-sm btn-gray btn-workflow-action`} onClick={() => applyWorkflowTransition({workflow:workflowItem, transition, type:"transition"})}>
                                    <Trans t={transition.label} domain="workflow-transition"/>
                                </button>
                            ))}
                            {workflowItem.globalActions.map(globalAction => (
                                <button key={globalAction.name} className={`btn btn-sm btn-gray btn-workflow-action`} onClick={() => applyWorkflowTransition({workflow:workflowItem,transition: globalAction, type:"globalAction"})}>
                                    <Trans t={globalAction.label} domain="workflow-transition"/>
                                </button>
                            ))}
                        </div>
                    </div>
                ))}

                <hr/>

                <a href="#" className="clickable" onClick={(event) => {event.preventDefault(); showHistory()}}>
                    <u><Trans t="show-history" domain="workflow"/></u>
                </a>
            </Card>

            <WorkflowModal/>
            <WorkflowHistoryModal/>
        </Fragment>
    );
}

export default connect(mapStateToProps, mapDispatchToProps)(Workflow);