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

use JsonSerializable;

/**
 * This class is used to provide a JSON serialization format for the
 * state of a Backend/ValidationRunner.
 */
readonly class ValidationReport implements JsonSerializable
{
    public function __construct(
        public string $name,
        public string $description,
        public int $priority,
        public bool $isComplete,
        public bool $isFailed,
        public bool $isTimedOut,
        /** @var Software[] */
        public array $software,
        /** @var Result[] */
        public array $results,
        public ?FailureInfo $failure = null,
    ) {}

    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'name'        => $this->name,
            'description' => $this->description,
            'priority'    => $this->priority,
            'is_complete' => $this->isComplete,
            'is_failed'   => $this->isFailed,
            'is_timedout' => $this->isTimedOut,
            'software'    => $this->software,
            'results'     => $this->results,
            'failure'     => $this->failure,
        ];
    }
}