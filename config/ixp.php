<?php

return [

    /*
    | IXP Configuration
    */

    /* ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
    ;;
    ;; Enables multi-IXP mode.
    ;;
    ;; See: https://github.com/inex/IXP-Manager/wiki/Multi-IXP-Functionality
    ;; */
    'multiixp' => [
        'enabled' => env( 'IXP_MULTIIXP_ENABLED', false ),
    ],

    /* ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
    ;;
    ;; Enables resller mode
    ;;
    ;; See: https://github.com/inex/IXP-Manager/wiki/Reseller-Functionality
    ;; */
    'reseller' => [
        'enabled' => env( 'IXP_RESELLER_ENABLED', false ),

        // If reseller mode enabled and this is set to true then super admin or customer itself
        // can not add/change resold customers details.
        'reseller' => env( 'IXP_RESELLER_RESOLD_BILLING', false ),
    ],


    /* ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
    ;; Specifies whether to display and enable control of AS112 functionality for customers
    ;;
    ;; See https://github.com/inex/IXP-Manager/wiki/AS112
    ;; */
    'as112' => [
        'ui_active' => env( 'IXP_AS112_UI_ACTIVE', false ),
    ],

];
