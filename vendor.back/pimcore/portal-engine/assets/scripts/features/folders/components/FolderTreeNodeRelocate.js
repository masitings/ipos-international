/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment, useState} from 'react';
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

export function TreeNodeFolder({
    path,
    name,
    isOpen = false,
    hasChildren = false,
    level = 0,
    selectedPath,
    onSelectionChange = noop,
    SubTreeComponent,
    permissions
}) {
    let isSelected = selectedPath === path;

    const [currentOpen, setCurrentOpen] = useState(isOpen);

    return (
        <Fragment>
            <div className={`tree__node tree__item__collapse tree__node--folder ${hasChildren && isOpen ? 'is-open' : ''} ${currentOpen ? 'is-open' : ''} ${isSelected ? 'is-selected' : ''}`}>
                <span className={`tree__item__collapse__icon ${hasChildren ? '': 'invisible'}`}>
                     <button type="button" className="btn btn-no-styling increased-area--2" onClick={() => {
                         setCurrentOpen(!currentOpen);
                     }} disabled={!hasChildren}>
                         <AngleIcon width="10"/>
                     </button>
                 </span>

                <button type="button" className={`btn btn-no-styling ${permissions && permissions.create === false ? 'disabled' : ''}`}
                        onClick={() => {permissions && permissions.create === true ? onSelectionChange({path}) : setCurrentOpen(true)}}
                        onDoubleClick={() => setCurrentOpen(!currentOpen)}>
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
                          isOpened={currentOpen}>
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

export default connect(mapStateToProps, null)(TreeNodeFolder)