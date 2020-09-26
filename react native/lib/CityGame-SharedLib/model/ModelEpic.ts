import { CityEpic } from "./masterdata/CityEpic";
import { GameDefinitionEpic } from "./masterdata/GameDefinitionEpic";
import { ObjectiveGroupDefinitionEpic } from "./masterdata/ObjectiveGroupDefinitionEpic";
import { ObjectiveDetailDefinitionEpic } from "./masterdata/ObjectiveDetailDefinitionEpic";
import { BookingInstanceEpic } from "./operational/BookingInstanceEpic";
import { SessionInstanceEpic } from "./operational/SessionInstanceEpic";
import { UserEpic } from "./auth/UserEpic";
import { combineEpics } from 'redux-observable';

export const ModelEpic = combineEpics(
    CityEpic,
    GameDefinitionEpic,
    ObjectiveGroupDefinitionEpic,
    ObjectiveDetailDefinitionEpic,
    BookingInstanceEpic,
    SessionInstanceEpic,
    UserEpic
);
