import * as firebase from 'firebase';
import { IDocumentData, DocumentDataBridge } from './DocumentDataBridge';


const EXCLUDE_ROOT = ["key", "document_reference", "collections", "inner"];
const EXCLUDE_CHILDREN = ["document_reference", "collections", "inner"];

export class Bridge {
    static Map<T extends IDocumentData>(pDocumentReference:firebase.firestore.QuerySnapshot<T>):T[] {
        return pDocumentReference.docs.map((pDoc) => Bridge.MakeInstance(pDoc.data(), pDoc.ref));
    }

    static MakeInstance<T extends IDocumentData>(pObject:T, pDocumentReference:firebase.firestore.DocumentReference<T>|null = null):T {
        if(pDocumentReference !== null) {
            pObject.key = pDocumentReference.id;
        }
        DocumentDataBridge.setInnerProperty(pObject, "document_reference_bridge", pDocumentReference);
        if(pDocumentReference !== null) {
            DocumentDataBridge.setInnerProperty(pObject, "document_reference", pDocumentReference);
        }
        return pObject;
    }

    static MakePlainObject(object:any, pDepth = 0):any {
        if(Array.isArray(object)) {
            const array:any[] = [];
            for(let i = 0, len = object.length; i < len; i++) {
                array[i] = Bridge.MakePlainObject(object[i], pDepth + 1);
            }
            return array;
        }
        else if(object !== null && typeof object === "object") {
            const newObject:any = {};
            for(const prop in object) {
                if(((pDepth === 0 && EXCLUDE_ROOT.indexOf(prop) === -1) || (pDepth !== 0 && EXCLUDE_CHILDREN.indexOf(prop) === -1)) && object.hasOwnProperty(prop)) {
                    if(typeof object[prop] !== "function") {
                        newObject[prop] = Bridge.MakePlainObject(object[prop], pDepth + 1);
                    }
                }
            }
            return newObject;
        }
        return object;
    }
}
