
<?php if( isset( $t->data[ 'rows' ][ 0 ] ) ) : ?>
    <?php $example_password = config( 'ixp_fe.app_passwords.show_passwords' ) ? $t->data[ 'rows' ][ 0 ][ 'password' ] : Str::limit( $t->data[ 'rows' ][ 0 ][ 'password' ] , 6 ) ?>
<?php else: ?>
    <?php $example_password = '$your_app_password' ?>
<?php endif; ?>

<div class="card mt-4">
    <div class="card-header">
        <h3>Application-Specific Passwords</h3>
    </div>
    <div class="card-body">
        <ul>
            <li>
                App passwords can be used for supported applications as advised by your IT administrators.
            </li>
            <li>
                An app password is a unique password generated for use with a specific application on a specific device. <b>It should never be used more than once.</b>
            </li>
            <li>
                Please use a descriptive name for your app password which clearly identifies the application and device.  This will help you identify the application and device that the password is for should it need to be deleted.
            </li>
            
        </ul>
    </div>
</div>
