import BaseModel from './BaseModel';
import PlayerModel from "./PlayerModel";
import RoleModel from "./RoleModel";

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

    getRolesForCasting(){

      return this.get('rolesForCasting');

    }

    getRolesForRunning(){

      return this.get('rolesForRunning');

    }

    getRolesModelForCasting(){

      let roles = this.getRolesForCasting();
      let rolesModel = [];

      for(let role of roles){
        rolesModel.push(new RoleModel(role))
      }

      return rolesModel;

    }

    getRolesModelForRunning(){

      let roles = this.getRolesForRunning();
      let rolesModel = [];

      for(let role of roles){
        rolesModel.push(new RoleModel(role))
      }

      return rolesModel;

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