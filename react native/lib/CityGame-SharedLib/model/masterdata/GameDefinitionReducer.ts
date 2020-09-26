import * as GameDefinitionState from "./GameDefinitionState";
import * as GameDefinitionActions from "./GameDefinitionActions";
import produce, { isDraft } from 'immer';
import { cacheObject, uncacheObject } from '../ObjectActions';


const gameDefinitionDraft = function(pDraft:GameDefinitionState.IGameDefinitionState, pAction:any) {
    switch(pAction.type) {
        case GameDefinitionActions.CACHE_GAME_DEFINITION:
            cacheObject(pDraft.game_definitions, pAction.payload);
            return;
        case GameDefinitionActions.UNCACHE_GAME_DEFINITION:
            uncacheObject(pDraft.game_definitions, pAction.payload);
            return;
    }
};

const gameDefinitionProduce = produce(gameDefinitionDraft, GameDefinitionState.initialGameDefinitionState);

export const GameDefinitionReducer = function(pDraft:GameDefinitionState.IGameDefinitionState, pAction:any) {
    if(isDraft(pDraft)) {
        gameDefinitionDraft(pDraft, pAction);
    }
    else {
        gameDefinitionProduce(pDraft, pAction);
    }
};
