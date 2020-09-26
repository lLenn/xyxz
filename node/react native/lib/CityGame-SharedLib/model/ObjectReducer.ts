import * as Actions from './ObjectActions';
import { setValue } from '../utils/object'; 
import produce, { isDraft } from 'immer';


const objectDraft = function(pState:any, pAction:any) {
    switch(pAction.type) {
        case Actions.SET_PROPERTY:
            setValue(pState, pAction.payload.field, pAction.payload.value);
            return;
    }
};

const objectProduce = produce(objectDraft, {});

export const ObjectReducer = function(pDraft:any, pAction:any) {
    if(isDraft(pDraft)) {
        objectDraft(pDraft, pAction);
    }
    else {
        objectProduce(pDraft, pAction);
    }
};
