import Ajax from '../tools/Ajax';
import ABuilder from "../tools/ABuilder";
import $ from "jquery";
import Highcharts from 'highcharts';
import gridLight from 'highcharts/themes/grid-light';
import 'datatables.net';

export default class Stats {

	constructor(lang) {

		this.lang = lang;

		this.teams = [
			'villageois',
			'tanneur',
			'loup',
		];

		this.highchartsTheme();

		this.overallStats();
		this.renderPlayer();

		setTimeout(() => {
			$('table').each((index, element) => {

				let table = $(element);

				table.DataTable({
					searching : false,
					paging : false,
					info : false,
				});

			});

		}, 1500);
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
		this.playerTeamRoleHistory(startingTeamGames, 'team', 'starting', this.lang.getLine('stat_starting_team'));
		this.playerTeamRoleHistory(endingTeamGames, 'team', 'ending', this.lang.getLine('stat_ending_team'));
		this.playerTeamRoleHistory(startingRolesGames, 'role', 'starting', this.lang.getLine('stat_starting_role'));
		this.playerTeamRoleHistory(endingRolesGames, 'role', 'ending', this.lang.getLine('stat_ending_role'));

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

	playerTeamRoleHistory(games, teamOrRole, startingEnding, sectionTitle) {
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

			let classes = 'table-responsive ' + 'table-' + startingEnding + '-' + teamOrRole + '-' + teamRole.replace(/[^a-z]+/gi, '-').toLowerCase();

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

			let arrGames = games[teamRole];

			for (let gameLine of arrGames) {

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
				'div',
				{'class': classes},
				new ABuilder(
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
				)
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
		this.playerTeamCharts(startingTeamStats, 'starting');
		this.playerTeamCharts(endingTeamStats, 'ending');
		this.playerRoleCharts(startingRolesStats, 'starting');
		this.playerRoleCharts(endingRolesStats, 'ending');

		$('.player-stats-charts-container').removeClass('d-none');

		setTimeout(() => {

			$('.highcharts-point').mouseover();

			$('.highcharts-label').each((index, element) => {

				let tooltip = $(element);
				let path = tooltip.find('path');

				tooltip
					.attr({transform: 'translate(5000,5000)'})
					.prepend(path);

			});

		}, 1000);

	}

	playerGlobalCharts(stats) {

		let chartRow = new ABuilder(
			'div',
			{
				'class': 'row player-chart-global-row',
			},
			''
		);

		let chartContainer = new ABuilder(
			'div',
			{
				'id': 'global-chart-win-container',
				'class': 'player-chart-container col-sm-12 col-md-12 col-lg-4',
			},
			''
		);

		let chartTitle = new ABuilder(
			'h3',
			{
				'class': 'text-center',
			},
			this.lang.getLine('stat_chart_win_ratio')
		);

		$('.player-global-stats-container')
			.append(
				chartRow.append(
					chartContainer
				)
			);

		let data = [
			{
				name: this.lang.getLine('stat_wins'),
				color: $('.hidden-success').css('background-color'),
				y: stats.wins,
			}, {
				name: this.lang.getLine('stat_losses'),
				color: $('.hidden-danger').css('background-color'),
				y: stats.losses,
			}
		];

		this.playerChart(data, 'global-chart-win-container');

		chartContainer.prepend(chartTitle)

	}

	playerTeamCharts(stats, endingStarting) {
		/*
		 * teams games ratio
		 */

		let globalRow = $('.player-chart-global-row');

		let gameStatsId = 'global-chart-' + endingStarting + '-team-container';

		let gamesStatsChartTitle = new ABuilder(
			'h3',
			{
				'class': 'text-center',
			},
			this.lang.getLine('stat_chart_' + endingStarting + '_team_ratio')
		);

		let gameStatsChartContainer = new ABuilder(
			'div',
			{
				'id': gameStatsId,
				'class': 'player-chart-container col-sm-6 col-md-6 col-lg-4',
			},
			''
		);

		globalRow.append(gameStatsChartContainer);

		let gameStatsData = [
			{
				name: this.lang.getLine('stat_chart_team_villageois'),
				color: $('.hidden-info').css('background-color'),
				y: stats.games_villageois,
			}, {
				name: this.lang.getLine('stat_chart_team_loup'),
				color: $('.hidden-dark').css('background-color'),
				y: stats.games_loup,
			}, {
				name: this.lang.getLine('stat_chart_team_tanneur'),
				color: $('.hidden-warning').css('background-color'),
				y: stats.games_tanneur,
			}
		];

		this.playerChart(gameStatsData, gameStatsId);

		gameStatsChartContainer.prepend(gamesStatsChartTitle);


		/*
		 * win ratio by team (starting and ending)
		 */

		for (let team of this.teams) {

			if (stats['games_' + team] > 0) {

				let ratioRow = new ABuilder(
					'div',
					{
						'class': 'row player-chart-team-' + team + '-win-ratio-row',
					},
					''
				);

				let tableClass = '.table-' + endingStarting + '-team-' + team;

				let teamStatsId = 'ratio-chart-' + endingStarting + '-team-' + team + '-container';

				let ratioChartContainer = new ABuilder(
					'div',
					{
						'id': teamStatsId,
						'class': 'player-chart-container col-sm-12 col-md-12 col-lg-12',
					},
					''
				);

				let chartTitle = new ABuilder(
					'h3',
					{
						'class': 'text-center',
					},
					this.lang.getLine('stat_chart_win_ratio')
				);

				ratioRow
					.append(ratioChartContainer)
					.insertAfter(tableClass);


				let data = [
					{
						name: this.lang.getLine('stat_wins'),
						color: $('.hidden-success').css('background-color'),
						y: stats['wins_' + team],
					}, {
						name: this.lang.getLine('stat_losses'),
						color: $('.hidden-danger').css('background-color'),
						y: stats['losses_' + team],
					}
				];

				this.playerChart(data, teamStatsId);

				ratioChartContainer.prepend(chartTitle);

			}
		}

	}

	playerRoleCharts(stats, endingStarting) {
		let globalRow = $('.player-chart-global-row');

		let id = endingStarting === 'starting' ? 'global-chart-starting-role-container' : 'global-chart-ending-role-container';

		let chartTitle = new ABuilder(
			'h3',
			{
				'class': 'text-center',
			},
			endingStarting === 'starting' ? this.lang.getLine('stat_chart_starting_role_ratio') : this.lang.getLine('stat_chart_ending_role_ratio')
		);

		let chartContainer = new ABuilder(
			'div',
			{
				'id': id,
				'class': 'player-chart-container col-sm-6 col-md-6 col-lg-6',
			},
			''
		);

		globalRow.append(chartContainer);

		let data = [];

		for (let role in stats) {

			let roleStat = stats[role];
			data.push({
				name: role.trim().charAt(0).toUpperCase() + role.trim().slice(1),
				y: roleStat.games
			});


			let ratioRow = new ABuilder(
				'div',
				{
					'class': 'row player-chart-role-' + role.replace(/[^a-z]+/gi, '-').toLowerCase() + '-win-ratio-row',
				},
				''
			);

			let tableClass = '.table-' + endingStarting + '-role-' + role.replace(/[^a-z]+/gi, '-').toLowerCase();

			let roleStatsId = 'ratio-chart-' + endingStarting + '-role-' + role.replace(/[^a-z]+/gi, '-').toLowerCase() + '-container';

			let ratioChartContainer = new ABuilder(
				'div',
				{
					'id': roleStatsId,
					'class': 'player-chart-container col-sm-12 col-md-12 col-lg-12',
				},
				''
			);

			let chartTitle = new ABuilder(
				'h3',
				{
					'class': 'text-center',
				},
				this.lang.getLine('stat_chart_win_ratio')
			);

			ratioRow
				.append(ratioChartContainer)
				.insertAfter(tableClass);


			let dataRole = [
				{
					name: this.lang.getLine('stat_wins'),
					color: $('.hidden-success').css('background-color'),
					y: roleStat.wins,
				}, {
					name: this.lang.getLine('stat_losses'),
					color: $('.hidden-danger').css('background-color'),
					y: roleStat.losses,
				}
			];

			this.playerChart(dataRole, roleStatsId);

			ratioChartContainer.prepend(chartTitle)

		}

		this.playerChart(data, id);

		chartContainer.prepend(chartTitle)
	}

	playerChart(data, container) {
		
		Highcharts.chart(container, {
			chart: {
				plotBackgroundColor: 'transparent',
				plotBorderWidth: 1,
				plotShadow: true,
				type: 'pie'
			},
			title: {
				text: ''
			},
			tooltip: {
				pointFormat: '{point.y} ({point.percentage:.1f}%)'
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: false,
					}
				}
			},
			series: [{
				colorByPoint: true,
				data: data
			}]
		});

	}

	highchartsTheme() {

		let applyTheme = gridLight;

		if (typeof applyTheme !== 'function' && typeof gridLight.default === 'function') {
			applyTheme = gridLight.default;
		}

		if (typeof applyTheme === 'function') {
			applyTheme(Highcharts);
		}

	}

}
