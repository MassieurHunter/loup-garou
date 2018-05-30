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

      rolesListForCasting += Role.getName() + ',';

    }

    for (let Role of rolesForRunning) {

      if(Role.hasFirstAction()) {

        rolesListForRunning += Role.getName() + ',';

      }

    }

    let rolesForCastingBlock = new ABuilder(
      'div',
      {
        'class': ''
      },
      Lang.getLine('casted_roles') + rolesListForCasting.substring(0, -1)
    );

    let rolesForRunningBlock = new ABuilder(
      'div',
      {
        'class': ''
      },
      Lang.getLine('roles_running_order') + rolesListForRunning.substring(0, -1)
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


    let rolesBlock = new ABuilder(
      'div',
      {
        'class': 'row roles-block',
      },
      new ABuilder(
        'div',
        {
          'class': 'col-lg-8 offset-lg-2 col-md-8 offset-md-2 mt-1',
        },
        RoleAlertBlock
      )
    );

    $('.waiting-for-start').remove();
    $('.play-game').append(rolesBlock);

  }

}