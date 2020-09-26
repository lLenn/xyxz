import { IGameDefinition } from './GameDefinition';
import { Request, AddParameters, GetParameters, RemoveParameters, BridgeRequest } from '../../service/bridge/BridgeRequest';
import { when } from '../../service/bridge/BridgeActions';
// import { ObjectiveGroupDefinition } from './ObjectiveGroupDefinition';
import { BridgeCombinedRequest } from '../../service/bridge/BridgeCombinedRequest';


export const GAME_DEFINITION_TARGET = "database.collections.game_definition";
export const GAME_DEFINITION_COLLECTION = "game_definitions";

export const CACHE_GAME_DEFINITION = "com.gogocitygames.model.masterdata.game_definition.cache_game_definition";
export const UNCACHE_GAME_DEFINITION = "com.gogocitygames.model.masterdata.game_definition.uncache_game_definition";
export const ADD_GAME_DEFINITION = "com.gogocitygames.model.masterdata.game_definition.add_game_definition";
export const GET_GAME_DEFINITION = "com.gogocitygames.model.masterdata.game_definition.get_game_definition";
export const DEEPLY_GET_GAME_DEFINITION = "com.gogocitygames.model.masterdata.game_definition.deeply_get_game_definition";
export const REMOVE_GAME_DEFINITION = "com.gogocitygames.model.masterdata.game_definition.remove_game_definition";

export function cacheGameDefinition(pGameDefinition:IGameDefinition) {
    return {
        type: CACHE_GAME_DEFINITION,
        payload: pGameDefinition
    };
}

export function uncacheGameDefinition(pKey:string) {
    return {
        type: UNCACHE_GAME_DEFINITION,
        payload: pKey
    };
}

export function addGameDefinition(pParentPath:string, pGameDefinition:IGameDefinition) {
    return {
        type: ADD_GAME_DEFINITION,
        payload: BridgeRequest.create<AddParameters>(Request.ADD, [pParentPath, GAME_DEFINITION_COLLECTION].join("/"), {
            document: pGameDefinition
        })
    };
}

export function whenAddedGameDefinition(pStore:any, pParentPath:string, pGameDefinition:IGameDefinition):Promise<IGameDefinition> {
    return new Promise<IGameDefinition>(function(resolve, reject) {
        when(pStore, addGameDefinition(pParentPath, pGameDefinition)).then(function(pGameDefinition) {
            pStore.dispatch(cacheGameDefinition(pGameDefinition));
            resolve(pGameDefinition);
        }).catch(reject);
    });
}

export function retrieveGameDefinitionByID(pParentPath:string, pGameDefinitionID:string) {
    return {
        type: GET_GAME_DEFINITION,
        payload: BridgeRequest.create<GetParameters>(Request.GET, [pParentPath, GAME_DEFINITION_COLLECTION].join("/"), {
            id: pGameDefinitionID
        })
    };
}

export function whenRetrievedGameDefinitionByID(pStore:any, pParentPath:string, pGameDefinitionID:string):Promise<IGameDefinition> {
    return new Promise(function(resolve, reject) {
        when(pStore, retrieveGameDefinitionByID(pParentPath, pGameDefinitionID)).then(function(pGameDefinition) {
            if(pGameDefinition !== null) {
                pStore.dispatch(cacheGameDefinition(pGameDefinition));
            }
            resolve(pGameDefinition);
        }).catch(reject);
    });
}

export function deeplyRetrieveGameDefinitionByID(pParentPath:string, pGameDefinitionID:string) {
    return {
        type: DEEPLY_GET_GAME_DEFINITION,
        payload: BridgeCombinedRequest.create(BridgeRequest.create<GetParameters>(Request.GET, [pParentPath, GAME_DEFINITION_COLLECTION].join("/"), {
            id: pGameDefinitionID
        }))
    };
}

export function whenDeeplyRetrievedGameDefinitionByID(pStore:any, pParentPath:string, pGameDefinitionID:string):Promise<IGameDefinition> {
    return new Promise(function(resolve, reject) {
        when(pStore, deeplyRetrieveGameDefinitionByID(pParentPath, pGameDefinitionID)).then(function(pGameDefinition) {
            if(pGameDefinition !== null) {
                pStore.dispatch(cacheGameDefinition(pGameDefinition));
            }
            resolve(pGameDefinition);
        }).catch(reject);
    });
}

export function removeGameDefinition(pParentPath:string, pGameDefinitionID:string) {
    return {
        type: REMOVE_GAME_DEFINITION,
        payload: BridgeRequest.create<RemoveParameters>(Request.REMOVE, [pParentPath, GAME_DEFINITION_COLLECTION].join("/"), {
            id: pGameDefinitionID
        })
    };
}

export function whenRemovedGameDefinition(pStore:any, pParentPath:string, pGameDefinitionID:string):Promise<void> {
    return new Promise(function(resolve, reject) {
        when(pStore, removeGameDefinition(pParentPath, pGameDefinitionID)).then(function() {
            pStore.dispatch(uncacheGameDefinition(pGameDefinitionID));
            resolve();
        }).catch(reject);
    });
}
