/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


async function handleErrors(response) {
    if (!response.ok) {
        const data = await response.json();
        if(!data.success) {
            throw Error(data.message);
        } else {
            throw Error(response.statusText);
        }
    }
    return response;
}

export default handleErrors;