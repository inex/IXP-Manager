<div class="card mt-4">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li role="user-remember-token" class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#user-remember-token">
                    Remember Token
                </a>
            </li>
            <li role="otc-remember-token" class="nav-item" >
                <a class="nav-link" href="<?= route( "otp-remember-token@list" ) ?>">
                    OTC Remember Token
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">

        <div class="tab-content">
            <div id="user-remember-token" class="tab-pane fade active show">
