/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


export const getSubfolderState = (state) => state.subfolder;
export const isSubfolderModalOpen = (state) => getSubfolderState(state).modalOpen;
export const getSubfolderModalState = (state) => getSubfolderState(state).modal;