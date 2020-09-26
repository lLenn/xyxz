import { CityReducer } from "./masterdata/CityReducer";
import { GameDefinitionReducer } from "./masterdata/GameDefinitionReducer";
import { ObjectiveGroupDefinitionReducer } from "./masterdata/ObjectiveGroupDefinitionReducer";
import { ObjectiveDetailDefinitionReducer } from "./masterdata/ObjectiveDetailDefinitionReducer";
import { BookingInstanceReducer } from "./operational/BookingInstanceReducer";
import { SessionInstanceReducer } from "./operational/SessionInstanceReducer";
import { TeamReducer } from "./operational/TeamReducer";
import { PlayerReducer } from './operational/PlayerReducer';
import { GameInstanceReducer } from './operational/GameInstanceReducer';
import { ObjectiveGroupInstanceReducer } from './operational/ObjectiveGroupInstanceReducer';
import { ObjectiveDetailInstanceReducer } from './operational/ObjectiveDetailInstanceReducer';
import { UserReducer } from './auth/UserReducer';
import * as ModelState from "./ModelState";
import produce, { isDraft } from 'immer';


const modelDraft = function(pDraft:ModelState.IModelState, pAction:any) {
    CityReducer(pDraft.city, pAction);
    GameDefinitionReducer(pDraft.game_definition, pAction);
    ObjectiveGroupDefinitionReducer(pDraft.objective_group_definition, pAction);
    ObjectiveDetailDefinitionReducer(pDraft.objective_detail_definition, pAction);
    BookingInstanceReducer(pDraft.booking_instance, pAction);
    TeamReducer(pDraft.session_instance, pAction);
    SessionInstanceReducer(pDraft.session_instance, pAction);
    PlayerReducer(pDraft.session_instance, pAction);
    GameInstanceReducer(pDraft, pAction);
    ObjectiveGroupInstanceReducer(pDraft, pAction);
    ObjectiveDetailInstanceReducer(pDraft, pAction);
    UserReducer(pDraft.user, pAction);
};

const modelProduce = produce(modelDraft, ModelState.initialModelState);

export const ModelReducer = function(pDraft:ModelState.IModelState, pAction:any) {
    if(isDraft(pDraft)) {
        modelDraft(pDraft, pAction);
    }
    else {
        modelProduce(pDraft, pAction);
    }
};
