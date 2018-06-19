{$header}
<h2 class="text-center">{$lang['statistics']}</h2>

<div class="loader-container m-5 p-5 text-center">
	<img src="/img/loader.svg" alt="...">
</div>


<div class="player-stats-table-container d-none table-responsive">
	<table class="table table-hover table-striped table-bordered player-stats-table player-games-all">
		<thead>
		<tr>
			<th class="text-center">{$lang['stat_nb_players']}</th>
			<th class="text-center">{$lang['stat_starting_team']}</th>
			<th class="text-center">{$lang['stat_starting_role']}</th>
			<th class="text-center">{$lang['stat_ending_team']}</th>
			<th class="text-center">{$lang['stat_ending_role']}</th>
			<th class="text-center">{$lang['stat_players_list']}</th>
			<th class="text-center">{$lang['stat_result']}</th>
		</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<div class="player-stats-table-container d-none table-responsive player-games-team-roles-container"></div>

<div class="player-stats-charts-container d-none"></div>

<input type="hidden" value="{$playerUid}" class="player-uid-stat">

{$footer}