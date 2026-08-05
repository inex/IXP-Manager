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

use JsonSerializable;

/**
 * Simple DTO containing a message and a status, and some optional
 */
class Result implements JsonSerializable
{
    private(set) ?string           $docsUrl        = null;
    private(set) ?string           $settingsUrl    = null;
    private(set) ?CallToActionLink $callToAction   = null;

    /** @var AdditionalInfoElement[] */
    private(set) array             $additionalInfo = [];

    public function __construct(
        readonly private(set) string     $message,
        readonly private(set) ResultType $type,
    ) {}

    /**
     * Provide a link to documentation for this result. The relative path is provided
     * and the full url will be made from this
     * */
    public function withDocsPath( string $docsPath ): Result
    {
        return $this->withDocsUrl(documentation_url($docsPath));
    }

    /**
     * Provide an absolute link to documentation for this result
     */
    public function withDocsUrl(string $docsLink): Result
    {
        $this->docsUrl = $docsLink;
        return $this;
    }

    /**
     * Provide a link to a settings page by it's $panel, and optionally, a $field on that page
     */
    public function withSettingsLink(string $panel, ?string $field = null): Result
    {
        return $this->withSettingsUrl(settings_ui_url($panel, $field));
    }

    /**
     * Provide an absolute link to a settings page
     */
    public function withSettingsUrl(string $settingsUrl): Result
    {
        $this->settingsUrl = $settingsUrl;
        return $this;
    }

    /**
     * Provide a CallToAction link, displayed prominently beside a validation result.
     */
    public function withCallToAction(string $linkText, string $url): Result
    {
        $this->callToAction = new CallToActionLink($linkText, $url);
        return $this;
    }

    /**
     * Add a text string as additional information under a validation.
     */
    public function addAdditionalInfoText(string $text): Result
    {
        return $this->addAdditionalInfo(new AdditionalInfoTextElement($text));
    }

    /**
     * Add a url string as additional information under a validation.
     */
    public function addAdditionalInfoUrl(string $url, string $text): Result
    {
        return $this->addAdditionalInfo(new AdditionalInfoUrlElement($url, $text));
    }

    /**
     * Add an AdditionalInfoElement to the result
     */
    public function addAdditionalInfo(AdditionalInfoElement $element): Result
    {
        $this->additionalInfo[] = $element;
        return $this;
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return  [
            'message'          => $this->message,
            'type'             => $this->type,
            'additional_info'  => $this->additionalInfo,
            'docs_url'         => $this->docsUrl,
            'settings_url'     => $this->settingsUrl,
            'call_to_action'   => $this->callToAction,
        ];
    }
}