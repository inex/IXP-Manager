<?php

use PragmaRX\Google2FALaravel\Support\Constants;

return [

    /*
     * Auth container binding
     */

    'enabled' => true,

    /*
     * Lifetime in minutes.
     * In case you need your users to be asked for a new one time passwords from time to time.
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
    'window' => 1,

    /*
     * Forbid user to reuse One Time Passwords.
     */
    'forbid_old_passwords' => false,

    /*
     * User's table column for google2fa secret
     */
    'otp_secret_column' => 'google2fa_secret',

    /*
     * One Time Password View
     */
    'view' => 'google2fa.login-form',

    /*
     * One Time Password error message
     */
    'error_messages' => [
        'wrong_otp'       => "The 'One Time Password' typed was wrong.",
        'cannot_be_empty' => 'One Time Password cannot be empty.',
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
    'qrcode_image_backend' => Constants::QRCODE_IMAGE_BACKEND_IMAGEMAGICK,


    /*
     * Require 2FA authentification for IXP superuser
     */
    'superuser_required' => env( '2FA_SUPERUSER_REQUIRED', false ),

    /*
     * Optional token expiration time in minutes, (30 days is the default)
     */
    'remember_me_expire' => env( '2FA_REMEMBER_ME_EXPIRE', 43200 ),

];
