{$header}
<script src="{$baseUrl}:3000/socket.io/socket.io.js"></script>
<h2 class="text-center">{$lang['play_game']}</h2>

<div class="play-game">
	<div class="row">
		<div class="alerts-container col-lg-8 offset-lg-2 col-md-10 offset-md-1 mt-1 text-center">
			<!-- Button trigger modal -->
			<button type="button" class="btn btn-block btn-primary mt-1 md-1 py-3" data-toggle="modal"
					data-target="#note-modal">
				{$lang['take_note']}
			</button>
			<div class="alert alert-secondary waiting-for-start mt-3" role="alert">
				{$lang['game_code']} : {$game['code']}<br/>
				{$lang['game_link']} : {$baseUrl}/game/join/{$game['code']}
			</div>
			<div class="alert alert-primary waiting-for-start" role="alert">
				{$lang['waiting_for_players']} <strong><span class="nb-players">{$game['nbPlayers']}</span>
					/ {$game['maxPlayers']}</strong>
			</div>
			<div class="roles-block text-left mt-3 md-1"></div>
			<div class="players-list text-left mt-1 md-1"></div>
			<div class="game-progress mt-1 md-1"></div>
			<div class="role-infos mt-1 md-1"></div>
			<div class="turn-finished mt-1 md-1"></div>
			<div class="action-results mt-1 md-1"></div>
			<div class="action-form-container mt-1 md-1"></div>
			<div class="vote-form-container mt-1 md-1"></div>
			<div class="vote-message mt-1 md-1"></div>
			<div class="vote-infos mt-1 md-1"></div>
			<div class="game-results text-left mt-1 md-1"></div>
			<div class="game-summary text-left mt-1 md-1"></div>
		</div>
	</div>

</div>

<!-- Modal for note taking -->
<div class="modal fade" id="note-modal" tabindex="-1" role="dialog" aria-labelledby="note-modal-label"
	 aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="note-modal-label">{$lang['take_note']}</h5>
				<button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<textarea class="note-textarea"></textarea>
			</div>
		</div>
	</div>
</div>


<input type="hidden" id="play-socket" value="1"/>
{$footer}