<?php if( $t->data['session_token'] === null ): ?>
    <div class="alert alert-info tw-mb-8" role="alert">
        <b>Active sessions</b> are only login sessions that had <em>Remember me</em> checked. Your current session was
        <b>not</b> initiated with <em>Remember me</em> checked.
    </div>
<?php endif; ?>

<div class="card mt-4">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li role="user-remember-token" class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#user-remember-token">
                    Active Sessions
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content">
            <div id="user-remember-token" class="tab-pane fade active show">