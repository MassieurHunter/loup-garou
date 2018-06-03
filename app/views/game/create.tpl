{$header}
<h2 class="text-center">{$lang['create_game']}</h2>

<div class="row">
    <div class="col-lg-6 offset-lg-3 col-md-6 offset-md-3 border rounded p-5 my-5">
        <form data-target="/game/create" class="ajax-form">
            <div class="form-group">
                <label for="exampleInputEmail1">
                    {$lang['max_players']} : <span class="nb-max-players font-weight-bold"></span>
                </label>
                <input type="range" name="max-players" class="form-control-range max-players-range" min="5" max="10" value="10">
            </div>
            <div class="form-group">
                <input class="btn btn-primary btn-block" type="submit" value="{$lang['create']}">
            </div>
            <div class="form-group">
                <div class="alert alert-success form-message form-message-success d-none text-center" role="alert"></div>
                <div class="alert alert-danger form-message form-message-error d-none text-center" role="alert"></div>
            </div>
        </form>
    </div>
</div>

{if $newGameCode}
    <p>
        {$lang['game_code_is']} <strong>{$newGameCode}</strong>
        <br/>

        <a href="/game/join/{$newGameCode}">{$lang['join_the_game']}</a>

    </p>
{/if}
{$footer}