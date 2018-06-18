{$header}
<h2 class="text-center">{$lang['statistics']}</h2>

<div class="table-responsive">
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
		<tr>
			<td colspan="7" class="text-center p5">
				<img src="/img/loader.svg" alt="...">
			</td>
		</tr>
		</tbody>
	</table>
</div>

<div class="table-responsive player-games-starting-team">
	<div class="text-center p5">
		<img src="/img/loader.svg" alt="...">
	</div>
</div>

<div class="table-responsive player-games-ending-team">
	<div class="text-center p5">
		<img src="/img/loader.svg" alt="...">
	</div>
</div>

<div class="table-responsive player-games-starting-role">
	<div class="text-center p5">
		<img src="/img/loader.svg" alt="...">
	</div>
</div>

<div class="table-responsive player-games-ending-role">
	<div class="text-center p5">
		<img src="/img/loader.svg" alt="...">
	</div>
</div>

<input type="hidden" value="{$playerUid}" class="player-uid-stat">

{$footer}