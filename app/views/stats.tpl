{$header}
<h2 class="text-center">{$lang['statistics']}</h2>

<div class="table-responsive">
	<table class="table table-hover table-striped table-bordered overall-stats-table">
		<thead>
		<tr>
			<th rowspan="2" class="text-center align-middle">{$lang['stat_player']}</th>
			<th colspan="4" class="text-center">{$lang['stat_games']}</th>
			<th colspan="3" class="text-center">{$lang['stat_percent_games']}</th>
			{*<th colspan="4" class="text-center">{$lang['stat_losses']}</th>*}
			<th colspan="4" class="text-center">{$lang['stat_wins']}</th>
			<th colspan="4" class="text-center">{$lang['stat_percent_win']}</th>
		</tr>
		<tr>
			<th>{$lang['stat_games_loup']}</th>
			<th>{$lang['stat_games_tanneur']}</th>
			<th>{$lang['stat_games_villageois']}</th>
			<th class="font-weight-bold">{$lang['stat_games_all']}</th>
			<th>{$lang['stat_percent_games_loup']}</th>
			<th>{$lang['stat_percent_games_tanneur']}</th>
			<th>{$lang['stat_percent_games_villageois']}</th>
			<th>{$lang['stat_wins_loup']}</th>
			<th>{$lang['stat_wins_tanneur']}</th>
			<th>{$lang['stat_wins_villageois']}</th>
			<th class="font-weight-bold">{$lang['stat_wins_all']}</th>
			{*<th>{$lang['stat_losses_loup']}</th>*}
			{*<th>{$lang['stat_losses_tanneur']}</th>*}
			{*<th>{$lang['stat_losses_villageois']}</th>*}
			{*<th>{$lang['stat_losses_all']}</th>*}
			<th>{$lang['stat_percent_win_loup']}</th>
			<th>{$lang['stat_percent_win_tanneur']}</th>
			<th>{$lang['stat_percent_win_villageois']}</th>
			<th class="font-weight-bold">{$lang['stat_percent_win_all']}</th>
		</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>

{$footer}