/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useState} from 'react';
import {connect} from "react-redux";
import {Modal} from "react-bootstrap";
import {ReactComponent as CloseIcon} from "~portal-engine/icons/close";
import {noop} from "~portal-engine/scripts/utils/utils";
import Trans, {useTranslation} from "~portal-engine/scripts/components/Trans";
import {
    getEditMetaDataModalDataPoolId,
    getEditMetaDataModalIds,
    getEditMetaDataModalState,
    getMetadataEditDataById
} from "~portal-engine/scripts/features/asset/asset-selectors";
import {
    batchMetaData,
    checkMetadata,
    closeEditMetaDataModal
} from "~portal-engine/scripts/features/asset/asset-actions";
import EditMetaData from "~portal-engine/scripts/features/asset/components/metadata/EditMetaData";
import ButtonWithIcon from "~portal-engine/scripts/components/buttons/ButtonWithIcon";
import {ReactComponent as SaveIcon} from "~portal-engine/icons/save";
import TagSelect from "~portal-engine/scripts/features/tags/coponents/TagSelect";
import TagApplyModeSelect from "~portal-engine/scripts/components/tag/TagApplyModeSelect";
import {ADD} from "~portal-engine/scripts/consts/tags-apply-modes";
import Fieldset from "~portal-engine/scripts/components/Fieldset";

export function UpdateModal({
    dataPoolId,
    ids,
    metadata,
    isOpen = false,
    onSubmit = noop,
    validate = noop,
    onClose = noop
}) {
    const [tags, setTags] = useState([]);
    const [tagsApplyMode, setTagsApplyMode] = useState(ADD);
    const closeText = useTranslation('data-pool.update-modal.close');

    const handleClose = () => {
        onClose();
    };

    return (
        <Modal
            show={isOpen}
            onHide={() => handleClose()}
            centered
            size="lg"
        >
            <Modal.Header>
                <Modal.Title>
                    <Trans t='data-pool.asset-metadata-update-modal.title'/>
                </Modal.Title>
                <button type="button"
                        className="close"
                        data-dismiss="modal"
                        aria-label={closeText}
                        onClick={() => handleClose()}>
                    <span aria-hidden="true"><CloseIcon width="22" height="22"/></span>
                </button>
            </Modal.Header>
            <Modal.Body>
                <Fieldset title={<Trans t="tags" domain="asset"/>}>
                    <div className="row">
                        <div className="col-8">
                            <TagSelect onChange={(values) => setTags(values.map(value => value.id))} className="w-100"/>
                        </div>

                        <div className="col-4">
                            <TagApplyModeSelect
                                value={tagsApplyMode}
                                onChange={setTagsApplyMode}
                            />
                        </div>
                    </div>
                </Fieldset>

                <Fieldset title={<Trans t="metadata" domain="asset"/>} className="mt-3">
                    <EditMetaData
                        id="batch"
                        forceMetadata={true}
                        setupMetadata={{}}
                        enableClear={true}
                    />
                </Fieldset>
            </Modal.Body>
            <Modal.Footer className="justify-content-center">
                <ButtonWithIcon
                    variant="primary"
                    Icon={<SaveIcon width="17" height="17"/>}
                    onClick={() => validate().then(() => onSubmit({ids, metadata, dataPoolId, tags, tagsApplyMode}))}
                >
                    <Trans t='data-pool.asset-metadata-update-modal.cta'/>
                </ButtonWithIcon>
            </Modal.Footer>
        </Modal>
    )
}

export function mapStateToProps(state) {
    let ids = getEditMetaDataModalIds(state);

    return {
        ids,
        dataPoolId: getEditMetaDataModalDataPoolId(state),
        isOpen: getEditMetaDataModalState(state),
        metadata: getMetadataEditDataById(state, "batch")
    };
}

export const mapDispatchToProps = (dispatch) => {
    return {
        validate: () => dispatch(checkMetadata({id: "batch"})),
        onSubmit: ({...payload}) => dispatch(batchMetaData(payload)),
        onClose: () => dispatch(closeEditMetaDataModal())
    };
};

export default connect(mapStateToProps, mapDispatchToProps)(UpdateModal);