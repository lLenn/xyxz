import { IDocumentData } from '../service/bridge/DocumentDataBridge';


export const CACHE_OBJECT = "com.gogocitygames.model.cache_object";
export const SET_PROPERTY = "com.gogocitygames.model.set_property";

export const getCachedObject = function<T extends IDocumentData>(pArr:T[], pKey:string) {
    for(let i = 0, len = pArr.length; i < len; i++) {
        if(pArr[i].key === pKey) {
            return pArr[i];
        }
    }
    return null;
};

export const cacheObject = function<T extends IDocumentData>(pArr:T[], pObject:any) {
    for(let i = 0, len = pArr.length; i < len; i++) {
        if(pArr[i].key === pObject.key) {
            pArr[i] = pObject;
            return;
        }
    }
    pArr.push(pObject);
};

export const uncacheObject = function<T extends IDocumentData>(pArr:T[], pKey:any) {
    for(let i = 0, len = pArr.length; i < len; i++) {
        if(pArr[i].key === pKey) {
            pArr.splice(i, 1);
            return;
        }
    }
};

export function setProperty(pField:string, pValue:any) {
    return {
        type: SET_PROPERTY,
        payload: {
            field: pField,
            value: pValue
        }
    };
}
