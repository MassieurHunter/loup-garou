import BaseModel from './BaseModel';

export default class PlayerModel extends BaseModel {

    getGameUid(){
        return this.get('gameUid');
    }

    getCode(){
        return this.get('code');
    }


}