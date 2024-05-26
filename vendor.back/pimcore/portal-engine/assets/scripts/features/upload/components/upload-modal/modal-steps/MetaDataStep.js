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
import Button from "react-bootstrap/Button";
import {Modal} from "react-bootstrap";
import Trans from "~portal-engine/scripts/components/Trans";
import TagSelect from "~portal-engine/scripts/features/tags/coponents/TagSelect";
import EditMetaData from "~portal-engine/scripts/features/asset/components/metadata/EditMetaData";
import {metaDataFinished} from "~portal-engine/scripts/features/upload/upload-actions";
import {checkMetadata} from "~portal-engine/scripts/features/asset/asset-actions";
import FormGroup from "~portal-engine/scripts/components/FormGroup";
import Fieldset from "~portal-engine/scripts/components/Fieldset";
import FormControl from "~portal-engine/scripts/components/FormControl";

export function MetaDataStep({validate, onFinished = noop}) {
    const [tags, setTags] = useState([]);
    const [filename, setFilename] = useState("");

    return (
        <Fragment>
            <Modal.Body>
                <Fieldset title={<Trans t="add-tags" domain="asset"/>}>
                    <FormGroup label={<Trans t="tags" domain="asset"/>}>
                        <TagSelect onChange={(values) => setTags(values.map(value => value.id))}/>
                    </FormGroup>
                </Fieldset>

                <Fieldset title={<Trans t="metadata" domain="asset"/>} className="mt-3">
                    <EditMetaData id="upload" forceMetadata={true} setupMetadata={{}}/>
                </Fieldset>

                <Fieldset title={<Trans t="change-file-names" domain="asset"/>} className="mt-3">
                    <div className="d-flex justify-content-between align-items-center">
                        <FormGroup label={<Trans t="new-file-name" domain="asset"/>} className="flex-grow-1">
                            <FormControl
                                value={filename}
                                onChange={(value) => setFilename(value)}
                            />
                        </FormGroup>

                        <span className="flex-shrink-1 ml-3">
                            <Trans t="change-file-name-suffix" domain="asset"/>
                        </span>
                    </div>
                </Fieldset>
            </Modal.Body>

            <Modal.Footer className="justify-content-center">
                <Button disabled={false}
                        variant="primary"
                        type="button"
                        className="btn-rounded"
                        onClick={() => validate().then(() => onFinished(tags, filename))}>
                    <Trans t="upload.meta-data-step.next"/>
                </Button>
            </Modal.Footer>
        </Fragment>
    )
}

export const mapDispatchToProps = (dispatch) => ({
    validate: () => dispatch(checkMetadata({id: "upload"})),
    onFinished: (tags, filename) => dispatch(metaDataFinished({tags, filename}))
});

export default connect(null, mapDispatchToProps)(MetaDataStep)