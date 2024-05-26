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
import {ReactComponent as AngleIcon} from "~portal-engine/icons/angle-down";
import {
    getAllChildFolderPathsByPath,
    getFolderByPath,
    isFolderOpenByPath
} from "~portal-engine/scripts/features/selectors";
import {UnmountClosed as Collapse} from "react-collapse";
import {ReactComponent as FolderIcon} from "~portal-engine/icons/folder-open";
import {toggleFolderCollapseState} from "~portal-engine/scripts/features/folders/folders-actions";

export function TreeNodeFolder({
    path,
    name,
    isOpen = false,
    hasChildren = false,
    level = 0,
    selectedPath,
    onCollapseToggle = noop,
    onSelectionChange = noop,
    SubTreeComponent
}) {
    let isSelected = selectedPath === path;

    return (
        <Fragment>
            <div className={`tree__node tree__item__collapse tree__node--folder ${hasChildren && isOpen ? 'is-open' : ''} ${isSelected ? 'is-selected' : ''}`}>
                <span className={`tree__item__collapse__icon ${hasChildren ? '': 'invisible'}`}>
                     <button type="button" className="btn btn-no-styling increased-area--2" onClick={() => {
                         onCollapseToggle({path, state: !isOpen});
                     }} disabled={!hasChildren}>
                         <AngleIcon width="10"/>
                     </button>
                 </span>

                <button type="button" className="btn btn-no-styling"
                        onClick={() => {onSelectionChange({path})}}
                        onDoubleClick={() => onCollapseToggle({path, state: !isOpen})}>
                    <span className="row row-gutter--1">
                        <span className="col-auto">
                            <FolderIcon className="text-primary" width="25"/>
                        </span>
                        <span className="col">
                            {name}
                        </span>
                    </span>
                </button>
            </div>

            {hasChildren ? (
                <Collapse theme={{collapse: 'collapse-container', content: ``}}
                          isOpened={isOpen}>
                    <SubTreeComponent parentPath={path}
                                      level={level + 1}
                                      selectedPath={selectedPath}
                                      onSelectionChange={onSelectionChange}/>
                </Collapse>
            ) : null}
        </Fragment>
    );
}


export function mapStateToProps(state, {path}) {
    let folder = getFolderByPath(state, path);

    return {
        ...folder,
        children: getAllChildFolderPathsByPath(state, folder),
        isOpen: isFolderOpenByPath(state, path),
    }
}

export const mapDispatchToProps = {
    onCollapseToggle: toggleFolderCollapseState,
};


export default connect(mapStateToProps, mapDispatchToProps)(TreeNodeFolder)