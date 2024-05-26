/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useEffect} from 'react';
import {connect} from "react-redux";
import {
    getAllChildFolderPathsByPath, getChildFolderPageByPath,
    getChildFolderPageFetchingState, hasMoreChildFoldersByPath,
} from "~portal-engine/scripts/features/selectors";
import {requestFolderChildrenPage} from "~portal-engine/scripts/features/folders/folders-actions";
import {FETCHING, NOT_ASKED} from "~portal-engine/scripts/consts/fetchingStates";
import LoadingIndicator from "~portal-engine/scripts/components/LoadingIndicator";
import FolderTreeNode from "~portal-engine/scripts/features/folders/components/FolderTreeNode";
import Trans from "~portal-engine/scripts/components/Trans";
import {noop} from "~portal-engine/scripts/utils/utils";

export const mapStateToProps = (state, {parentPath = null}) => {
    let currentPage = getChildFolderPageByPath(state, parentPath);

    return ({
        parentPath,
        currentPage,
        paths: getAllChildFolderPathsByPath(state, parentPath),
        hasMoreChildren: hasMoreChildFoldersByPath(state, parentPath),
        firstPageFetchingState: getChildFolderPageFetchingState(state, parentPath, 1),
        currentPageFetchingState: getChildFolderPageFetchingState(state, parentPath, currentPage)
    });
};


const Tree = connect(mapStateToProps)(props => {
    const {
        paths = [],
        level = 0,
        firstPageFetchingState = NOT_ASKED,
        currentPageFetchingState = NOT_ASKED,
        parentPath,
        selectedPath,
        currentPage = 1,
        hasMoreChildren = true,
        onSelectionChange = noop,
        dispatch
    } = props;
    
    useEffect(function () {
        if (firstPageFetchingState === NOT_ASKED) {
            dispatch(requestFolderChildrenPage(parentPath));
        }
    }, []);

    const loadMoreHandler = () => {
        dispatch(requestFolderChildrenPage(parentPath, currentPage + 1));
    };

    return (
        (firstPageFetchingState === NOT_ASKED || firstPageFetchingState === FETCHING)
            ? (<LoadingIndicator className="my-3" size="sm" showText={false}/>)
            : (
                <ol className={`list-unstyled tree tree--level-${level}`}>
                    {paths.map((path) => {
                        return <li className={`tree__item`} key={path}>
                            <FolderTreeNode path={path}
                                            level={level}
                                            selectedPath={selectedPath}
                                            onSelectionChange={onSelectionChange}
                                            SubTreeComponent={Tree}
                            />
                        </li>
                    })}

                    {currentPageFetchingState === FETCHING
                        ? (
                            <LoadingIndicator className="mt-2 mb-3" size="sm" showText={false}/>
                        ) : (
                            hasMoreChildren ? (
                                <li className={`tree__item`}>
                                    <button type="button"
                                            className="btn btn-secondary btn-sm btn-block my-2"
                                            onClick={loadMoreHandler}>
                                        <Trans t="folders.load-more"/>
                                    </button>
                                </li>
                            ) : null
                        )}
                </ol>
            )
    )
});

export default Tree;