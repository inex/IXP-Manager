<?php

return [
    // 8 is allowed when 2FA is enabled, which is the IXP Manager default. If 2FA is to be disabled, then this should be
    // at least 15.
    'minimum_length' => min( max(8,  (int) env( 'PASSWORD_POLICY_MINIMUM_LENGTH', 8  ) ), 72 ),

    // NIST recommends the maximum length be at least 64 characters. IXP Manager uses bcrypt which places a hard limit on
    // the supported length of passwords.
    'maximum_length' => max( min(72, (int) env( 'PASSWORD_POLICY_MAXIMUM_LENGTH', 72 ) ), 72 ),

    // Require that passwords are not previously compromised. Recommended by NIST as of 2025.
    'not_compromised'    =>  (bool) env('PASSWORD_POLICY_REQUIRE_NOT_COMPROMISED', false),

    // Require at least one letter? (Note: not recommended by NIST as of 2025 as it leads to more predictable, weaker passwords.
    'require_letters'    =>  (bool) env('PASSWORD_POLICY_REQUIRE_LETTERS',    false),

    // Require at least one number? (Note: not recommended by NIST as of 2025 as it leads to more predictable, weaker passwords.
    'require_numbers'    =>  (bool) env('PASSWORD_POLICY_REQUIRE_NUMBERS',    false),

    // Require at least one of each letter case? (Note: not recommended by NIST as of 2025 as it leads to more predictable, weaker passwords.
    'require_mixed_case' =>  (bool) env('PASSWORD_POLICY_REQUIRE_MIXED_CASE', false),

    // Require at least one symbol? (Note: not recommended by NIST as of 2025 as it leads to more predictable, weaker passwords.
    'require_symbols'    =>  (bool) env('PASSWORD_POLICY_REQUIRE_SYMBOLS',    false),

];