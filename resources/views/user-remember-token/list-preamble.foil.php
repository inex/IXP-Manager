<?php if( $t->data['session_token'] === null ): ?>
    <div class="alert alert-info tw:mb-8" role="alert">
        <?= __( ':activeSessions are only login sessions that had :rememberMe checked. Your current session was :not initiated with :rememberMe checked.', [
            'activeSessions' => '<b>'  . __( 'Active sessions' ) . '</b>',
            'rememberMe'     => '<em>' . __( 'Remember me' )     . '</em>',
            'not'            => '<b>'  . __( 'not' )             . '</b>',
        ] ) ?>
    </div>
<?php endif; ?>

<div class="card mt-4">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li role="user-remember-token" class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#user-remember-token">
                    <?= __( 'Active Sessions' ) ?>
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content">
            <div id="user-remember-token" class="tab-pane fade active show">