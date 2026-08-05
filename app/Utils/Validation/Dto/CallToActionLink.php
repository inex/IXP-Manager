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

namespace IXP\Utils\Validation\Dto;

/**
 * A generic container for a 'call to action' link displayed prominently beside
 * the validation message
 */
readonly class CallToActionLink implements \JsonSerializable
{
    public function __construct(
        private(set) string     $text,
        private(set) string     $url,
    ){}

    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'text' => $this->text,
            'url' => $this->url,
        ];
    }
}