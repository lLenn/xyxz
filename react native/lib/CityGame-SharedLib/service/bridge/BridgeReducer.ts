import * as Actions from './BridgeActions';
import * as BridgeState from './BridgeState';
import produce, { isDraft } from 'immer';
import { getById, removeById } from './BridgeState';


const bridgeDraft = function(state:BridgeState.IBridgeState, action:any) {
    switch(action.type) {
        case Actions.ADD_REQUEST:
        case Actions.GET_DOCUMENT:
        case Actions.QUERY_DOCUMENT:
            state.requests.push(action.payload);
            return;
        case Actions.ADD_COMBINED_REQUEST: {
            state.combined_requests.push(action.payload);
            return;
        }
        case Actions.ADD_TO_COMBINED_REQUEST: {
            const combined_request = getById(state.combined_requests, action.payload.combined_request_id);
            combined_request!.requests.push(action.payload.request.id);
            return;
        }
        case Actions.SET_STATE: {
            const request = getById(state.requests, action.payload.request_id);
            request!.state = action.payload.state;
            return;
        }
        case Actions.SET_COMBINED_STATE: {
            const combined_request = getById(state.combined_requests, action.payload.combined_request_id);
            combined_request!.state = action.payload.state;
            return;
        }
        case Actions.SET_RESPONSE: {
            const request = getById(state.requests, action.payload.request_id);
            if(request) {
                request.response = action.payload.response;
            }
            return;
        }
        case Actions.SET_COMBINED_RESPONSE: {
            const combined_request = getById(state.combined_requests, action.payload.combined_request_id);
            combined_request!.response = action.payload.response;
            return;
        }
        case Actions.REMOVE_REQUEST: {
            removeById(state.requests, action.payload.request_id);
            return;
        }
        case Actions.REMOVE_COMBINED_REQUEST: {
            removeById(state.combined_requests, action.payload.combined_request_id);
            return;
        }
        case Actions.FAILED_REQUEST: {
            for(let i = 0, len = state.requests.length; i < len; i++) {
                if(state.requests[i].id === action.payload.request_id) {
                    state.requests[i].error = action.error;
                    break; 
                }
            }
            return;
        }
    }
};

const bridgeProduce = produce(bridgeDraft, BridgeState.initialBridgeState);

export const BridgeReducer = function(pDraft:any, pAction:any) {
    if(isDraft(pDraft)) {
        bridgeDraft(pDraft, pAction);
    }
    else {
        bridgeProduce(pDraft, pAction);
    }
};
