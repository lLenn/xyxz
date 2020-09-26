import { Reducer } from './Reducer';
import { Epic } from './Epic';
import { initializeAPI } from 'app-shared';
import { IState } from '../redux/State';
import { firebaseConfig } from 'app-shared';


export function createStore() {
    return initializeAPI<IState>(Reducer, Epic, firebaseConfig(__DEV__));
}
