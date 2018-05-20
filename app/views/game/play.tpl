{$header}
<script src="{$baseUrl}:3000/socket.io/socket.io.js"></script>
<h2 class="text-center">{$lang['play_game']} ({$lang['game_code']} : {$game['code']})</h2>

<div class="row">
    <div class="col-lg-8 offset-lg-2 col-md-8 offset-md-2 text-center">
        <div class="alert alert-primary" role="alert">
            {$lang['waiting_for_players']} <strong><span class="nb-players">{$game['nbPlayers']}</span> / {$game['maxPlayers']}</strong>
        </div>
    </div>
</div>

<input type="hidden" id="play-socket" value="1"/>
{$footer}