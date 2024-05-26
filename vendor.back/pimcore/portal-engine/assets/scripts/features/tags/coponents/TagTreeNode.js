/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment} from 'react';
import {connect} from "react-redux";
import {noop} from "~portal-engine/scripts/utils/utils";
import Checkbox from "~portal-engine/scripts/components/Checkbox";
import {ReactComponent as TagIcon} from "~portal-engine/icons/tag";
import {ReactComponent as AngleIcon} from "~portal-engine/icons/angle-down";
import {getChildTagIdsById, getTagById, isTagOpenById} from "~portal-engine/scripts/features/selectors";
import {Collapse} from "react-collapse";
import {toggleTagCollapseState} from "~portal-engine/scripts/features/tags/tags-actions";

export function TreeNodeTag({
    id,
    name,
    isOpen = false,
    selectedIds = [],
    hasChildren = false,
    children = [],
    level = 0,
    onCollapseToggle = noop,
    onSelectionChange = noop,
    SubTreeComponent
}) {
    let isSelected = selectedIds.includes(id);

    return (
        <Fragment>
            <div className={`tree__node tree__item__collapse tree__node--tag ${hasChildren && isOpen ? 'is-open' : ''}`}>
                <span className={`tree__item__collapse__icon mr-2 ${hasChildren ? '' : 'invisible'}`}>
                     <button type="button"
                             className="btn btn-no-styling increased-area--2"
                             disabled={!hasChildren}
                             onClick={() => {
                                 onCollapseToggle({id, state: !isOpen});
                             }}>
                         <AngleIcon width="10"/>
                     </button>
                 </span>

                <Checkbox
                    checked={isSelected}
                    label={
                        <div className="row row-gutter--1">
                            <div className="col col-auto">
                                <TagIcon className="text-muted" width="14" height="14"/>
                            </div>
                            <div className="col">
                                {name}
                            </div>
                        </div>}
                    onChange={() => onSelectionChange({id, state: !isSelected})}/>
            </div>

            {children && children.length ? (
                <Collapse theme={{collapse: 'collapse-container', content: ``}}
                          isOpened={isOpen}>
                    <SubTreeComponent parentId={id}
                                      level={level + 1}
                                      selectedIds={selectedIds}
                                      onSelectionChange={onSelectionChange}/>
                </Collapse>
            ) : null}
        </Fragment>
    );
}

function mapStateToProps(state, {id}) {
    let tag = getTagById(state, id);
    let children = getChildTagIdsById(state, id);

    return {
        ...tag,
        hasChildren: children.length,
        children: children,
        isOpen: isTagOpenById(state, id),
    }
}

export const mapDispatchToProps = {
    onCollapseToggle: toggleTagCollapseState,
};

export default connect(mapStateToProps, mapDispatchToProps)(TreeNodeTag)