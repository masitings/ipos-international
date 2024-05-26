/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {abortAbleFetch, prepareFetchPromise} from "~portal-engine/scripts/utils/fetch";
import {addParamsObjectToURL} from "~portal-engine/scripts/utils/utils";
import {showError} from "~portal-engine/scripts/utils/general";
import {getLanguage} from "~portal-engine/scripts/utils/intl";

export function startUpload() {
    let {abort, response} = abortAbleFetch('/_portal-engine/api/asset/upload/start');

    return {
        abort,
        response: prepareFetchPromise(response)
    };
}

export function uploadFromUrl({url, dataPoolId, folder, uploadId, metadata, tags, filename}) {
    let data = new FormData();
    data.append('url', url);
    data.append('metadata', metadata ? JSON.stringify(metadata) : null);
    data.append('tags', tags);
    data.append('filename', filename);
    let {abort, response} = abortAbleFetch(addParamsObjectToURL('/_portal-engine/api/asset/upload/import-url', {
        fullPath: folder,
        dataPoolId,
        uploadId,
        uploadFolder: window.location.search.indexOf("uploadFolder") > -1
    }), {
        method: 'POST',
        body: data
    });

    return {
        abort,
        response: prepareFetchPromise(response)
    };
}

const uploadFile = ({folder, file, dataPoolId, uploadId, onProgress, metadata, tags, filename}) => {
    let request = new XMLHttpRequest();

    let promise = new Promise((resolve) => {
        let data = new FormData();
        data.append('metadata', metadata ? JSON.stringify(metadata) : null);
        data.append('tags', tags);
        data.append('filename', filename);
        data.append('Filedata', file.blob, file.path);

        request.open('POST', addParamsObjectToURL('/_portal-engine/api/asset/upload/add-asset', {
            dataPoolId,
            uploadId,
            fullPath: folder,
            uploadFolder: window.location.search.indexOf("uploadFolder") > -1,
            _locale: getLanguage()
        }));

        if (onProgress) {
            request.upload.addEventListener('progress', evt => {
                onProgress(evt.loaded / evt.total);
            });
        }

        request.addEventListener('load', function (e) {
            resolve(request.response);
        });

        request.send(data);
    });

    return {
        response: promise,
        abort: () => request.abort()
    }
};

export const replaceFile = ({id, file, dataPoolId, onProgress}) => {
    let request = new XMLHttpRequest();

    let promise = new Promise((resolve) => {
        let data = new FormData();
        data.append('assetId', id);
        data.append('dataPoolId', dataPoolId);
        data.append('Filedata', file.blob, file.path);

        request.open('POST', addParamsObjectToURL('/_portal-engine/api/asset/upload/replace-asset'));

        if (onProgress) {
            request.upload.addEventListener('progress', evt => {
                onProgress(evt.loaded / evt.total);
            });
        }

        request.addEventListener('load', function (e) {
            resolve(request.response);
        });

        request.send(data);
    });

    return {
        response: promise,
        abort: () => request.abort()
    }
};

export const uploadFiles = ({folder, files, onProgressesChanged, dataPoolId, uploadId, metadata, tags, filename}) => {
    let progresses = [...Array(files.length).keys()].map(() => 0);
    onProgressesChanged(progresses);

    let queue = files;
    let isAborted = false;
    let currentRequest;

    let promise = new Promise((resolve) => {
        const loadNext = () => {
            if (!queue.length || isAborted) {
                // End of queue
                resolve();
                return;
            }

            let [currentFile, ...remainingQueue] = queue;
            let index = files.indexOf(currentFile);

            currentRequest = uploadFile({
                folder,
                file: currentFile,
                dataPoolId,
                uploadId,
                metadata,
                tags,
                filename,
                onProgress: currentProgress => {
                    progresses = [...progresses];
                    progresses[index] = currentProgress;
                    onProgressesChanged(progresses);
                }
            });

            currentRequest.response.then((responseString) => {
                const {error, data} = JSON.parse(responseString);
                currentRequest = null;

                if (error) {
                    showError(error);
                }

                if (data && data.stopUpload) {
                    queue = [];
                    resolve();
                    return;
                }

                queue = remainingQueue;
                loadNext();
            }).catch(error => {
                if (error) {
                    showError(error);
                }

                currentRequest = null;
                queue = remainingQueue;
                loadNext();
            });
        };

        loadNext();
    });

    return {
        response: promise,
        abort: () => {
            isAborted = true;
            if (currentRequest && currentRequest.abort) {
                currentRequest.abort();
            }
        }
    }
};

export const uploadZip = ({folder, file, dataPoolId, uploadId, metadata, tags, filename, onProgressesChanged}) => {
    let request = new XMLHttpRequest();
    let currentRequest;
    let isAborted = false;

    let promise = new Promise((resolve, reject) => {
        let data = new FormData();
        data.append('metadata', metadata ? JSON.stringify(metadata) : null);
        data.append('tags', tags);
        data.append('filename', filename);
        data.append('Filedata', file.blob, file.path);

        request.open('POST', addParamsObjectToURL('/_portal-engine/api/asset/upload/import-zip', {
            dataPoolId,
            uploadId,
            fullPath: folder,
            uploadFolder: window.location.search.indexOf("uploadFolder") > -1
        }));

        if (onProgressesChanged) {
            request.upload.addEventListener('progress', evt => {
                onProgressesChanged([evt.loaded / evt.total, 0]);
            });
        }

        request.addEventListener('load', function (e) {
            onProgressesChanged([1, 0]);
            let response;
            try {
                response = JSON.parse(request.response);
            } catch (e) {}

            if (response && !response.error && response.data && response.data.zipNumFiles && response.data.zipId) {
                let numberOfFiles = response.data.zipNumFiles;
                let id = response.data.zipId;
                let currentIndex = 0;

                const extractNext = () => {
                    if (currentIndex >= numberOfFiles || isAborted) {
                        // End of queue
                        onProgressesChanged([1, 1]);
                        resolve();
                        return;
                    }

                    currentRequest = abortAbleFetch(addParamsObjectToURL('/_portal-engine/api/asset/upload/import-zip-file', {
                        dataPoolId,
                        uploadId,
                        fullPath: folder,
                        zipId: id,
                        zipIndex: currentIndex,
                        uploadFolder: window.location.search.indexOf("uploadFolder") > -1
                    }), {
                        method: 'POST'
                    });

                    prepareFetchPromise(currentRequest.response).then(({error, data}) => {
                        currentRequest = null;

                        if (error) {
                            showError(error);
                        }

                        if (data && data.stopUpload) {
                            resolve();
                            return;
                        }

                        currentIndex++;
                        onProgressesChanged([1, Math.min(1, (currentIndex + .25) / numberOfFiles)]);
                        extractNext();
                    }).catch((error) => {
                        if (error) {
                            showError(error);
                        }

                        currentRequest = null;
                        currentIndex++;
                        onProgressesChanged([1, Math.min(1, (currentIndex + .25) / numberOfFiles)]);
                        extractNext();
                    })
                };

                extractNext();
            } else {
                reject()
            }

        });

        request.send(data);
    });

    return {
        response: promise,
        abort: () => {
            request.abort();
            isAborted = true;

            if (currentRequest && currentRequest.abort) {
                currentRequest.abort();
            }
        }
    }
};

export function fetchUploadedList({dataPoolId, uploadId}) {
    let {abort, response} = abortAbleFetch(addParamsObjectToURL('/_portal-engine/api/asset/upload/finalize', {dataPoolId, uploadId}));

    return {
        abort,
        response: prepareFetchPromise(response)
    }
}

export const cancelUpload = ({uploadId}) =>
    prepareFetchPromise(fetch(addParamsObjectToURL('/_portal-engine/api/asset/upload/reset-list', {uploadId}), {method: 'POST'}));