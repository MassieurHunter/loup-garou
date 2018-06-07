{$header}
<script src="{$baseUrl}:3000/socket.io/socket.io.js"></script>
<h2 class="text-center">{$lang['play_game']}</h2>

<div class="row waiting-for-start">
	<div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 mt-3 text-center">
		<div class="alert alert-secondary" role="alert">{$lang['game_code']} : {$game['code']}</div>
	</div>
</div>

<div class="play-game">
	<div class="row waiting-for-start">
		<div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 mt-1 text-center">
			<div class="alert alert-primary" role="alert">
				{$lang['waiting_for_players']} <strong><span class="nb-players">{$game['nbPlayers']}</span>
					/ {$game['maxPlayers']}</strong>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="alerts-container col-lg-8 offset-lg-2 col-md-10 offset-md-1 mt-1 text-center">
			<div class="roles-block mt-1 md-1"></div>
			<div class="game-progress mt-1 md-1"></div>
			<div class="role-infos mt-1 md-1"></div>
			<div class="action-results mt-1 md-1"></div>
			<div class="action-form-container mt-1 md-1"></div>
			<div class="turn-finished mt-1 md-1"></div>
			<div class="vote-message mt-1 md-1"></div>
			<div class="votes-infos mt-1 md-1"></div>
		</div>
	</div>

</div>


<input type="hidden" id="play-socket" value="1"/>
{$footer}