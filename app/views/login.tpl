{$header}
<h2 class="text-center">{$langMain['please_sign_in']}</h2>

<div class="row">
    <div class="col-lg-6 offset-lg-3 col-md-6 offset-md-3 border rounded p-5 my-5">
        <form id="login-form" data-target="player/login" class="ajax-form">
            <div class="form-group">
                <input class="form-control" name="name" value="" required placeholder="{$langMain['name']}" type="text">
            </div>
            <div class="form-group">
                <input class="form-control" name="password" value="" required placeholder="{$langMain['password']}" type="password">
            </div>
            <div class="form-group">
                <input class="btn btn-primary btn-block" type="submit" value="{$langMain['sign_in']}">
            </div>
            <div class="form-group">
                <div class="alert alert-success form-message form-message-success d-none" role="alert"></div>
                <div class="alert alert-danger form-message form-message-error d-none" role="alert"></div>
            </div>
        </form>
    </div>
</div>
{$footer}
