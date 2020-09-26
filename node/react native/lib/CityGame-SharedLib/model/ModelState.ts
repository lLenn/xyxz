import * as CityState from "./masterdata/CityState";
import * as GameDefinitionState from "./masterdata/GameDefinitionState";
import * as ObjectiveGroupDefinitionState from "./masterdata/ObjectiveGroupDefinitionState";
import * as ObjectiveDetailDefinitionState from "./masterdata/ObjectiveDetailDefinitionState";
import * as BookingInstanceState from "./operational/BookingInstanceState";
import * as SessionInstanceState from "./operational/SessionInstanceState";
import * as UserState from './auth/UserState';


export interface IModelState { 
    city: CityState.ICityState;
    game_definition: GameDefinitionState.IGameDefinitionState;
    objective_group_definition: ObjectiveGroupDefinitionState.IObjectiveGroupDefinitionState;
    objective_detail_definition: ObjectiveDetailDefinitionState.IObjectiveDetailDefinitionState;
    booking_instance: BookingInstanceState.IBookingInstanceState;
    session_instance: SessionInstanceState.ISessionInstanceState;
    user: UserState.IUserState;
}

export const initialModelState:IModelState = { 
    city: CityState.initialCityState,
    game_definition: GameDefinitionState.initialGameDefinitionState,
    objective_group_definition: ObjectiveGroupDefinitionState.initialObjectiveGroupDefinitionState,
    objective_detail_definition: ObjectiveDetailDefinitionState.initialObjectiveDetailDefinitionState,
    booking_instance: BookingInstanceState.initialBookingInstanceState,
    session_instance: SessionInstanceState.initialSessionInstanceState,
    user: UserState.initialUserState
};
