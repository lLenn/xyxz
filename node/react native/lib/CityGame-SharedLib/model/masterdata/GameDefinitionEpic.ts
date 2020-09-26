import { Observable, from, concat, of } from 'rxjs';
import { Action } from 'redux';
import { mergeMap } from 'rxjs/operators';
import { combineEpics, ActionsObservable, ofType } from 'redux-observable';
import { ADD_GAME_DEFINITION, GET_GAME_DEFINITION, REMOVE_GAME_DEFINITION, DEEPLY_GET_GAME_DEFINITION } from './GameDefinitionActions';
import { IGameDefinition, GameDefinition } from './GameDefinition';
import { getDocumentEpic, addDocumentEpic, removeDocumentEpic, queryDocumentsEpic, onResponse } from '../../service/bridge/BridgeEpic';
import { LifeCycle } from '../../service/bridge/BridgeRequest';
import { setState, addToCombinedRequest, addCombinedRequest, setCombinedState, setCombinedResponse } from '../../service/bridge/BridgeActions';
import { retrieveObjectiveGroupDefinitionsByQuery } from './ObjectiveGroupDefinitionActions';
import { IObjectiveGroupDefinition } from './ObjectiveGroupDefinition';
import { retrieveObjectiveDetailDefinitionsByQuery } from './ObjectiveDetailDefinitionActions';
import { IObjectiveDetailDefinition } from './ObjectiveDetailDefinition';
import { DocumentDataBridge } from '../../service/bridge/DocumentDataBridge';
import { getFromCacheOrObservable } from '../getFromCacheOrObservable';
import { IAPIState } from '../../APIState';


export function addGameDefinition(pAction:any, pState:{value:IAPIState;}) {
    return concat(addDocumentEpic(pAction.payload, {
        sys_insert_dt: Date.now(),
        sys_insert_user_id: pState.value.auth.uid?pState.value.auth.uid:"no user"
    }), of(setState(pAction.payload.id, LifeCycle.DONE)));
}

export function getGameDefinition(pAction:any) {
    return concat(getDocumentEpic(pAction.payload), of(setState(pAction.payload.id, LifeCycle.DONE)));
}

export function deeplyGetGameDefinition(pAction:any, pState:{ value:IAPIState; }) {
    return concat(
        of(addCombinedRequest(pAction.payload)),
        onResponse(
            getFromCacheOrObservable(pAction.payload.initial_request, pState.value.model, getDocumentEpic(pAction.payload.initial_request)),
            (pGameDefinition:IGameDefinition) => (new Observable<Action<any>>(function(pSubscriberGame) {
                const groupRequest = retrieveObjectiveGroupDefinitionsByQuery([pAction.payload.initial_request.path, pAction.payload.initial_request.parameters.id].join("/"), [], ["sys_insert_dt", "asc"]);
                pSubscriberGame.next(addToCombinedRequest(pAction.payload.id, groupRequest.payload));
                if(DocumentDataBridge.getInnerProperty(pGameDefinition, "objective_group_definitions") === undefined) {
                    concat(onResponse(queryDocumentsEpic(groupRequest.payload), (pGroups:IObjectiveGroupDefinition[]) => {
                        DocumentDataBridge.setInnerProperty(pGameDefinition, "objective_group_definitions", pGroups);
                        return from(pGroups).pipe(mergeMap((pGroup:IObjectiveGroupDefinition) => new Observable<Action<any>>(function(pSubscriberGroup) {
                            DocumentDataBridge.setInnerProperty(pGroup, "map_position_address_definition", GameDefinition.getAddressDefinitionByCode(pGameDefinition, pGroup.map_position_address));
                            const detailRequest = retrieveObjectiveDetailDefinitionsByQuery(DocumentDataBridge.getInnerProperty(pGroup, "document_reference").path, [], ["sys_insert_dt", "asc"]);
                            pSubscriberGroup.next(addToCombinedRequest(pAction.payload.id, detailRequest.payload));
                            concat(onResponse(queryDocumentsEpic(detailRequest.payload), (pDetails:IObjectiveDetailDefinition[]) => new Observable<Action<any>>(function(pSubscriberDetails) {
                                for(let i = 0, len = pDetails.length; i < len; i++) {
                                    DocumentDataBridge.setInnerProperty(pDetails[i], "map_position_address_definition", GameDefinition.getAddressDefinitionByCode(pGameDefinition, pDetails[i].map_position_address));
                                }
                                DocumentDataBridge.getInnerProperty(pGroup, "objective_detail_definitions", pDetails);
                                pSubscriberDetails.next(setState(detailRequest.payload.id, LifeCycle.DONE));
                                pSubscriberDetails.complete();
                            })), of(setState(groupRequest.payload.id, LifeCycle.DONE))).subscribe(pSubscriberGroup);
                        })));
                    }), of(setCombinedResponse(pAction.payload.id, pGameDefinition))).subscribe(pSubscriberGame);
                }
                else {
                    pSubscriberGame.complete();
                }
            }))),
        of(setCombinedState(pAction.payload.id, LifeCycle.DONE))
    );
}

export function removeGameDefinition(pAction:any) {
    return concat(removeDocumentEpic(pAction.payload), of(setState(pAction.payload.id, LifeCycle.DONE)));
}

export const GameDefinitionEpic = combineEpics(
    function(pActions:ActionsObservable<Action<any>>, pState:any) {
        return pActions.pipe(
            ofType(ADD_GAME_DEFINITION),
            mergeMap((pAction:any) => addGameDefinition(pAction, pState))
        );
    },
    function(pActions:ActionsObservable<Action<any>>, pState:any) {
        return pActions.pipe(
            ofType(GET_GAME_DEFINITION),
            mergeMap(getGameDefinition)
        );
    },
    function(pActions:ActionsObservable<Action<any>>, pState:any) {
        return pActions.pipe(
            ofType(DEEPLY_GET_GAME_DEFINITION),
            mergeMap((pAction) => deeplyGetGameDefinition(pAction, pState))
        );
    },
    function(pActions:ActionsObservable<Action<any>>, pState:any) {
        return pActions.pipe(
            ofType(REMOVE_GAME_DEFINITION),
            mergeMap(removeGameDefinition)
        );
    }
);
