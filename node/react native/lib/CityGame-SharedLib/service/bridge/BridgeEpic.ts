import { firestore } from 'firebase';
import { batchActions } from 'redux-batched-actions';
import { Observable, concat, of, empty } from 'rxjs';
import { LifeCycle, IBridgeRequest } from './BridgeRequest';
import { setState, setResponse, addRequest, requestFailed, SET_RESPONSE, SET_COMBINED_RESPONSE, CANCEL_REQUEST, cancelRequest } from './BridgeActions';
import { IDocumentData, DocumentDataBridge, ISysData } from './DocumentDataBridge';
import { Bridge } from './Bridge';
import { mergeMap } from 'rxjs/operators';
import { Action } from 'redux';
import { ActionsObservable, ofType } from 'redux-observable';
import { IBridgeState } from './BridgeState';


function initialize(pObject:IDocumentData, pDocumentReference:firebase.firestore.DocumentReference) {
    pObject.key = pDocumentReference.id;
    DocumentDataBridge.setInnerProperty(pObject, "document_reference", pDocumentReference);
    return pObject;
}

function copy(pObject:any):any {
    if(Array.isArray(pObject)) {
        const array:any[] = [];
        for(let i = 0, len = pObject.length; i < len; i++) {
            array[i] = copy(pObject[i]);
        }
        return array;
    }
    else if(pObject !== null && typeof pObject === "object") {
        const newObject:{[key:string]:any;} = {};
        for(const prop in pObject) {
            if(prop !== "inner" && pObject.hasOwnProperty(prop)) {
                newObject[prop] = copy(pObject[prop]);
            }
            else {
                newObject[prop] = Object.assign({}, pObject[prop]);
            }
        }
        return newObject;
    }
    return pObject;
}

// Takes an action observable {pObservable} and executes the response observable {onResponseObservable} if it encounters an action of a certain type {pType}
export function onType(pObservable:Observable<any>, onResponseObservable:(value: any) => Observable<any>|void, pType:string, pGetResponse:(pAction:any) => any = (pAction) => pAction) {
    return pObservable.pipe(
        mergeMap((pAction) => {
            if(pAction.type === pType) {
                const observable = onResponseObservable(copy(pGetResponse(pAction)));
                // if the response observer doesn't return anything it's skipped.
                if(observable !== undefined) {
                    return concat(of(pAction), observable);
                }
                else {
                    return of(pAction);
                }
            }
            else {
                return of(pAction);
            }
        })
    );
}

// Takes an action observable {pObservable} and executes the response observable {onResponseObservable} if it encounters an action of the type SET_RESPONSE
export function onResponse(pObservable:Observable<any>, onResponseObservable:(value: any) => Observable<any>|void) {
    return onType(pObservable, onResponseObservable, SET_RESPONSE, (pAction) => pAction.payload.response);
}

// Takes an action observable {pObservable} and executes the response observable {onResponseObservable} if it encounters an action of the type SET_COMBINED_RESPONSE
export function onCombinedResponse(pObservable:Observable<any>, onResponseObservable:(value: any) => Observable<any>|void) {
    return onType(pObservable, onResponseObservable, SET_COMBINED_RESPONSE, (pAction) => pAction.payload.response);
}

export function addDocumentEpic(pRequest:IBridgeRequest<any>, pSysData:ISysData) {
    return new Observable<any>(function(pSubscriber) {
        pSubscriber.next(batchActions([addRequest(pRequest), setState(pRequest.id, LifeCycle.PENDING)]));
        const collRef = firestore().collection(pRequest.path);
        const doc = Bridge.MakePlainObject(pRequest.parameters.document);
        doc.sys_insert_dt = pSysData.sys_insert_dt;
        doc.sys_insert_user_id = pSysData.sys_insert_user_id;
        collRef.add(doc).then(function(docRef) {
            initialize(doc, docRef);
            pSubscriber.next(setResponse(pRequest.id, doc));
            pSubscriber.complete();
        }).catch(function(pError:any) {
            pSubscriber.next(requestFailed(pRequest.id, pError));
            pSubscriber.error(pError);
        });
    });
}

export function addDocumentEpicById(pRequest:IBridgeRequest<any>, pSysData:ISysData) {
    return new Observable<any>(function(pSubscriber) {
        pSubscriber.next(batchActions([addRequest(pRequest), setState(pRequest.id, LifeCycle.PENDING)]));
        const collRef = firestore().collection(pRequest.path);
        const docRef = collRef.doc(pRequest.parameters.key);
        const doc = Bridge.MakePlainObject(pRequest.parameters.document);
        doc.sys_insert_dt = pSysData.sys_insert_dt;
        doc.sys_insert_user_id = pSysData.sys_insert_user_id;
        docRef.set(doc).then(() => {
            initialize(doc, docRef);
            pSubscriber.next(setResponse(pRequest.id, doc));
            pSubscriber.complete();
        }).catch(function(pError:any) {
            pSubscriber.next(requestFailed(pRequest.id, pError));
            pSubscriber.error(pError);
        });
    });
}

export function updateDocumentEpic(pRequest:IBridgeRequest<any>, pSysData:ISysData) {
    return new Observable<any>(function(pSubscriber) {
        pSubscriber.next(batchActions([addRequest(pRequest), setState(pRequest.id, LifeCycle.PENDING)]));
        const doc = DocumentDataBridge.getInnerProperty(pRequest.parameters.document, "document_reference");
        const updateData = Bridge.MakePlainObject(pRequest.parameters.document);
        updateData.sys_edit_dt = pSysData.sys_edit_dt;
        updateData.sys_edit_user_id = pSysData.sys_edit_user_id;
        doc.update(updateData).then(function() {
            pSubscriber.complete();
        }).catch(function(pError:any) {
            pSubscriber.next(requestFailed(pRequest.id, pError));
            pSubscriber.error(pError);
        });
    });
}

export function updateDocumentEpicById(pRequest:IBridgeRequest<any>, pSysData:ISysData) {
    return new Observable<any>(function(pSubscriber) {
        pSubscriber.next(batchActions([addRequest(pRequest), setState(pRequest.id, LifeCycle.PENDING)]));
        const collRef = firestore().collection(pRequest.path);
        const docRef = collRef.doc(pRequest.parameters.key);
        const updateData = Bridge.MakePlainObject(pRequest.parameters.document);
        updateData.sys_edit_dt = pSysData.sys_edit_dt;
        updateData.sys_edit_user_id = pSysData.sys_edit_user_id;
        docRef.update(updateData).then(function() {
            pSubscriber.next(setResponse(pRequest.id, initialize(updateData, docRef)));
            pSubscriber.complete();
        }).catch(function(pError:any) {
            pSubscriber.next(requestFailed(pRequest.id, pError));
            pSubscriber.error(pError);
        });
    });
}

export function updateByTransactionEpic<T extends IDocumentData>(pRequest:IBridgeRequest<any>, pReducer:(pDocument:T, pActions:Action<any>[]) => T, pSysData:ISysData) {
    return new Observable<any>(function(pSubscriber) {
        pSubscriber.next(batchActions([addRequest(pRequest), setState(pRequest.id, LifeCycle.PENDING)]));
        const doc = DocumentDataBridge.getInnerProperty(pRequest.parameters.document, "document_reference");
        firestore().runTransaction((pTransaction) => {
            return pTransaction.get<T>(doc).then((pDocument) => {
                if(pDocument.exists) {
                    let actions = pRequest.parameters.actions;
                    if(Array.isArray(actions) === false) {
                        actions = [actions];
                    }
                    const data = Bridge.MakePlainObject(pReducer(initialize(pDocument.data()!, doc) as T, actions.map((pAction:any) => Bridge.MakePlainObject(pAction))));
                    data.sys_edit_dt = pSysData.sys_edit_dt;
                    data.sys_edit_user_id = pSysData.sys_edit_user_id;
                    pTransaction.update(doc, data);
                    return data;
                }
                else {
                    return null;
                }
            });
        }).then((pResult) => {
            pSubscriber.next(setResponse(pRequest.id, initialize(pResult as T, doc)));
            pSubscriber.complete();
        }).catch(function(pError:any) {
            pSubscriber.next(requestFailed(pRequest.id, pError));
            pSubscriber.error(pError);
        });
    });
}

export function getDocumentEpic<T extends IDocumentData>(pRequest:IBridgeRequest<any>) {
    return new Observable<any>(function(pSubscriber) {
        pSubscriber.next(batchActions([addRequest(pRequest), setState(pRequest.id, LifeCycle.PENDING)]));
        const collRef = firestore().collection(pRequest.path);
        const docRef = collRef.doc(pRequest.parameters.id);
        docRef.get().then((pData) => {
            const data = (pData.data()!==undefined?initialize(pData.data() as T, docRef):null);
            pSubscriber.next(setResponse(pRequest.id, data));
            pSubscriber.complete();
        }).catch(function(pError:any) {
            pSubscriber.next(requestFailed(pRequest.id, pError));
            pSubscriber.error(pError);
        });
    });
}

export function runTransactionEpic(pRequest:IBridgeRequest<any>, pTransactionCallback:(pTransaction:any) => Promise<unknown> ) {
    return new Observable<any>(function(pSubscriber) {
        pSubscriber.next(batchActions([addRequest(pRequest), setState(pRequest.id, LifeCycle.PENDING)]));
        firestore().runTransaction(pTransactionCallback).then((pResult) => {
            pSubscriber.next(setResponse(pRequest.id, pResult));
            pSubscriber.complete();
        }).catch(function(pError:any) {
            pSubscriber.next(requestFailed(pRequest.id, pError));
            pSubscriber.error(pError);
        });
    });
}

export function snapshotDocumentEpic<T extends IDocumentData>(pRequest:IBridgeRequest<any>, pActionsObservable?:ActionsObservable<Action<any>>) {
    return new Observable<any>(function(pSubscriber) {
        pSubscriber.next(batchActions([addRequest(pRequest), setState(pRequest.id, LifeCycle.PENDING)]));
        const collRef = firestore().collection(pRequest.path);
        const docRef = collRef.doc(pRequest.parameters.id);
        const unsub = docRef.onSnapshot((pData) => {
            const data = (pData.data()!==undefined?initialize(pData.data() as T, docRef):null);
            pSubscriber.next(setResponse(pRequest.id, data));
        }, function(pError:any) {
            pSubscriber.next(requestFailed(pRequest.id, pError));
            pSubscriber.error(pError);
        }, pSubscriber.complete);
        if(pActionsObservable !== undefined) {
            pActionsObservable.pipe(
                ofType(CANCEL_REQUEST),
                mergeMap((pAction:any) => {
                    if(pRequest.id === pAction.payload.request_id) {
                        unsub();
                    }
                    return empty();
                })
            ).subscribe(pSubscriber);
        }
    });
}

export function queryDocumentsEpic<T extends IDocumentData>(pRequest:IBridgeRequest<any>) {
    return new Observable<any>(function(pSubscriber) {
        pSubscriber.next(batchActions([addRequest(pRequest), setState(pRequest.id, LifeCycle.PENDING)]));
        let query;
        if(pRequest.parameters.group === true) {
            query = firestore().collectionGroup(pRequest.path);
        }
        else {
            query = firestore().collection(pRequest.path);
        }
        let queries = pRequest.parameters.queries;
        if(queries.length === 3 && typeof queries[1] === "string") {
            queries = [queries];
        }
        for(let i = 0 , len = queries.length; i < len; i++) {
            query = query.where.apply(query, queries[i]) as firestore.CollectionReference;
        }
        if(typeof pRequest.parameters.orderBy === "string") {				
            query = query.orderBy(pRequest.parameters.orderBy) as firestore.CollectionReference;
        }
        else if(Array.isArray(pRequest.parameters.orderBy)) {
            query = query.orderBy.apply(query, pRequest.parameters.orderBy) as firestore.CollectionReference;
        }
        query.get().then((pData:firestore.QuerySnapshot) => {
            const data = pData.docs.map(function(pItem) { return initialize(pItem.data() as T, pItem.ref); });
            pSubscriber.next(setResponse(pRequest.id, data));
            pSubscriber.complete();
        }).catch(function(pError:any) {
            pSubscriber.next(requestFailed(pRequest.id, pError));
            pSubscriber.error(pError);
        });
    });
}

export function querySnapshotDocumentEpic<T extends IDocumentData>(pRequest:IBridgeRequest<any>, pActionsObservable?:ActionsObservable<Action<any>>) {
    return new Observable<any>(function(pSubscriber) {
        pSubscriber.next(batchActions([addRequest(pRequest), setState(pRequest.id, LifeCycle.PENDING)]));
        let query;
        if(pRequest.parameters.group === true) {
            query = firestore().collectionGroup(pRequest.path);
        }
        else {
            query = firestore().collection(pRequest.path);
        }
        let queries = pRequest.parameters.queries;
        if(typeof queries[0] === "string") {
            queries = [queries];
        }
        for(let i = 0 , len = queries.length; i < len; i++) {
            query = query.where.apply(query, queries[i]) as firestore.CollectionReference;
        }
        if(typeof pRequest.parameters.orderBy === "string") {				
            query = query.orderBy(pRequest.parameters.orderBy) as firestore.CollectionReference;
        }
        else if(Array.isArray(pRequest.parameters.orderBy)) {
            query = query.orderBy.apply(query, pRequest.parameters.orderBy) as firestore.CollectionReference;
        }
        const unsub = query.onSnapshot((pData) => {
            const data = pData.docs.map(function(pItem) { return initialize(pItem.data() as T, pItem.ref); });
            pSubscriber.next(setResponse(pRequest.id, data));
        }, function(pError:any) {
            pSubscriber.next(requestFailed(pRequest.id, pError));
            pSubscriber.error(pError);
        }, pSubscriber.complete);
        if(pActionsObservable !== undefined) {
            pActionsObservable.pipe(
                ofType(CANCEL_REQUEST),
                mergeMap((pAction:any) => {
                    if(pRequest.id === pAction.payload.request_id) {
                        unsub();
                    }
                    return empty();
                })
            ).subscribe(pSubscriber);
        }
    });
}

export function removeDocumentEpic(pRequest:IBridgeRequest<any>) {
    return new Observable<any>(function(pSubscriber) {
        pSubscriber.next(batchActions([addRequest(pRequest), setState(pRequest.id, LifeCycle.PENDING)]));
        const collRef = firestore().collection(pRequest.path);
        const docRef = collRef.doc(pRequest.parameters.id);
        docRef.delete().then(function() {
            pSubscriber.complete();
        }).catch(function(pError:any) {
            pSubscriber.next(requestFailed(pRequest.id, pError));
            pSubscriber.error(pError);
        });
    });
}

export function cancelAllRequests(pState:IBridgeState) {
    return new Observable(function(pSubscriber) {
        for(let i = 0, len = pState.requests.length; i < len; i++) {
            pSubscriber.next(cancelRequest(pState.requests[i].id));
        }
        pSubscriber.complete();
    });
}
