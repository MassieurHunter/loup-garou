import Ajax from './tools/Ajax';
import Forms from './components/Forms';
import Noty from 'noty';
import GameModel from './models/GameModel';
import RoleModel from './models/RoleModel';
import PlayerModel from './models/PlayerModel';
import LangModel from "./models/LangModel";
import $ from 'jquery';

let loupGarou = {

	init() {
		this.bootstrap = require('bootstrap');
		this.forms = new Forms();
		this.player = new PlayerModel();
		this.game = new GameModel();
		this.lang = new LangModel();

		Noty.overrideDefaults({
			theme: 'bootstrap-v4',
			layout: 'bottom',
			timeout: 5000
		});

		this.listenCreateGame();
		this.listenThemeChange();
		this.play();

	},

	listenCreateGame() {
		let range = $('.max-players-range');

		range.on('input', () => {
			$('.nb-max-players').html(range.val());
		}).trigger('input')
	},

	listenThemeChange() {

		let themeSelector = $('.themes-selector');

		themeSelector.on('change', (event) => {

			let themeStyle = $('link[data-type="theme"]');
			themeStyle.attr('href', '');

			if (themeSelector.val() !== '') {
				themeStyle.attr('href', 'css/' + themeSelector.val() + '/bootstrap.min.css');
			}

		});

	},

	play() {
		if ($('#play-socket').val() === '1') {
			let getUrl = window.location;
			let baseUrl = getUrl.protocol + "//" + getUrl.host;
			this.socket = io.connect(baseUrl + ':3000');
			this.socket.on('message', (message) => {
				switch (message.type) {
					case 'connection' :
						Ajax.post('socket/connection', [], (response) => {

							this.player = new PlayerModel(response.data.player);
							this.game = new GameModel(response.data.game);
							this.lang = new LangModel(response.data.lang);
							this.socket.emit('playerJoined', response.data);

						});
						break;

					case 'playerJoined' :

						let Player = new PlayerModel(message.player);
						this.game = new GameModel(message.game);

						new Noty({
							type: 'info',
							text: this.lang.getLine('player_joined_game').replace('*playername*', Player.getName())
						}).show();

						$('.nb-players').html(this.game.getNbPlayers());

						if (this.game.isReadyToStart()) {

							this.socket.emit('gameStart', {
								game: this.game.toJSON(),
								player: this.player.toJSON()
							});

						}

						break;

					case 'gameStart':

						Ajax.post('game/start', [], () => {

							this.socket.emit('rolesInfos', {
								game: this.game.toJSON(),
							});
						});


						break;

					case 'rolesInfos' :

						Ajax.post('roles/infos', [], (response) => {

							this.game = new GameModel(response.data.game);
							this.player.setGame(response.data.game);
							this.player.setRole(response.data.role);
							this.game.setLang(this.lang.toJSON());
							this.player.setLang(this.lang.toJSON());

							this.game.displayRoles();
							this.player.displayRoleName();
							this.socket.emit('roleTurn', {
								game: this.game.toJSON(),
								player: this.player.toJSON(),
							});

						});

						break;

					case 'roleTurn' :

						let CurrentRole = new RoleModel(message.role);

						this.game.setCurrentRoleName(CurrentRole.getName());
						this.game.setProgress(message.progress);
						this.game.displayProgress();

						if (this.player.getRoleModel().getModel() === CurrentRole.getModel()) {

							if (this.player.getRoleModel().hasFirstAction() || this.player.getRoleModel().hasSecondAction()) {

								this.player.displayAction('first');

							} else {

								this.socket.emit('playerFinishedTurn', {

									player: this.player.toJSON(),
									game: this.game.toJSON(),
									role: this.game.toJSON(),

								});

							}

						} else if(this.player.getNewRoleModel().getModel() === 'insomniaque' && this.player.getNewRoleModel().getModel() === CurrentRole.getModel()){

							this.player.displayAction('first', true);
							
						}

						break;

					case 'playerPlayedFirstAction' :

						let Role = new RoleModel(message.role);
						let NewRole = new RoleModel(message.newRole);
						let doppel = message.doppel;

						if (this.player.getRoleModel().getModel() === Role.getModel()) {

							if (this.player.getRoleModel().hasSecondAction()) {

								this.player.displayAction('second');

							} else if (doppel && this.player.getRoleModel().getModel() === 'doppelganger') {

								console.log('nouveau role du doppel', NewRole.getName())

								this.player.setNewRole(NewRole.toJSON());

								if (this.player.getNewRoleModel().getModel() !== 'insomniaque' && (this.player.getNewRoleModel().hasFirstAction() || this.player.getNewRoleModel().hasSecondAction())) {
									
									this.player.displayAction('first', true);
								
								} else {

									this.socket.emit('playerFinishedTurn', {

										player: this.player.toJSON(),
										game: this.game.toJSON(),
										role: this.player.getRoleModel().toJSON(),

									});
									
								}

							} else {

								this.socket.emit('playerFinishedTurn', {

									player: this.player.toJSON(),
									game: this.game.toJSON(),
									role: this.player.getRoleModel().toJSON(),

								});
							}

						} else if (this.player.getNewRoleModel().getModel() === Role.getModel()) {

							if (this.player.getNewRoleModel().hasSecondAction()) {

								this.player.displayAction('second', true);

							} else {

								this.socket.emit('playerFinishedTurn', {

									player: this.player.toJSON(),
									game: this.game.toJSON(),
									role: this.game.toJSON(),

								});
							}

						}

						break;

					case 'playerFinishedTurn' :

						this.player.finishTurn();

						break;

					case 'actionsFinished' :

						this.player.displayVote();

						break;

					case 'playerVoted' :

						console.log('plop');
						this.game.refreshVotes(message.nbVotes);
						
						break;

				}
			})


		}
	}


};

loupGarou.init();