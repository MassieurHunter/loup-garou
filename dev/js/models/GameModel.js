import BaseModel from './BaseModel';
import PlayerModel from "./PlayerModel";
import RoleModel from "./RoleModel";
import ABuilder from '../tools/ABuilder';
import LangModel from "./LangModel";
import Ajax from "../tools/Ajax";

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

	setProgress(progress) {
		this.set('progress', progress)
	}

	getProgress() {
		return this.get('progress', 0);
	}

	setCurrentRoleName(currentRoleName) {
		this.set('currentRoleName', currentRoleName)
	}

	getCurrentRoleName() {
		return this.get('currentRoleName', '');
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

			rolesListForRunning += Role.getName() + ', ';

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
		$('.roles-block').append(RoleAlertBlock);

	}

	displayProgress() {


		let roleName = new ABuilder(
			'h4',
			{
				'class': 'progress-role-name text-center'
			},
			this.getLangModel().getLine('current turn')
			+ ' '
			+ this.getCurrentRoleName()
		);


		let progressBar = new ABuilder(
			'div',
			{'class': 'progress'},
			new ABuilder(
				'div',
				{
					'class': 'progress-bar progress-bar-striped progress-bar-animated',
					'role': 'progressbar',
					'aria-valuemin': 0,
					'aria-valuemax': 100,
					'aria-valuenow': this.getProgress(),
					'style': 'width:' + this.getProgress() + '%'
				},
			)
		);

		$('.game-progress').html('');
		$('.game-progress').append(roleName);
		$('.game-progress').append(progressBar);

	}

	refreshVotes(nbVotes) {

		$('.vote-infos').html(
			new ABuilder(
				'div',
				{
					'class': 'alert alert-primary',
					'role': 'alert'
				},
				this.getLangModel().getLine('nb_votes') + ' ' + nbVotes + '/' + this.getNbPlayers())
		);
		
		if(nbVotes === this.getNbPlayers()){
			
			Ajax.post('vote/results', [], (response) => {})
			
		}

	}

}