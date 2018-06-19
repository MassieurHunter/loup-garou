import Ajax from '../tools/Ajax';
import ABuilder from "../tools/ABuilder";
import $ from "jquery";
import Highcharts from 'highcharts';

export default class Stats {

	constructor(lang) {

		this.lang = lang;

		this.setHighchartsTheme();

		this.overallStats();
		this.renderPlayer();
	}

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
	}

	renderPlayer() {

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

				$('.loader-container').remove();
				this.playerGameHistory(history);
				this.playerStats(stats);

			});

		}
	}

	playerGameHistory(history) {

		let allGames = history.all;
		let startingTeamGames = history.startingTeams;
		let endingTeamGames = history.endingTeams;
		let startingRolesGames = history.startingRoles;
		let endingRolesGames = history.endingRoles;

		this.playerGlobalHistory(allGames);
		this.playerTeamRoleHistory(startingTeamGames, this.lang.getLine('stat_starting_team'));
		this.playerTeamRoleHistory(endingTeamGames, this.lang.getLine('stat_ending_team'));
		this.playerTeamRoleHistory(startingRolesGames, this.lang.getLine('stat_starting_role'));
		this.playerTeamRoleHistory(endingRolesGames, this.lang.getLine('stat_ending_role'));

		$('.player-stats-table-container').removeClass('d-none');

	}

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
	}

	playerTeamRoleHistory(games, sectionTitle) {
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

		$('.player-games-team-roles-container')
			.append(title)
			.append(allTables);
	}

	playerStats(stats) {

		let allStats = stats.all;
		let startingTeamStats = stats.startingTeams;
		let endingTeamStats = stats.endingTeams;
		let startingRolesStats = stats.startingRoles;
		let endingRolesStats = stats.endingRoles;

		this.playerGlobalCharts(allStats);
		this.playerTeamRoleCharts(startingTeamStats);
		this.playerTeamRoleCharts(endingTeamStats);
		this.playerTeamRoleCharts(startingRolesStats);
		this.playerTeamRoleCharts(endingRolesStats);

		$('.player-stats-charts-container').removeClass('d-none');

	}

	playerGlobalCharts(stats) {

		let chartContainer = new ABuilder(
			'div',
			{
				'id': 'global-chart-container',
				'class': 'player-chart-container',
			},
			''
		);

		$('.player-stats-charts-container').append(chartContainer);

		Highcharts.chart('global-chart-container', {
			chart: {
				plotBackgroundColor: 'transparent',
				plotBorderWidth: 1,
				plotShadow: true,
				type: 'pie'
			},
			title: {
				text: this.lang.getLine('stat_chart_global')
			},
			tooltip: {
				pointFormat: '{point.y} ({point.percentage:.1f}%)'
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: true,
						format: '<b>{point.name}</b> : {point.y}',
						style: {
							color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
						}
					}
				}
			},
			series: [{
				name: this.lang.getLine('stat_chart_ratio'),
				colorByPoint: true,
				data: [{
					name: this.lang.getLine('stat_wins'),
					y: stats.wins,
				}, {
					name: this.lang.getLine('stat_losses'),
					y: stats.losses,
				}
				]
			}]
		});

	}

	playerTeamRoleCharts(stats) {

	}

	playerCharts(stats, sectionTitle) {

	}

	setHighchartsTheme() {
		Highcharts.theme = {
			colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572',
				'#FF9655', '#FFF263', '#6AF9C4'],
			chart: {
				backgroundColor: {
					color: 'transparent',
					// linearGradient: [0, 0, 500, 500],
					// stops: [
					// 	[0, 'rgb(255, 255, 255)'],
					// 	[1, 'rgb(240, 240, 255)']
					// ]
				},
				borderWidth: 0,
				borderColor: 'transparent',
			},
			title: {
				// style: {
				// 	color: '#000',
				// 	font: 'bold 16px "Trebuchet MS", Verdana, sans-serif'
				// }
			},
			subtitle: {
				// style: {
				// 	color: '#666666',
				// 	font: 'bold 12px "Trebuchet MS", Verdana, sans-serif'
				// }
			},

			legend: {
				// itemStyle: {
				// 	font: '9pt Trebuchet MS, Verdana, sans-serif',
				// 	color: 'black'
				// },
				// itemHoverStyle: {
				// 	color: 'gray'
				// }
			}
		};

		Highcharts.setOptions(Highcharts.theme);
	}

}