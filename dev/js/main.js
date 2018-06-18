import Ajax from './tools/Ajax';
import Forms from './components/Forms';
import Noty from 'noty';
import GameModel from './models/GameModel';
import RoleModel from './models/RoleModel';
import PlayerModel from './models/PlayerModel';
import LangModel from "./models/LangModel";
import $ from 'jquery';
import * as io from 'socket.io-client';
import ABuilder from "./tools/ABuilder";

let loupGarou = {

	init() {
		this.bootstrap = require('bootstrap');
		this.forms = new Forms();
		this.player = new PlayerModel();
		this.game = new GameModel();

		Ajax.post('lang', [], (response) => {

			this.lang = new LangModel(response.data);

		});

		Noty.overrideDefaults({
			theme: 'bootstrap-v4',
			layout: 'bottom',
			timeout: 5000
		});

		this.listenCreateGame();
		this.listenThemeChange();
		this.play();
		this.stats();

	},

	listenCreateGame() {
		let range = $('.max-players-range');

		range.on('input', () => {
			$('.nb-max-players').html(range.val());

			let data = [{name: 'nbPlayers', value: range.val()}];

			Ajax.post('game/future/role', data, (response) => {
				$('.future-roles').html(response.data.roles);
				$('.alert-future-role').removeClass('d-none');

			});

		}).trigger('input')
	},

	listenThemeChange() {

		let themeSelector = $('.themes-selector');

		themeSelector.on('change', (event) => {

			let themeStyle = $('link[data-type="theme"]');
			themeStyle.attr('href', '');

			if (themeSelector.val() !== '') {
				themeStyle.attr('href', 'css/' + themeSelector.val() + '/bootstrap.min.css');

				let dataPost = [{name: 'theme', value: themeSelector.val()}];
				Ajax.post('player/theme', dataPost, (response) => {
				});
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
							this.socket.emit('playerJoined', response.data);

							$(window).bind('beforeunload', (e) => {
								return this.lang.getLine('sure_to_leave');
							});

						});
						break;

					case 'playerJoined' :

						let Player = new PlayerModel(message.player);
						this.game = new GameModel(message.game);
						this.game.setLang(this.lang.toJSON());

						if (Player.getPlayerUid() !== this.player.getPlayerUid()) {

							new Noty({
								type: 'info',
								text: this.lang.getLine('player_joined_game').replace('*playername*', Player.getName())
							}).show();


						}

						$('.nb-players').html(this.game.getNbPlayers());

						this.game.displayPlayers();

						if (this.game.isReadyToStart()) {

							this.socket.emit('gameStart', {
								game: this.game.toJSON(),
								player: this.player.toJSON()
							});

						}

						break;

					case 'playerRejoined' :

						let Player2 = new PlayerModel(message.player);
						this.game = new GameModel(message.game);

						if (Player2.getPlayerUid() !== this.player.getPlayerUid()) {

							new Noty({
								type: 'info',
								text: this.lang.getLine('player_rejoined_game').replace('*playername*', Player2.getName())
							}).show();

						} else {

							this.socket.emit('playerRejoined', {
								game: this.game.toJSON(),
								player: this.player.toJSON(),
							});


						}

						break;

					case 'connectedElseWhere':

						new Noty({
							type: 'warning',
							text: this.lang.getLine('connected_elsewhere'),
							timeout: false
						}).show();

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
							this.game.displayPlayers();
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

						} else if (this.player.getNewRoleModel().getModel() === 'insomniaque' && this.player.getNewRoleModel().getModel() === CurrentRole.getModel()) {

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

					case 'rebuildActions' :

						Ajax.post('player/actions/rebuild', [], (response) => {
						});

						break;

					case 'actionsFinished' :

						this.player.displayVote();

						break;

					case 'rebuildVote' :

						Ajax.post('player/vote/rebuild', [], (response) => {
						});

						break;

					case 'playerVoted' :

						this.game.refreshVotes(message.nbVotes);

						break;


					case 'playerCanceledVote' :

						let Canceler = new PlayerModel(message.player);
						this.game.refreshVotes(message.nbVotes);

						if (Canceler.getPlayerUid() === this.player.getPlayerUid()) {

							this.player.displayVote();

						}


						break;

				}
			})


		}
	},

	stats() {

		setTimeout(() => {
			this.overallStats();
			this.playerStats();
		}, 2000);

	},

	overallStats() {
		if ($('.overall-stats-table').length > 0) {

			Ajax.post('stats/overall', [], (response) => {

				let stats = response.data.stats;
				let table = [];

				for (let i in stats) {

					let playerLine = stats[i];
					let tds = [];

					for (let j in playerLine) {

						let stat = playerLine[j];

						let td = new ABuilder(
							'td',
							{'class': j.indexOf('_all') !== -1 ? 'font-weight-bold' : ''},
							stat
						);

						tds.push(td);

					}


					let tr = new ABuilder(
						'tr',
						{},
						tds
					);

					table.push(tr);

				}

				$('.overall-stats-table tbody').html('').append(table);

			});

		}
	},

	playerStats() {

		if ($('.player-uid-stat').length > 0) {

			let data = [
				{
					'name': 'playerUid',
					'value': $('.player-uid-stat').val()
				}
			];
			Ajax.post('stats/player', data, (response) => {

				let history = response.data.history;
				let stats = response.data.stats;

				let allGames = history.all;
				let startingTeamGames = history.startingTeams;
				let endingTeamGames = history.endingTeams;
				let startingRolesGames = history.startingRoles;
				let endingRolesGames = history.endingRoles;

				let allStats = stats.all;
				let startingTeamStats = stats.startingTeams;
				let endingTeamStats = stats.endingTeams;
				let startingRolesStats = stats.startingRoles;
				let endingRolesStats = stats.endingRoles;

				this.playerGlobalHistory(allGames);
				this.playerTeamRoleHistory(startingTeamGames, this.lang.getLine('stat_starting_team'), '.player-games-starting-team');
				this.playerTeamRoleHistory(endingTeamGames, this.lang.getLine('stat_ending_team'), '.player-games-ending-team');
				this.playerTeamRoleHistory(startingRolesGames, this.lang.getLine('stat_starting_role'), '.player-games-starting-role');
				this.playerTeamRoleHistory(endingRolesGames, this.lang.getLine('stat_ending_role'), '.player-games-ending-role');

			});

		}
	},

	playerGlobalHistory(games) {
		let allGamesTable = [];

		for (let i in games) {

			let gameLine = games[i];

			let tds = [];

			for (let j in gameLine) {

				let stat = gameLine[j];

				let isWinnerCell = j.indexOf('winner') !== -1;
				let isWinner = isWinnerCell && stat === true;
				let isLooser = isWinnerCell && stat === false;

				let isPlayersCell = j.indexOf('players') !== -1;

				let cell = stat;

				if (isWinner) {
					cell = this.lang.getLine('stat_win');
				} else if (isLooser) {
					cell = this.lang.getLine('stat_loose');
				} else if (isPlayersCell) {

					let arrPlayers = [];

					for (let playerUid in stat) {

						let player = stat[playerUid];
						let playerName = player.name.trim().charAt(0).toUpperCase() + player.name.trim().slice(1);
						arrPlayers.push(playerName);

					}

					arrPlayers.sort();
					cell = arrPlayers.join(', ');

				}

				let td = new ABuilder(
					'td',
					{'class': 'text-capitalize ' + (isWinner ? 'table-success' : (isLooser ? 'table-danger' : ''))},
					cell
				);

				tds.push(td);

			}

			let tr = new ABuilder(
				'tr',
				{},
				tds
			);

			allGamesTable.push(tr);

		}


		$('.player-games-all tbody').html('').append(allGamesTable);
	},

	playerTeamRoleHistory(games, sectionTitle, container) {
		let allTables = [];

		let title = new ABuilder(
			'h2',
			{'class': 'mt-5 text-center text-uppercase'}
			,
			sectionTitle
		);

		for (let teamRole in games) {

			let teamRoleTitle = new ABuilder(
				'h3',
				{'class': 'mt-3 text-capitalize'}
				,
				teamRole
			);

			let thead = new ABuilder(
				'thead',
				{},
				new ABuilder(
					'tr',
					{},
					[
						new ABuilder(
							'th',
							{},
							this.lang.getLine('stat_nb_players')
						),
						new ABuilder(
							'th',
							{},
							this.lang.getLine('stat_players_list')
						),
						new ABuilder(
							'th',
							{},
							this.lang.getLine('stat_result')
						),
					]
				)
			);

			let gamesForRoleOrTeam = [];

			let startingGames = games[teamRole];

			for (let gameLine of startingGames) {

				let tds = [];

				for (let j in gameLine) {

					let stat = gameLine[j];

					let isWinnerCell = j.indexOf('winner') !== -1;
					let isWinner = isWinnerCell && stat === true;
					let isLooser = isWinnerCell && stat === false;

					let isPlayersCell = j.indexOf('players') !== -1;

					let cell = stat;

					if (isWinner) {
						cell = this.lang.getLine('stat_win');
					} else if (isLooser) {
						cell = this.lang.getLine('stat_loose');
					} else if (isPlayersCell) {

						let arrPlayers = [];

						for (let playerUid in stat) {

							let player = stat[playerUid];
							let playerName = player.name.trim().charAt(0).toUpperCase() + player.name.trim().slice(1);
							arrPlayers.push(playerName);

						}

						arrPlayers.sort();
						cell = arrPlayers.join(', ');

					}

					let td = new ABuilder(
						'td',
						{'class': 'text-capitalize ' + (isWinner ? 'table-success' : (isLooser ? 'table-danger' : ''))},
						cell
					);

					tds.push(td);

				}

				let tr = new ABuilder(
					'tr',
					{},
					tds
				);

				gamesForRoleOrTeam.push(tr);

			}

			let gameTable = new ABuilder(
				'table',
				{'class': 'table table-hover table-striped table-bordered player-stats-table'},
				[
					thead,
					new ABuilder(
						'tbody',
						{},
						gamesForRoleOrTeam
					)
				]
			);

			allTables.push(teamRoleTitle);
			allTables.push(gameTable);

		}

		$(container)
			.html('')
			.append(title)
			.append(allTables);
	},

};

loupGarou.init();