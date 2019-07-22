<div class="alert alert-warning" role="alert">
    <div class="d-flex align-items-center">
        <div class="text-center">
            <i class="fa fa-exclamation-circle fa-2x"></i>
        </div>
        <div class="col-sm-12">
            <strong>Treat your API key as a password and do not copy the below URLs into public websites and other public forums.</strong>
        </div>
    </div>
</div>

<?php if( !config( 'ixp_fe.api_keys.show_keys' ) ): ?>
    <div class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm d-block">
        <div>To protect your API keys from unintentional disclosure, you need to enter your password to display them:</div>

        <div>
            <ul class="navbar-nav">

                <form class="navbar-form navbar-left form-inline d-block d-lg-flex" action="<?= route( "api-key@list-show-keys" ) ?>" method="POST">
                    <li class="nav-item">
                        <div class="nav-link d-flex ">
                            <label for="select_protocol" class="col-sm-4 col-lg-4">Password:</label>
                            <input name="pass" type="password" class="form-control" required>
                            <input type="submit" class="btn-primary btn ml-4" value="Submit">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        </div>
                    </li>
                </form>

            </ul>
        </div>

    </div>

<?php endif; ?>
