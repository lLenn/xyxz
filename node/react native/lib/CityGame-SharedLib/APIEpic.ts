import { ModelEpic } from "./model/ModelEpic";
import { AuthEpic } from "./service/auth/AuthEpic";
import { combineEpics } from 'redux-observable';


export const APIEpic = combineEpics(ModelEpic, AuthEpic);
