import { generateUID } from '../../utils/functions';
import { Action } from 'redux';


export enum LifeCycle {
    WAITING = 0,
    PENDING,
    DONE,
    FAILED
}

export enum Request {
    ADD = "add",
    GET = "get",
    QUERY = "query",
    UPDATE = "update",
    TRANSACTION = "transaction",
    REMOVE = "remove",
    SNAPSHOT = "snapshot",
    SET = "set"
}

export type Query = []|[string|string[], "<" | "<=" | "==" | ">=" | ">" | "array-contains" | "in" | "array-contains-any", any];
export type OrderBy = string|[string, "asc"|"desc"];

export interface Parameters {}

export interface AddParameters extends Parameters {
    document:any;
    key?:string;
}

export interface UpdateParameters extends Parameters {
    key?:string;
    document:any;
}

export interface UpdateTransactionParameters extends UpdateParameters {
    actions:Action<any>|Action<any>[];
}

export type TransactionParameters = Parameters;

export interface GetParameters extends Parameters {
    id:string;
}

export interface SnapshotParameters extends GetParameters {
    target:string;
}

export interface QueryParameters extends Parameters {
    queries:Query[];
    orderBy?:OrderBy;
    group?:boolean;
}

export interface RemoveParameters extends Parameters {
    id:string;
}

export interface IBridgeRequest<U extends Parameters> {
    id:string;
    state:LifeCycle;
    path:string; // Belongs in the parameters object
    request:Request;
    parameters:U;
    response:any;
    error:any;
}

export class BridgeRequest {
    static create<U>(pRequest:Request, pPath:string, pParameters:U):IBridgeRequest<U> {
        return {
            id: generateUID(),
            state: LifeCycle.WAITING,
            request: pRequest,
            path: pPath,
            parameters: pParameters,
            response: undefined,
            error: undefined
        };
    }

    static isBridgeRequest(pObject:any) {
        if(pObject.id !== undefined && pObject.state !== undefined && pObject.request !== undefined && pObject.path !== undefined && pObject.parameters !== undefined) {
            return true;
        }

        return false;
    }
}
