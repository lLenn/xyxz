import {
    CONSTANTS_GRID_COLUMN_HEADER_DEFAULT,
    CONSTANTS_GRID_COLUMN_HEADER_SYS_INSERT_DT, CONSTANTS_GRID_COLUMN_HEADER_SYS_INSERT_USER_ID,
    CONSTANTS_GRID_COLUMN_HEADER_SYS_EDIT_DT, CONSTANTS_GRID_COLUMN_HEADER_SYS_EDIT_USER_ID
} from '../../Constants';


export interface IDocumentData extends ISysData {
    key?:string;
    inner?:any;
}

export interface ISysData {    
    sys_insert_dt?:number;
    sys_insert_user_id?:string;
    sys_edit_dt?:number;
    sys_edit_user_id?:string;
}

export class DocumentDataBridge {

    static getDefaultSysFields() {
        return {
            sys_insert_dt: Date.now(),
            sys_insert_user_id: 'DefaultInsertUser',
            sys_edit_dt: Date.now(),
            sys_edit_user_id: 'DefaultEditUser'
        };
    }

    static getEmptyDefaultSysFields() {
        return {
            sys_insert_dt: 0, // todo: check if needed? also in other components.. If added here, the screen shows "1970-01-01, 01:00:00".
            sys_insert_user_id: '',
            sys_edit_dt: 0, // todo: check if needed? also in other components.. If added here, the screen shows "1970-01-01, 01:00:00".
            sys_edit_user_id: ''
        };
    }

    static getHeader(pKey: string) {
        switch (pKey) {
            case 'sys_insert_dt': return CONSTANTS_GRID_COLUMN_HEADER_SYS_INSERT_DT;
            case 'sys_insert_user_id': return CONSTANTS_GRID_COLUMN_HEADER_SYS_INSERT_USER_ID;
            case 'sys_edit_dt': return CONSTANTS_GRID_COLUMN_HEADER_SYS_EDIT_DT;
            case 'sys_edit_user_id': return CONSTANTS_GRID_COLUMN_HEADER_SYS_EDIT_USER_ID;
            default: return CONSTANTS_GRID_COLUMN_HEADER_DEFAULT;
        }
    }

    static clearInner<T extends IDocumentData>(pObject:T) {
        pObject.inner = {};
    }

    static addInner<T extends IDocumentData>(pObject:T) {
        if(pObject.inner === undefined) {
            pObject.inner = {}; 
        }
    }

    static setInnerProperty<T extends IDocumentData>(pObject:T, pName:string, pValue:any) {
        DocumentDataBridge.addInner(pObject);
        pObject.inner[pName] = pValue;
    }

    static getInnerProperty<T extends IDocumentData>(pObject:T, pName:string, pDefault?:any) {
        DocumentDataBridge.addInner(pObject);
        if(pObject.inner && pObject.inner[pName] !== undefined) {
            return pObject.inner[pName];
        }
        else if(pObject.inner && pDefault !== undefined) {
            pObject.inner[pName] = pDefault;
            return pDefault;
        }
        else {
            return undefined;
        }
    }
}
