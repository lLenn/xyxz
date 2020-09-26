import { IBridgeRequest } from './BridgeRequest';
import { IBridgeCombinedRequest } from './BridgeCombinedRequest';
import { DocumentDataBridge } from './DocumentDataBridge';


export interface IBridgeState {
    requests: IBridgeRequest<any>[];
    combined_requests: IBridgeCombinedRequest[];
}

export const initialBridgeState:IBridgeState = {
    requests: [],
    combined_requests: []
};

function find<T>(pArr:T[], cb:(pItem:any) => any) {	
    for(let i = 0, len = pArr.length; i < len; i++) {
        if(cb(pArr[i])) {
            return pArr[i];
        }
    }

    return null;
}

export function getBy<T>(pArr:T[], pProp:string, pVal:any) {
    return find(pArr, function(pItem) {
        return pItem[pProp] === pVal;
    });
}

function getByMultiple<T extends any>(pArr:T[], pProp:string, pVals:any[]) {
    const result:T[] = [];
    for(let i = 0, len = pArr.length; i < len; i++) {
        if(pVals.indexOf(pArr[i][pProp]) !== -1) {
            result.push(pArr[i]);
        }
    }

    return result;
}

export function removeBy<T extends any>(pArr:T[], pProp:string, pVal:any) {
    for(let i = 0, len = pArr.length; i < len; i++) {
        if(pArr[i][pProp] === pVal) {
            pArr.splice(i, 1);
            return; 
        }
    }
}

export function addBy<T extends any>(pArr:T[], pVal:any, pProp:string) {
    if(Array.isArray(pVal) === false) {
        pVal = [pVal];
    }

    for(let i = 0, len = pVal.length; i < len; i++) {
        let found = false;
        for(let j = 0, jlen = pArr.length; j < jlen; j++) {
            if(pArr[j][pProp] === pVal[i][pProp]) {
                pArr[j] = pVal[i];
                found = true;
                break;
            }
        }
        if(!found) {
            pArr.push(pVal[i]);
        }
    }
}

export function getByRef<T>(pArr:T[], pRef:string) {
    return find(pArr, function(pItem) {
        return DocumentDataBridge.getInnerProperty(pItem, "document_reference").path === pRef;	
    });
}

export function getByKey<T>(pArr:T[], pKey:string) {
    return getBy(pArr, "key", pKey);
}

export function getById<T>(pArr:T[], pID:string) {
    return getBy(pArr, "id", pID);
}

export function getByIds<T>(pArr:T[], pIDs:string[]) {
    return getByMultiple(pArr, "id", pIDs);
}

export function getByKeys<T>(pArr:T[], pKeys:string[]) {
    return getByMultiple(pArr, "key", pKeys);
}

export function removeById<T>(pArr:T[], pID:string) {
    removeBy(pArr, "id", pID);
}

export function removeByKey<T>(pArr:T[], pID:string) {
    removeBy(pArr, "key", pID);
}

export function getRequest(pState:IBridgeState, pRequestID:string):IBridgeRequest<any>|null {
    return getById(pState.requests, pRequestID);
}

export function getCombinedRequest(pState:IBridgeState, pRequestID:string):IBridgeCombinedRequest|null {
    return getById(pState.combined_requests, pRequestID);	
}

export function addByKey<T>(pArr:T[], pVal:any) {
    return addBy<T>(pArr, pVal, "key");
}
