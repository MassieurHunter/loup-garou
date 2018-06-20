import Ajax from '../tools/Ajax';
import ABuilder from "../tools/ABuilder";
import $ from "jquery";
import Highcharts from 'highcharts';
import gridLight from 'highcharts/themes/grid-light';

export default class Stats {

	constructor(lang) {

		this.lang = lang;

		this.highchartsTheme();

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
		this.playerTeamCharts(startingTeamStats, 'starting');
		this.playerTeamCharts(endingTeamStats, 'ending');
		this.playerRoleCharts(startingRolesStats, 'starting');
		this.playerRoleCharts(endingRolesStats, 'ending');

		$('.player-stats-charts-container').removeClass('d-none');

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

		let statTitle = new ABuilder(
			'h2',
			{
				'class': 'text-center',
			},
			this.lang.getLine('stat_chart_global')
		);
		
		let chartTitle = new ABuilder(
			'h3',
			{
				'class': 'text-center',
			},
			this.lang.getLine('stat_chart_win_ratio')
		);

		$('.player-stats-charts-container')
			.append(statTitle)
			.append(
			chartRow.append(
				chartContainer
			)
		);

		Highcharts.chart('global-chart-win-container', {
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
				name: this.lang.getLine('stat_chart_ratio'),
				colorByPoint: true,
				data: [{
					name: this.lang.getLine('stat_wins'),
					color: $('.hidden-success').css('background-color'),
					y: stats.wins,
				}, {
					name: this.lang.getLine('stat_losses'),
					color: $('.hidden-danger').css('background-color'),
					y: stats.losses,
				}
				]
			}]
		});

		chartContainer.prepend(chartTitle)

	}

	playerTeamCharts(stats, endingStarting) {
		let globalRow = $('.player-chart-global-row');
		
		let id = endingStarting === 'starting' ? 'global-chart-starting-team-container' : 'global-chart-ending-team-container';

		let chartTitle = new ABuilder(
			'h3',
			{
				'class': 'text-center',
			},
			endingStarting === 'starting' ? this.lang.getLine('stat_chart_starting_team_ratio') :this.lang.getLine('stat_chart_ending_team_ratio') 
		);

		let chartContainer = new ABuilder(
			'div',
			{
				'id': id,
				'class': 'player-chart-container col-sm-6 col-md-6 col-lg-4',
			},
			''
		);

		globalRow.append(chartContainer);
		

		Highcharts.chart(id, {
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
				name: this.lang.getLine('stat_chart_ratio'),
				colorByPoint: true,
				data: [{
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
				]
			}]
		});

		chartContainer.prepend(chartTitle)
	}

	playerRoleCharts(stats, endingStarting) {
		let globalRow = $('.player-chart-global-row');

		let id = endingStarting === 'starting' ? 'global-chart-starting-role-container' : 'global-chart-ending-role-container';

		let chartTitle = new ABuilder(
			'h3',
			{
				'class': 'text-center',
			},
			endingStarting === 'starting' ? this.lang.getLine('stat_chart_starting_role_ratio') :this.lang.getLine('stat_chart_ending_role_ratio')
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
		
		for(let role in stats){
			
			let roleStat = stats[role];
			data.push({
				name : role.trim().charAt(0).toUpperCase() + role.trim().slice(1),
				y : roleStat.games
			});
			
		}


		Highcharts.chart(id, {
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
				name: this.lang.getLine('stat_chart_ratio'),
				colorByPoint: true,
				data: data
			}]
		});

		chartContainer.prepend(chartTitle)
	}

	playerCharts(stats, sectionTitle, container) {

	}

	highchartsTheme() {

		gridLight(Highcharts);

	}

}