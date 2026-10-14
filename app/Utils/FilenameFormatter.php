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

class FilenameFormatter
{
    public static function truncate(string $name, int $truncatedLength = 80): string
    {
        if (mb_strlen($name) <= $truncatedLength) {
            // No truncation required
            return $name;
        }

        $extension = pathinfo($name, PATHINFO_EXTENSION);
        if ($extension === '' || mb_strlen($extension) > 15) {
            // If there is no extension, or the extension is too long, just truncate.
            return mb_substr($name, 0, $truncatedLength - mb_strlen('...')) . '...';
        }

        // Long filename with a reasonable extension: account for extension, the . separator, and the ... in our budget
        $maxBasenameLength = $truncatedLength - mb_strlen($extension) - 4;

        $basename = pathinfo($name, PATHINFO_FILENAME);
        return mb_substr($basename, 0, $maxBasenameLength) . '...' . '.' . $extension;
    }
}