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

namespace IXP\Services\Validation\Dto;

/**
 * DTO for sharing details about an exception that occurred.
 * Depending on PHP's configuration, an exception trace can contain arguments which are closures,
 * and cannot be serialized. We use this object to pass back key information about what happened instead.
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
final readonly class FailureInfo implements \JsonSerializable
{
    public function __construct(
        private(set) string $exception,
        private(set) string $message,
        private(set) string $file,
        private(set) int    $line,
    ) {}

    /**
     * Create an instance of FailureInfo from a \Throwable
     */
    public static function fromThrowable(\Throwable $e): static
    {
        return new static( get_class($e), $e->getMessage(), $e->getFile(), $e->getLine() );
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'exception' => $this->exception,
            'message'   => $this->message,
            'file'      => $this->file,
            'line'      => $this->line,
        ];
    }
}