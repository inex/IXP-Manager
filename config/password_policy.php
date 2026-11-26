<?php

$default = (bool) env('2FA_ENABLED', true) && env('2FA_ENFORCE_FOR_USERS', \IXP\Models\User::AUTH_CUSTUSER) ? 8 : 15;

return [
    // Minimum length is 8 characters.
    'minimum_length'     => min( max( 8,  (int) env( 'PASSWORD_POLICY_MINIMUM_LENGTH', $default ) ), 72 ),

    // Maximum length is 72 characters. No good reason to restrict it (or is there? let us know!)
    'maximum_length'     => 72,

    // Require that passwords are not previously compromised. Recommended by NIST as of 2025.
    'not_compromised'    =>  (bool) env('PASSWORD_POLICY_REQUIRE_NOT_COMPROMISED', true),

    // Require at least one number? (Note: not recommended by NIST as of 2025 as it leads to more predictable, weaker passwords.
    'require_numbers'    =>  (bool) env('PASSWORD_POLICY_REQUIRE_NUMBERS',    false),

    // Require at least one of each letter case? (Note: not recommended by NIST as of 2025 as it leads to more predictable, weaker passwords.
    'require_mixed_case' =>  (bool) env('PASSWORD_POLICY_REQUIRE_MIXED_CASE', false),

    // Require at least one symbol? (Note: not recommended by NIST as of 2025 as it leads to more predictable, weaker passwords.
    'require_symbols'    =>  (bool) env('PASSWORD_POLICY_REQUIRE_SYMBOLS',    false),

];