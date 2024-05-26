/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import * as STEPS from "~portal-engine/scripts/consts/upload-steps";
import UploadSelectionStep from "~portal-engine/scripts/features/upload/components/upload-modal/modal-steps/UploadSelectionStep";
import MetaDataStep from "~portal-engine/scripts/features/upload/components/upload-modal/modal-steps/MetaDataStep";
import UploadStep from "~portal-engine/scripts/features/upload/components/upload-modal/modal-steps/UploadStep";
import FinishedStep from "~portal-engine/scripts/features/upload/components/upload-modal/modal-steps/FinishedStep";
import React from "react";
import {connect} from "react-redux";
import {getCurrentStep} from "~portal-engine/scripts/features/upload/upload-selectors";

export function Steps({
    currentStep = STEPS.UPLOAD_SELECTION,
}) {
    return(
        <div>
            {currentStep === STEPS.UPLOAD_SELECTION ? (
                <UploadSelectionStep />
            ) : null}

            {currentStep === STEPS.META_DATA ? (
                <MetaDataStep/>
            ) : null}

            {currentStep === STEPS.UPLOAD ? (
                <UploadStep/>
            ) : null}

            {currentStep === STEPS.FINISHED ? (
                <FinishedStep/>
            ) : null}
        </div>
    )
}

export const mapStateToProps = state => ({
    currentStep: getCurrentStep(state)
});

export default connect(mapStateToProps)(Steps);
