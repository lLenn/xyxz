import { LifeCycle, Request, GetParameters, QueryParameters, IBridgeRequest, OrderBy, BridgeRequest, Query } from './BridgeRequest';
import { getRequest, getCombinedRequest } from './BridgeState';
import { IBridgeCombinedRequest, BridgeCombinedRequest } from './BridgeCombinedRequest';
import { getValue } from '../../utils/object';


export const ADD_DOCUMENT = "com.gogocitygames.bridge.add_document";
export const GET_DOCUMENT = "com.gogocitygames.bridge.get_document";
export const QUERY_DOCUMENT = "com.gogocitygames.bridge.query_document";
export const ADD_REQUEST = "com.gogocitygames.bridge.add_request";
export const ADD_COMBINED_REQUEST = "com.gogocitygames.bridge.add_combined_request";
export const ADD_TO_COMBINED_REQUEST = "com.gogocitygames.bridge.add_to_combined_request";
export const TRANSFORM_COMBINED_REQUEST = "com.gogocitygames.bridge.transform_combined_request";
export const REMOVE_REQUEST = "com.gogocitygames.bridge.remove_request";
export const REMOVE_COMBINED_REQUEST = "com.gogocitygames.bridge.remove_combined_request";
export const FAILED_REQUEST = "com.gogocitygames.bridge.failed_request";
export const CANCEL_REQUEST = "com.gogocitygames.bridge.cancel_request";
export const SET_RESPONSE = "com.gogocitygames.bridge.set_response";
export const SET_COMBINED_RESPONSE = "com.gogocitygames.bridge.set_combined_response";
export const SET_STATE = "com.gogocitygames.bridge.set_state";
export const SET_COMBINED_STATE = "com.gogocitygames.bridge.set_combined_state";

export function getDocument(pPath:string) {
    const splitPath = pPath.split("/");
    return {
        type: GET_DOCUMENT,
        payload: BridgeRequest.create<GetParameters>(Request.GET, splitPath.slice(0, -1).join("/"), { id: splitPath[splitPath.length-1] })
    };
}

export function queryDocument(pPath:string, pQuery:Query|Query[], pOrderBy?:OrderBy) {
    return {
        type: QUERY_DOCUMENT,
        payload: BridgeRequest.create<QueryParameters>(Request.QUERY, pPath, { queries: pQuery, orderBy:pOrderBy })
    };
}

export function untilPropertyExists(pStore:any, pPath:string, pAction:{ type:string; payload?:any; }) {
    return until(pStore, pAction, function(pState) {
        return getValue(pState, pPath);
    });
}

export function until(pStore:any, pAction:{ type:string; payload?:any; }, pCallback:(pState:any)=>any) {
    return new Promise(function(resolve, reject) {
        /*
        const timeout = setTimeout(function() {
            unsubscribe();
            throw new Error("Action timed out!");
        }, 5000);
        */
        const unsubscribe = pStore.subscribe(function() {
            const val = pCallback(pStore.getState());
            if(val !== undefined) {
                unsubscribe();
                // clearTimeout(timeout);
                resolve(val);
            }
        });
        pStore.dispatch(pAction);
    });
}

export function when(pStore:any, pAction:{ type:string; payload:IBridgeRequest<any>|IBridgeCombinedRequest;}):Promise<any> {
    return new Promise(function(resolve, reject) {
        let resolved = false;
        const unsubscribe = pStore.subscribe(function() {
            let req;
            if(BridgeRequest.isBridgeRequest(pAction.payload)) {			
                req = getRequest(pStore.getState().database, pAction.payload.id);
            }
            else if(BridgeCombinedRequest.isBridgeCombinedRequest(pAction.payload)) {
                req = getCombinedRequest(pStore.getState().database, pAction.payload.id);				
            }
            if(!resolved && req != null && req.response !== undefined) {
                resolved = true;
                resolve(req.response);
            }
            else if(req != null && req.state === LifeCycle.DONE) {
                if(!resolved) {
                    resolved = true;
                    resolve();
                }
                unsubscribe();
                pStore.dispatch(removeRequest(pAction.payload.id));
            }
            else if(req != null && req.state === LifeCycle.FAILED) {
                reject(req.error);				
            }
        });
        pStore.dispatch(pAction);
    });
}

export function setResponse(pRequestID:string, pResponse:any) {
    return {
        type: SET_RESPONSE,
        payload: {
            request_id: pRequestID,
            response: pResponse
        }
    };
}

export function setCombinedResponse(pCombinedRequestID:string, pResponse:any) {
    return {
        type: SET_COMBINED_RESPONSE,
        payload: {
            combined_request_id: pCombinedRequestID,
            response: pResponse
        }
    };
}

export function requestFailed(pRequestID:string, pError:any) {
    return {
        type: FAILED_REQUEST,
        payload: {
            request_id: pRequestID
        },
        error: pError
    };
}

export function cancelRequest(pRequestID:string) {
    return {
        type: CANCEL_REQUEST,
        payload: {
            request_id: pRequestID
        }
    };
}

export function addRequest(pRequest:IBridgeRequest<any>) {
    return {
        type: ADD_REQUEST,
        payload: pRequest
    };
}

export function addCombinedRequest(pCombinedRequest:IBridgeCombinedRequest) {
    return {
        type: ADD_COMBINED_REQUEST,
        payload: pCombinedRequest
    };
}

export function addToCombinedRequest(pCombinedID:string, pRequest:IBridgeRequest<any>|IBridgeCombinedRequest) {
    return {
        type: ADD_TO_COMBINED_REQUEST,
        payload: {
            combined_request_id: pCombinedID,
            request: pRequest
        }
    };
}

export function transformCombinedRequest(pCombinedID:string, pCallback:(pCombinedRequest:IBridgeCombinedRequest, ...rest:any[])=>void) {
    return {
        type: TRANSFORM_COMBINED_REQUEST,
        payload: {
            combined_request_id: pCombinedID,
            callback: pCallback
        }
    };
}

export function removeRequest(pRequestID:string) {
    return {
        type: REMOVE_REQUEST,
        payload: {
            request_id: pRequestID
        }
    };
}

export function removeCombinedRequest(pCombinedRequestID:string) {
    return {
        type: REMOVE_COMBINED_REQUEST,
        payload: {
            combined_request_id: pCombinedRequestID
        }
    };
}

export function setState(pRequestID:string, pState:LifeCycle) {
    return {
        type: SET_STATE,
        payload: {
            request_id: pRequestID,
            state: pState
        }
    };
}

export function setCombinedState(pCombinedRequestID:string, pState:LifeCycle) {
    return {
        type: SET_COMBINED_STATE,
        payload: {
            combined_request_id: pCombinedRequestID,
            state: pState
        }
    };
}
