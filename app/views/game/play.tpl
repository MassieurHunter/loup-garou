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
	
	<div class="row roles-block">
		<div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 mt-1 text-center"></div>
	</div>

	<div class="row role-infos">
		<div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 mt-1 text-center"></div>
	</div>
	
	<div class="row action-results">
		<div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 mt-1 text-center"></div>
	</div>
	
</div>


<input type="hidden" id="play-socket" value="1"/>
{$footer}