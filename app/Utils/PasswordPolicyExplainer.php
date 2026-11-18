<?php
/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace IXP\Utils;

use Illuminate\Validation\Rules\Password;

/**
 * This class provides a list of human readable requirements based on the provided password policy rules.
 *
 * Note: Currently, only supports the basic set of rules supported by the Password rule - no 'customRules'.
 */
class PasswordPolicyExplainer
{
    private array $messages = [
        'min'                  => "Minimum length is :value characters",
        'max'                  => "Maximum length is :value characters",
        'mixedCase'            => "Password must contain at least one uppercase and one lowercase letter",
        'letters'              => "Password must contain at least one letter",
        'numbers'              => "Password must contain at least one number",
        'symbols'              => "Password must contain at least one symbol",
        'uncompromised'        => "Password must not be found in leaked password lists",
        'compromisedThreshold' => "Password must not be found in leaked password lists more than :value times",
    ];

    public function explain( Password $policy ): array
    {
        $requirements = [];
        $rules = $policy->appliedRules();

        // Don't double up on mixedCase + letters
        if (array_key_exists("mixedCase", $rules) && $rules['mixedCase']) {
            unset($rules['letters']);
        }

        foreach ( $rules as $rule => $value) {
            if (!array_key_exists($rule, $this->messages)) {
                continue;
            }
            if (!$value) {
                continue;
            }
            $message = $this->messages[$rule];
            if (str_contains($message, ":value")) {
                $message = str_replace(":value", (string) $value, $message);
            }
            $requirements[] = $message;
        }

        return $requirements;
    }

    public function explainDefault(): array
    {
        return $this->explain( Password::default() );
    }
}