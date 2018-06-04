import BaseModel from './BaseModel';
import PlayerModel from "./PlayerModel";
import RoleModel from "./RoleModel";
import ABuilder from '../tools/ABuilder';
import LangModel from "./LangModel";

export default class GameModel extends BaseModel {

  setLang(lang) {
    this.set('lang', lang);
  }

  getLangModel() {
    return new LangModel(this.get('lang', {}));
  }

  getGameUid() {
    return this.getInt('gameUid');
  }

  getCode() {
    return this.get('code');
  }

  getNbPlayers() {
    return this.getInt('nbPlayers');
  }

  getMaxPlayers() {
    return this.getInt('maxPlayers');
  }

  isReadyToStart() {
    return this.getNbPlayers() === this.getMaxPlayers();
  }

  getRolesForCasting() {

    return this.get('rolesForCasting');

  }

  getRolesForRunning() {

    return this.get('rolesForRunning');

  }

  getRolesModelForCasting() {

    let roles = this.getRolesForCasting();
    let rolesModel = [];

    for (let role of roles) {
      rolesModel.push(new RoleModel(role))
    }

    return rolesModel;

  }

  getRolesModelForRunning() {

    let roles = this.getRolesForRunning();
    let rolesModel = [];

    for (let role of roles) {
      rolesModel.push(new RoleModel(role))
    }

    return rolesModel;

  }

  getPlayers() {
    return this.get('players')
  }

  getPlayersModel() {
    let players = this.getPlayers();
    let playersModel = [];

    for (let player of players) {
      playersModel.push(new PlayerModel(player))
    }

    return playersModel;

  }

  displayRoles() {

    let Lang = this.getLangModel();
    let rolesForCasting = this.getRolesModelForCasting();
    let rolesForRunning = this.getRolesModelForRunning();

    let rolesListForCasting = '';
    let rolesListForRunning = '';

    for (let Role of rolesForCasting) {

      rolesListForCasting += Role.getName() + ', ';

    }

    for (let Role of rolesForRunning) {

      if(Role.hasFirstAction()) {

        rolesListForRunning += Role.getName() + ', ';

      }

    }

    let rolesForCastingBlock = new ABuilder(
      'div',
      {
        'class': ''
      },
      Lang.getLine('casted_roles') + rolesListForCasting.substr(0, rolesListForCasting.length - 2)
    );

    let rolesForRunningBlock = new ABuilder(
      'div',
      {
        'class': ''
      },
      Lang.getLine('roles_running_order') + rolesListForRunning.substr(0, rolesListForRunning.length - 2)
    );

    let RoleAlertBlock = new ABuilder(
      'div',
      {
        'class': 'alert alert-primary'
      },
      [
        rolesForCastingBlock,
        rolesForRunningBlock
      ]
    );

    $('.waiting-for-start').remove();
    $('.roles-block > div').append(RoleAlertBlock);

  }

}