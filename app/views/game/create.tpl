{$header}
<h2 class="text-center">{$langMain['create_game']}</h2>

<div class="row">
    <div class="col-lg-4 offset-lg-4 col-md-6 offset-md-3 border rounded p-5 my-5">
        <form action="/game/create" method="post">
            <div class="form-group">
                <label for="exampleInputEmail1">
                    {$langMain['max_players']} : <span class="nb-max-players font-weight-bold"></span>
                </label>
                <input type="range" name="max-players" class="form-control-range max-players-range" min="3" max="10" value="10">
            </div>
            <div class="form-group">
                <input class="btn btn-primary btn-block" type="submit" value="{$langMain['create']}">
            </div>
        </form>
    </div>
</div>

{if $newGameCode}
    <p>
        {$langMain['game_code_is']} <strong>{$newGameCode}</strong>
        <br/>

        <a href="/game/join/{$newGameCode}">{$langMain['join_the_game']}</a>

    </p>
{/if}
{$footer}