<?php

use PragmaRX\Google2FALaravel\Support\Constants;

return [

    /*
     * Auth container binding
     */

    'enabled' => env( '2FA_ENABLED', true ),

    /*
     * Lifetime in minutes.
     * In case you need your users to be asked for a new one time passwords from time to time.
     *
     * For IXP Manager, we leave this as eternal and rely on the maximum session lifetime instead.
     */
    'lifetime' => 0, // 0 = eternal

    /*
     * Renew lifetime at every new request.
     */
    'keep_alive' => true,

    /*
     * Auth container binding
     */
    'auth' => 'auth',

    /*
     * 2FA verified session var
     */
    'session_var' => 'google2fa',

    /*
     * One Time Password request input name
     */
    'otp_input' => 'one_time_password',

    /*
     * One Time Password Window
     */
    'window' => env( '2FA_WINDOW', 4 ),

    /*
     * Forbid user to reuse One Time Passwords.
     */
    'forbid_old_passwords' => false,

    /*
     * User's table column for google2fa secret
     */
    'otp_secret_column' => 'secret',

    /*
     * One Time Password View
     */
    'view' => 'user.2fa.login-form',

    /*
     * One Time Password error message
     */
    'error_messages' => [
        'wrong_otp'       => "The one time password entered was wrong.",
        'cannot_be_empty' => 'One time password cannot be empty.',
        'unknown'         => 'An unknown error has occurred. Please try again.',
    ],

    /*
     * Throw exceptions or just fire events?
     */
    'throw_exceptions' => false,

    /*
     * Which image backend to use for generating QR codes?
     *
     * Supports imagemagick, svg and eps
     */
    'qrcode_image_backend' => Constants::QRCODE_IMAGE_BACKEND_SVG,


    /*
     * Require 2FA authentication for IXP users >= this level.
     *
     * By default we set it to '\IXP\Models\User::AUTH_SUPERUSER + 1' which means no users will be
     * forced to enabled 2fa.
     *
     * To force:
     *
     * - all superusers, set this to \IXP\Models\User::AUTH_SUPERUSER (3);
     * - all custadmins and superusers, set this to \IXP\Models\User::AUTH_CUSTADMIN (2);
     * - all users set this to \IXP\Models\User::AUTH_CUSTUSER (1 or less);
     */
    'ixpm_2fa_enforce_for_users' => env( '2FA_ENFORCE_FOR_USERS', \IXP\Models\User::AUTH_SUPERUSER + 1 ),

];
