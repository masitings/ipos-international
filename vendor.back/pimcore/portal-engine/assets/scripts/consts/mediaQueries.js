/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


const XS = 0;
const SM = 576;
const MD = 768;
const LG = 992;
const XL = 1200;

const createdUpMQ = width => `(min-width: ${width}px)`;
export const XS_UP = createdUpMQ(XS);
export const SM_UP = createdUpMQ(SM);
export const MD_UP = createdUpMQ(MD);
export const LG_UP = createdUpMQ(LG);
export const XL_UP = createdUpMQ(XL);

const createdDownMQ = width => `(max-width: ${width - 1}px)`;
export const XS_DOWN = createdDownMQ(XS);
export const SM_DOWN = createdDownMQ(SM);
export const MD_DOWN = createdDownMQ(MD);
export const LG_DOWN = createdDownMQ(LG);
export const XL_DOWN = createdDownMQ(XL);