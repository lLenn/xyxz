import {
    IAPIState, initialAPIState, getCachedBookingInstance, getCachedSessionInstance, getByKey, GameInstance, IPlayer,
    IUser, getByKeys, generateUID, DocumentDataBridge, translate, ISolutionProgress } from "app-shared";
import { APP_DEFAULT_LANGUAGE_CODE } from '../Constants';


export enum AppState {
    CLEAR = "CLEAR",
    LOADING = "LOADING",
    START = "START",
    LOGGED_IN = "LOGGED_IN",
    UPDATE_USER = "UPDATE_USER",
    SELECT_BOOKING = "SELECT_BOOKING",
    SELECT_SESSION = "SELECT_SESSION",
    TEAM_OVERVIEW = "TEAM_OVERVIEW",
    VIEWING_TEAMS = "VIEWING_TEAMS",
    MANAGE_TEAM = "MANAGE_TEAM",
    CREATE_TEAM = "CREATE_TEAM",
    ADD_PLAYER = "ADD_PLAYER",
    START_GAME = "START_GAME",
    STEP_IN_GAME = "STEP_IN_GAME",
    IN_GAME = "IN_GAME",
    OPEN_GROUP_INFO = "OPEN_GROUP_INFO",
    OPEN_GROUP_ENDED = "OPEN_GROUP_ENDED",
    OPEN_GROUP_LEFT = "OPEN_GROUP_LEFT",
    REOPEN_RIDDLE = "REOPEN_RIDDLE",
    SOLVING_RIDDLE = "SOLVING_RIDDLE",
    SOLVED_RIDDLE = "SOLVED_RIDDLE",
    FAILED_RIDDLE = "FAILED_RIDDLE",
    ATTEMPTED_RIDDLE = "ATTEMPTED_RIDDLE",
    GAME_FINISHED = "GAME_FINISHED",
    SET_LANGUAGE_CODE = "SET_LANGUAGE_CODE"
}

export interface IState extends IAPIState {
    app:{
        state: string;
        active_booking_instance_key?: string;
        active_session_instance_key?: string;
        active_session_instance_keys?: string[];
        active_team_key?: string;
        active_objective_detail_instance_key?: string;
        active_objective_detail_instance_progress?: ISolutionProgress;
        language_code?: string;
        screens: {[key:string]:{[key:string]:any;};};
        error?: {code:string; message: string;};
        google_disabled?: boolean;
        requirements_ok?: boolean;
    };
};

export const initialState:IState = Object.assign({
    app: {
        state: AppState.START + "_" + generateUID(),
        language_code: APP_DEFAULT_LANGUAGE_CODE,
        screens: {}
    }
}, initialAPIState);

export function translateApp(pState:IState, pI18NCode:string) {
    const pI18NCodeDefinitions = getActiveTranslationsFromState(pState);
    if(!pI18NCodeDefinitions) {
        return pI18NCode;
    }
    return translate(pState.app.language_code!.toUpperCase(), pI18NCodeDefinitions, pI18NCode);
}

export function getAppState(pAppState:string) {
    return pAppState.split("_").slice(0, -1).join("_") as AppState;
}

export function getActiveTranslationsFromState(pState:IState) {
    const game_definition = DocumentDataBridge.getInnerProperty(getActiveTeam(pState)!.game_instance!, "game_definition");
    if(game_definition) {
        return game_definition.I18N_code_definitions;
    }
    return null;
}

export function getActiveBooking(pState:IState) {
    if(pState.app.active_booking_instance_key) {
        return getCachedBookingInstance(pState.model.booking_instance, pState.app.active_booking_instance_key);
    }
    else {
        return null;
    }
}

export function getActiveLanguageCode(pState:IState) {
    return pState.app.language_code ? pState.app.language_code : APP_DEFAULT_LANGUAGE_CODE;
}

export function getActiveUser(pState:IState):IUser|null {
    if(pState.auth.uid)
        return getByKey(pState.model.user.users, pState.auth.uid);

    return null;
}

export function getActiveSessionInstance(pState:IState) {
    if(pState.app.active_session_instance_key) {
        return getCachedSessionInstance(pState.model.session_instance, pState.app.active_session_instance_key);
    }
    else {
        return null;
    }
}

export function getActiveSessionInstances(pState:IState) {
    if(pState.app.active_session_instance_keys) {
        return getByKeys(pState.model.session_instance.session_instances, pState.app.active_session_instance_keys?pState.app.active_session_instance_keys:[]);
    }
    else {
        return [];
    }
}

export function getActiveTeam(pState:IState) {
    if(pState.app.active_session_instance_key && pState.app.active_team_key) {
        return getByKey(getCachedSessionInstance(pState.model.session_instance, pState.app.active_session_instance_key)!.teams, pState.app.active_team_key);
    }
    else {
        return null;
    }
}

export function getActiveObjectiveGroupInstance(pState:IState) {
    const team = getActiveTeam(pState);
    if(team && team.game_instance) {
        return GameInstance.getObjectiveGroupInstanceByKey(team.game_instance!, team.game_instance!.active_objective_group_instance_key!);
    } else {
        return null;
    }
}

export function getActiveObjectiveDetailInstance(pState:IState) {
    const team = getActiveTeam(pState);
    if(team && team.game_instance && team.game_instance.active_timer_key) {
        return GameInstance.getObjectiveDetailInstanceByKey(team.game_instance!, team.game_instance!.active_objective_group_instance_key!, team.game_instance!.active_timer_key);
    } else if(team && team.game_instance && pState.app.active_objective_detail_instance_key) {
        return GameInstance.getObjectiveDetailInstanceByKey(team.game_instance!, team.game_instance!.active_objective_group_instance_key!, pState.app.active_objective_detail_instance_key);
    } else {
        return null;
    }
}

export function getActivePlayer(pState:IState):IPlayer|null {
    if(pState.app.active_session_instance_key && pState.app.active_team_key) {
        return getByKey(getByKey(getCachedSessionInstance(pState.model.session_instance, pState.app.active_session_instance_key)!.teams, pState.app.active_team_key)!.players, pState.auth.uid!) as IPlayer;
    }
    else {
        return null;
    }
}

export function mergeOptionsScreen(pState:IState, pScreen:string, pOptions:any = {}) {
    return Object.assign(pOptions, pState.app.screens[pScreen]);
}
