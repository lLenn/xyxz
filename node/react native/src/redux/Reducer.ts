import * as State from "./State";
import * as Actions from "./Actions";
import { APIReducer, generateUID } from "app-shared";
import produce from 'immer';
import { APP_DEFAULT_LANGUAGE_CODE } from '../Constants';


function stripNonPlainObjects(pObject:any):any {    
    if(Array.isArray(pObject)) {
        const newArr = [];
        for(let i = 0, len = pObject.length; i < len; i++) {
            newArr.push(stripNonPlainObjects(pObject[i]));
        }
        return newArr;
    }
    else if(pObject !== null && typeof pObject === "object") {
        if(pObject.constructor != Object) {
            return null;
        }
        const newObj:any = {};
        for(const prop in pObject) {
            if(pObject.hasOwnProperty(prop)) {
                newObj[prop] = stripNonPlainObjects(pObject[prop]);
            }
        }
        return newObj;
    }
    return pObject;
}

export const Reducer = produce((pDraft:State.IState, pAction) => {
    switch(pAction.type) {
        case Actions.SET_BOOKING:
            if(pAction.payload !== null) {
                pDraft.app.active_booking_instance_key = pAction.payload;
            }
            break;
        case Actions.SET_SESSION:
            pDraft.app.active_session_instance_key = pAction.payload?pAction.payload:undefined;
            break;
        case Actions.SET_ACTIVE_SESSION_INSTANCES:
            pDraft.app.active_session_instance_keys = pAction.payload?pAction.payload:undefined;
            break;
        case Actions.SET_LANGUAGE_CODE_STATE:
            // default pas gezet bij oproepen actie
            pDraft.app.language_code = pAction.payload ? pAction.payload : APP_DEFAULT_LANGUAGE_CODE;
            break;
        case Actions.SET_TEAM:
            pDraft.app.active_team_key = pAction.payload?pAction.payload:undefined;
            break;
        case Actions.UNSET_TEAM:
            delete pDraft.app.active_team_key;
            break;
        case Actions.SET_STATE:
            pDraft.app.state = pAction.payload;
            break;
        case Actions.SET_OBJECTIVE_DETAIL:
            if(pDraft.app.active_objective_detail_instance_key !== pAction.payload) {
                pDraft.app.active_objective_detail_instance_key = pAction.payload;
                pDraft.app.active_objective_detail_instance_progress = { answers: [] };
            }
            break;
        case Actions.UNSET_OBJECTIVE_DETAIL:
            delete pDraft.app.active_objective_detail_instance_key;
            delete pDraft.app.active_objective_detail_instance_progress;
            break;
        case Actions.SET_SCREEN_PROPERTIES:
            pDraft.app.screens[pAction.payload.screen] = pAction.payload.props;
            break;
        case Actions.DISABLE_GOOGLE:
            pDraft.app.google_disabled = true;
            break;
        case Actions.CLEAR_APP:
            pDraft.app = {
                state: State.AppState.CLEAR + "_" + generateUID(),
                language_code: APP_DEFAULT_LANGUAGE_CODE,
                screens: {}
            };
            break;
        case Actions.ERROR:
            pDraft.app.error = pAction.payload;
            break;
        case Actions.CLEAR_ERROR:
            delete pDraft.app.error;
            break;
        case Actions.SET_REQUIREMENTS_OK:
            pDraft.app.requirements_ok = pAction.payload;
            break;
        case Actions.ADD_ANSWER_TO_OBJECTIVE_DETAIL:
            if(pDraft.app.active_objective_detail_instance_progress !== undefined) {
                pDraft.app.active_objective_detail_instance_progress.answers.push(pAction.answer);
            }
            break;
    }
    APIReducer(pDraft, pAction);
    /*
    if(__DEV__) {
        console.log(stripNonPlainObjects(pAction));
        console.log(stripNonPlainObjects(pDraft));
    }
    else if(__DEV__) {
        console.log(pAction.type);
    }
    */
}, State.initialState);
