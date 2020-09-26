import { IGameDefinition } from "./GameDefinition";
import { getCachedObject } from '../ObjectActions';


export interface IGameDefinitionState {
    game_definitions: IGameDefinition[];
}

export const initialGameDefinitionState:IGameDefinitionState = {
    game_definitions: []
};

export function getCachedGameDefinition(pState:IGameDefinitionState, pKey:string) {
    return getCachedObject(pState.game_definitions, pKey);
}
