import * as firebase from 'firebase';
import { IDocumentData } from './DocumentDataBridge';
import { AuthService } from './../auth/AuthService';


export class ServiceBridge<T extends IDocumentData> {
    collection_ref:firebase.firestore.CollectionReference<T>;
    auth:AuthService = new AuthService();

    constructor(collection_ref:firebase.firestore.CollectionReference) {
        this.collection_ref = collection_ref as firebase.firestore.CollectionReference<T>;
    }

    add(record:T) {
        // Inject an insert date and insert user => if already given it will be overwritten here!
        record.sys_insert_dt = Date.now();
        record.sys_insert_user_id = this.auth.getUserUID()!;
        return this.collection_ref.add(record);
    }

    getByPath(path:string):Promise<firebase.firestore.DocumentSnapshot<T>> {
        const paths = path.split('/');
        return this.collection_ref.doc(paths[paths.length-1]).get();
    }

    getById(id:string):Promise<firebase.firestore.DocumentSnapshot<T>> {
        return this.collection_ref.doc(id).get();
    }

    getByQuery(fieldPath:string|firebase.firestore.FieldPath, opStr:firebase.firestore.WhereFilterOp, value:any):Promise<firebase.firestore.QuerySnapshot<T>> {
        return this.collection_ref.where(fieldPath, opStr, value).get();
    }

    getList() {
        return this.collection_ref;
    }

    edit(record:T) {
        const key = record.key;
        delete record.key; // We remove the key so it's not duplicated in the database. It exists already as the document id, no need to store it in the object itself.
        // Inject an edit date and edit user => if already given it will be overwritten here!
        record.sys_edit_dt = Date.now();
        record.sys_edit_user_id = this.auth.getUserUID()!;
        return this.collection_ref.doc(key).update(record);
    }

    deleteById(id:string) {
        return this.collection_ref.doc(id).delete();
    }
}
