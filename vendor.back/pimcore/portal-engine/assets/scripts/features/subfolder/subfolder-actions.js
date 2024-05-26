/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {createAction} from "@reduxjs/toolkit";

export const openSubfolderModal = createAction("subfolder/modal/open");
export const closeSubfolderModal = createAction("subfolder/modal/close");

export const updateModalState = createAction("subfolder/modal/update");