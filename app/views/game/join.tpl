{$header}
<h2 class="text-center">{$lang['join_game']}</h2>


<div class="row">
    <div class="col-lg-6 offset-lg-3 col-md-6 offset-md-3 border rounded p-5 my-5">
        <form data-target="/game/join" class="ajax-form">
            <div class="form-group">
                <input class="form-control" name="game-code" value="" required placeholder="{$lang['game_code']}"
                       type="text">
            </div>
            <div class="form-group">
				<button class="btn btn-primary btn-block" type="submit" >{$lang['sign_in']}</button>
            </div>
            <div class="form-group">
                <div class="alert alert-success form-message form-message-success d-none text-center" role="alert"></div>
                <div class="alert alert-danger form-message form-message-error {$hideError} text-center" role="alert">{$error}</div>
            </div>
</form>
    </div>
</div>


{$footer}