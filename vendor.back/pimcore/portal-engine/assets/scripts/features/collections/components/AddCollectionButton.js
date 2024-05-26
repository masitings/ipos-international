/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";
import {ReactComponent as PlusIcon} from "~portal-engine/icons/plus";
import Trans from "~portal-engine/scripts/components/Trans";
import React, {Fragment} from "react";
import {connect} from "react-redux";
import {usePromptModal} from "~portal-engine/scripts/components/modals/PromptModal";
import {createCollection} from "~portal-engine/scripts/features/collections/collections-actions";

export function AddCollectionButton({dispatch}) {
    const {prompt, promptModal} = usePromptModal((name) => dispatch(createCollection({name})), {
        title: <Trans t="collection.create-prompt.title"/>,
        label: <Trans t="collection.create-prompt.label"/>,
        cancelText: <Trans t="collection.create-prompt.cancel"/>,
        confirmText: <Trans t="collection.create-prompt.confirm"/>,
    });

    return (
        <Fragment>
            <ButtonWithIcon type="button"
                            variant="primary"
                            Icon={<PlusIcon width="16" height="16"/>}
                            onClick={() => prompt()}>
                <Trans t={'collection.create'}/>
            </ButtonWithIcon>

            {promptModal}
        </Fragment>
    )
}

export default connect()(AddCollectionButton);