/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment, useState} from "react";
import {connect} from "react-redux";
import Trans from "~portal-engine/scripts/components/Trans";
import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";
import {ReactComponent as SaveIcon} from "~portal-engine/icons/save";
import {usePromptModal} from "~portal-engine/scripts/components/modals/PromptModal";
import {saveSearch} from "~portal-engine/scripts/features/search/search-actions";

export function SaveSearchButton({dispatch}) {
    const [urlQuery, setUrlQuery] = useState(null);

    const {prompt, promptModal} = usePromptModal((name) => dispatch(saveSearch({urlQuery, name})), {
        title: <Trans t="search.save-prompt.title"/>,
        label: <Trans t="search.save-prompt.label"/>,
        cancelText: <Trans t="search.save-prompt.cancel"/>,
        confirmText: <Trans t="search.save-prompt.confirm"/>,
    });

    return (
        <Fragment>
            <ButtonWithIcon type="button"
                            variant="primary"
                            Icon={<SaveIcon width="16" height="16"/>}
                            onClick={() => {
                                setUrlQuery(window.location.search);
                                prompt()
                            }}>
                <Trans t={'search.save-modal.cta'}/>
            </ButtonWithIcon>

            {promptModal}
        </Fragment>
    )
}

export default connect()(SaveSearchButton);