/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {createReducer} from "@reduxjs/toolkit";
import {openSubfolderModal, closeSubfolderModal, updateModalState} from "~portal-engine/scripts/features/subfolder/subfolder-actions";

const initialModalState = {
    name: ""
}

const initialState = {
    modalOpen: false,
    modal: initialModalState
};

export default createReducer(initialState, {
    [openSubfolderModal.type]: (state, {payload: {modalOpen, modalState = {}}}) => ({
            ...state,
            modalOpen: modalOpen,
            modal: {
                ...initialModalState,
                ...modalState
            }
    }),

    [closeSubfolderModal.type]: (state) => ({
        ...state,
        modalOpen: false,
        modal: initialModalState
    }),

    [updateModalState.type]: (state, {payload}) => ({
        ...state,
        modal: payload
    })
});