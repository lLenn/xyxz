import { AddressDefinition, IAddressDefinition } from './AddressDefinition';
import { IObjectiveGroupDefinition } from './ObjectiveGroupDefinition';
import { IObjectiveDetailDefinition } from './ObjectiveDetailDefinition';
import { ImplementedLanguageDefinition, IImplementedLanguageDefinition } from './ImplementedLanguageDefinition';
import { IRecordDeleteCheckPromise } from '../general/GeneralTypes';
import { ObjectiveGroupDefinitionService } from '../../service/masterdata/ObjectiveGroupDefinitionService';
import { IDocumentData, DocumentDataBridge } from '../../service/bridge/DocumentDataBridge';
import { generateUID } from '../../utils/functions';
import { I18NCodeDefinition, II18NCodeDefinition } from './I18NCodeDefinition';
import { CONFIG_DEFAULT_ACTIVATION_DISTANCE } from '../../Config';


export interface IGameDefinition extends IDocumentData {
    game_definition_code: string;
    game_definition_name: string;
    flag_enabled: boolean;
    description: string;
    maximum_duration: number;
    start_address: string; // Contains an address code (AddressDefinition.address_code)!
    end_address: string;  // Contains an address code (AddressDefinition.address_code)!
    end_game_msg_success: string;
    end_game_msg_fail: string;
    activation_distance: number; // Unis is m.
    implemented_language_definitions: IImplementedLanguageDefinition[];
    I18N_code_definitions: II18NCodeDefinition[];
    address_definitions: IAddressDefinition[];
}

export class GameDefinition {

    // Is an object that contains the max field length per property.
    static max_field_length_object = {
        game_definition_code: 50,
        game_definition_name: 50,
        description: 1000,
        maximum_duration: 3, // Unit is minutes.
        end_game_msg_success: 500,
        end_game_msg_fail: 500,
        activation_distance: 8 // We need to test around the Globe, so should be able to set above 40 000 km (=40 000 000 m).
    };

    static tooltip_object = {
        flag_enabled: 'If unchecked, this game will not be playable !! Uncheck only in case of serious problems that affect this particular game.',
        end_game_msg_success: 'Message that will be shown when the game is successfully finished.',
        end_game_msg_fail: "Message that will be shown when the game is NOT successfully finished. This means the game didn't finish within the maximum duration.",
        activation_distance: "Distance (in m) which is used (if not 0) on the corresponding objective group & detail instances," +
                             " BUT only if no activation distance is set on all lower levels AND no test distance is set." +
                             " Activation means the user is able to start the group or detail on the map."
    };

    static getDefault():IGameDefinition {
        return Object.assign({
            key: generateUID(),
            game_definition_code: 'default game def code',
            game_definition_name: 'default game def name',
            flag_enabled: false,
            description: 'default description',
            maximum_duration: 120,
            start_address: 'default start address',
            end_address: 'default end address',
            end_game_msg_success: 'default end game message success',
            end_game_msg_fail: 'default end game message fail',
            activation_distance: CONFIG_DEFAULT_ACTIVATION_DISTANCE,
            implemented_language_definitions: [ ImplementedLanguageDefinition.getDefault() ],
            I18N_code_definitions: [ I18NCodeDefinition.getDefault() ],
            address_definitions: [ AddressDefinition.getDefault() ]
        }, DocumentDataBridge.getDefaultSysFields()
        );
    }

    // Notice there is already a "getDefault()" method in "GameDefinitionService.ts".
    static getEmptyDefault():IGameDefinition {
        return Object.assign({
            key: '',
            game_definition_code: '',
            game_definition_name: '',
            flag_enabled: false,
            description: '',
            maximum_duration: 0,
            start_address: '',
            end_address: '',
            end_game_msg_success: '',
            end_game_msg_fail: '',
            activation_distance: CONFIG_DEFAULT_ACTIVATION_DISTANCE,
            implemented_language_definitions: [],
            I18N_code_definitions: [],
            address_definitions: []
        }, DocumentDataBridge.getEmptyDefaultSysFields()
        );
    }

    static checkAllowDeleteByKey(record_key: string, parent_ids: string[]): Promise<IRecordDeleteCheckPromise> {
        // We check if the subcollection of ObjectiveGroupDefinitons has any documents.
        // If yes, we don't allow to delete. If no, delete is allowed.
        // Promise is needed because we need to wait for the asynchronous database call.
        return new Promise<IRecordDeleteCheckPromise>( (resolve, reject) => {
            // parent_ids[0] is the City, record_key is the GameDefinition.
            const objective_group_definition_service = new ObjectiveGroupDefinitionService(parent_ids[0], record_key);
            const delete_promise: IRecordDeleteCheckPromise = { flag_allow_delete: false, warning_msg_why_delete_not_possible: "" };

            // Here we use get() because we need to get the data only once, we don't need to listen for realtime updates.
            objective_group_definition_service.getList().get().then( (querySnapshot: any) => {
                if (!(querySnapshot.size > 0)) {
                    // If the subcollection has no document, than allow a delete.
                    delete_promise.flag_allow_delete = true;
                }
                else {
                    let msg = 'Not possible to delete because of subcollection "objective_group_definitions" with the following ' + querySnapshot.size + ' documents:\n';

                    querySnapshot.forEach((doc: any) => {
                        // Notice 'ogd' is an actual instance of ObjectiveGroupDefinition (is realized with the code in folder "bridge").
                        const ogd = doc.data();

                        msg += 'Code: "' + ogd.objective_group_definition_code + '", Name: "' + ogd.objective_group_definition_name + '"\n';
                    });

                    delete_promise.flag_allow_delete = false;
                    delete_promise.warning_msg_why_delete_not_possible = msg;
                }

                resolve(delete_promise);
            } );
        } );
    }

    static getHeader(pKey: string) {
        switch (pKey) {
            case 'key': return 'Key';
            case 'game_definition_code': return 'Code';
            case 'game_definition_name': return 'Name';
            case 'flag_enabled': return 'Enabled';
            case 'description': return 'Description';
            case 'maximum_duration': return 'Max. duration (in min)';
            case 'start_address': return 'Start address';
            case 'end_address': return 'End address';
            case 'end_game_msg_success': return 'Success msg';
            case 'end_game_msg_fail': return 'Fail msg';
            case 'activation_distance': return 'Def. activation dist.';
            default: return DocumentDataBridge.getHeader(pKey);
        }
    }

    static getObjectiveGroupDefinition(pObject:IGameDefinition, pDocRef: firebase.firestore.DocumentReference<IObjectiveGroupDefinition>):IObjectiveGroupDefinition|null {
        const objective_group_definitions = DocumentDataBridge.getInnerProperty(pObject, "objective_group_definitions");
        for (let i = 0, len = objective_group_definitions.length; i < len; i++) {
            if (pDocRef.path === objective_group_definitions[i].document_reference.path) {
                return objective_group_definitions[i];
            }
        }

        return null;
    }

    static getObjectiveDetailDefinition(pObject:IGameDefinition, pDocRef:firebase.firestore.DocumentReference<IObjectiveDetailDefinition>):IObjectiveDetailDefinition|null {
        const objective_group_definitions = DocumentDataBridge.getInnerProperty(pObject, "objective_group_definitions");
        for (let i = 0, len = objective_group_definitions.length; i < len; i++) {
            if (pDocRef.path.indexOf(objective_group_definitions[i].document_reference.path) === 0) {
                return objective_group_definitions[i].getObjectiveDetailDefinition(pDocRef);
            }
        }

        return null;
    }

    static getAddressDefinitionByCode(pObject:IGameDefinition, pCode:string) {
        for (let i = 0, len = pObject.address_definitions.length; i < len; i++) {
            if (pObject.address_definitions[i].address_code === pCode) {
                return pObject.address_definitions[i];
            }
        }

        return null;
    }
}
