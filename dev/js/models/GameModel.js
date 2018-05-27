import BaseModel from './BaseModel';
import PlayerModel from "./PlayerModel";

export default class GameModel extends BaseModel {

    getGameUid(){
        return this.getInt('gameUid');
    }

    getCode(){
        return this.get('code');
    }

    getNbPlayers(){
        return this.getInt('nbPlayers');
    }

    getMaxPlayers(){
        return this.getInt('maxPlayers');
    }

    isReadyToStart(){
        return this.getNbPlayers() === this.getMaxPlayers();
    }

    getPlayers(){
        return this.get('players')
    }

    getPlayersModel(){
        let players = this.getPlayers();
        let playersModel = [];

        for(let player of players){
            playersModel.push(new PlayerModel(player))
        }

        return playersModel;

    }

}