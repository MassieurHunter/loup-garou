import BaseModel from './BaseModel';
import RoleModel from './RoleModel';

export default class PlayerModel extends BaseModel {

    getPlayerUid(){
        return this.getInt('playerUid');
    }

    getName(){
        return this.get('name');
    }

    getRoleModel(){
        return new RoleModel(this.get('role'));
    }

}