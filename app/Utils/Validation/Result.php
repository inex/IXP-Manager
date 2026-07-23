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

namespace IXP\Utils\Validation;

/**
 * Simple DTO containing a message and a status, and some optional
 */
class Result
{
    private(set) ?string           $docsUrl        = null;
    private(set) ?string           $settingsUrl    = null;
    private(set) ?CallToActionLink $callToAction   = null;

    public function __construct(
        readonly private(set) string     $message,
        readonly private(set) ResultType $type,
    ) {}

    public function withDocsPath( string $docsPath ): Result
    {
        return $this->withDocsUrl(documentation_url($docsPath));
    }

    public function withDocsUrl(string $docsLink): Result
    {
        $this->docsUrl = $docsLink;
        return $this;
    }

    public function withSettingsUrl(string $settingsUrl): Result
    {
        $this->settingsUrl = $settingsUrl;
        return $this;
    }

    public function withCallToAction(string $linkText, string $url): Result
    {
        $this->callToAction = new CallToActionLink($linkText, $url);
        return $this;
    }

}