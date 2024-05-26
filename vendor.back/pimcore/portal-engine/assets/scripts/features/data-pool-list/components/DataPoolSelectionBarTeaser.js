/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {connect} from "react-redux";
import {mapStateToProps as dataPoolTeaserMapStateToProps} from "~portal-engine/scripts/features/data-pool-list/components/DataPoolTeaser";
import Teaser from "~portal-engine/scripts/components/Teaser";
import {toggleSelection} from "~portal-engine/scripts/features/data-pool-list/data-pool-list-actions";
import {getConfig} from "~portal-engine/scripts/utils/general";

export const mapStateToProps = (state, props) => ({
    className: `teaser--shadow-sm ${props.className}`,
    size: 'sm',
    actionHandler: {},
    ...dataPoolTeaserMapStateToProps(state, props)
});

export const mapDispatchToProps = (dispatch, {
    id,
}) => {
    return {
        onSelectedToggle: (isSelected) => dispatch(toggleSelection({
            id,
            isSelected,
            dataPoolId: getConfig("currentDataPool.id"),
            collectionId: getConfig("collection.id")
        }))
    }
};


export default connect(mapStateToProps, mapDispatchToProps)(Teaser)