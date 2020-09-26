import { generateUID } from '../../utils/functions';
import { LifeCycle, Parameters, IBridgeRequest } from './BridgeRequest';


export interface IBridgeCombinedRequest {
    id:string;
    state:LifeCycle;
    initial_request:IBridgeRequest<Parameters>;
    requests:string[];
    response:any;
    error:any;
}

export class BridgeCombinedRequest {
    static create(pInitialRequest:IBridgeRequest<Parameters>):IBridgeCombinedRequest {
        return {
            id: generateUID(),
            state: LifeCycle.WAITING,
            initial_request: pInitialRequest,
            requests: [pInitialRequest.id],
            response: undefined,
            error: undefined
        };
    }

    static isBridgeCombinedRequest(pObject:any) {
        if(pObject.id !== undefined && pObject.state !== undefined && pObject.initial_request !== undefined && pObject.requests !== undefined && pObject.initial_request.id === pObject.requests[0]) {
            return true;
        }

        return false;
    }
}
