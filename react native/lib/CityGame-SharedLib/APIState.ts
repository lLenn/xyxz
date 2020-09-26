import * as ModelState from "./model/ModelState";
import * as AuthState from "./service/auth/AuthState";
import * as BridgeState from "./service/bridge/BridgeState";


export interface IAPIState {
    auth: AuthState.IAuthState;
    database: BridgeState.IBridgeState;
    model: ModelState.IModelState;
}

export const initialAPIState:IAPIState = {
    auth: AuthState.initialAuthState,
    database: BridgeState.initialBridgeState,
    model: ModelState.initialModelState
};
