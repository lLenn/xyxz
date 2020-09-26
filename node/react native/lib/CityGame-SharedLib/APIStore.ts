import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import { enableBatching  } from 'redux-batched-actions';
import { createEpicMiddleware, combineEpics } from 'redux-observable';
import { APIEpic } from './APIEpic';
import { APIReducer } from './APIReducer';
import { initializeFirestore } from './Firestore';
import { firebaseConfig } from './FirebaseConfig';


export function initializeAPI<T>(reducer:any = APIReducer, epic:any, pConfig:any = firebaseConfig()) {
    const epicMiddleware = createEpicMiddleware();
    const store = createStore<unknown, any, { dispath: unknown; }, T>(enableBatching(reducer), applyMiddleware(epicMiddleware, thunk));
    if(epic !== undefined) {
        epicMiddleware.run(combineEpics(APIEpic, epic));
    }
    else {
        epicMiddleware.run(APIEpic as any);
    }
    initializeFirestore(pConfig);
    return store;
}
