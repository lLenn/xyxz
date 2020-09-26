import { ObjectReducer } from "./model/ObjectReducer";
import { ModelReducer } from "./model/ModelReducer";
import { AuthReducer } from "./service/auth/AuthReducer";
import { BridgeReducer } from "./service/bridge/BridgeReducer";
import * as APIState from "./APIState";
import produce, { isDraft } from 'immer';


function apiDraft(pDraft:APIState.IAPIState, pAction:any) {
    AuthReducer(pDraft.auth, pAction);
    BridgeReducer(pDraft.database, pAction);
    ModelReducer(pDraft.model, pAction);
    ObjectReducer(pDraft, pAction);
}

const apiProduce = produce(apiDraft, APIState.initialAPIState);

export const APIReducer = function(pDraft:APIState.IAPIState, pAction:any) {
    if(isDraft(pDraft)) {
        return apiDraft(pDraft, pAction);
    }
    else {
        return apiProduce(pDraft, pAction);
    }
};
