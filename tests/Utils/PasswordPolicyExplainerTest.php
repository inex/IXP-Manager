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

namespace Tests\Utils;

use Illuminate\Validation\Rules\Password;
use IXP\Utils\PasswordPolicyExplainer;
use Tests\TestCase;
use Tests\Trait\SetsPasswordPolicy;

class PasswordPolicyExplainerTest extends TestCase
{
    use SetsPasswordPolicy;

    public function testPolicyExplainer(): void
    {
        $explainer = new PasswordPolicyExplainer();
        $this
            ->setPasswordPolicy( 8, 16 )
            ->assertEquals([
                "Minimum length is 8 characters",
                "Maximum length is 16 characters",
            ], $explainer->explain(Password::default()));

        // no doubling up of letters + mixedCase rule
        $this
            ->setPasswordPolicy( minLength: 8, maxLength: 16, requireMixedCase: true, requireNumbers: true, requireSymbols: true, notCompromised: true )
            ->assertEquals([
                "Minimum length is 8 characters",
                "Maximum length is 16 characters",
                "Password must contain at least one uppercase and one lowercase letter",
                "Password must contain at least one number",
                "Password must contain at least one symbol",
                "Password must not be found in leaked password lists",
            ], $explainer->explain(Password::default()));

        // letters rule there when mixedCase isn't active
        $this
            ->setPasswordPolicy( minLength: 8, maxLength: 16, requireMixedCase: false, requireNumbers: true, requireSymbols: true, notCompromised: true )
            ->assertEquals([
                "Minimum length is 8 characters",
                "Maximum length is 16 characters",
                "Password must contain at least one number",
                "Password must contain at least one symbol",
                "Password must not be found in leaked password lists",
            ], $explainer->explain(Password::default()));
    }

    public function testExplainDefaultPolicy(): void
    {
        $explainer = new PasswordPolicyExplainer();
        $this
            ->setPasswordPolicy( 8, 16 )
            ->assertEquals($explainer->explain(Password::default()), $explainer->explainDefault());
    }
}