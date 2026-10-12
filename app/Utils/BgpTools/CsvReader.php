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

namespace IXP\Utils\BgpTools;

use IXP\Exceptions\GeneralException;

/**
 * CsvReader reads a provided CSV file
 * @author     Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class CsvReader
{
    /**
     * Provided the contents of a CSV file ($csvData), this function returns
     * a generator that yields each row of the CSV file. If the optional
     * $headers parameters is provided, the CSV file headers are checked
     * against this list to ensure they match.
     *
     * @throws GeneralException
     */
    public function read( string $csvData, array $headers = []): \Generator
    {
        try {
            // Load $csvData into a stream for fgetcsv
            if ( ( $memory = fopen("php://memory", "w") ) === false ) {
                throw new GeneralException("Could not open temporary file");
            } else if ( fwrite($memory, $csvData ) === false ) {
                throw new GeneralException("Could not write to temporary file");
            }
            // set file pointer to beginning
            rewind( $memory );

            $headersFromFile = null;
            while ( ($line = fgetcsv( $memory) ) != false ) {
                if ( null === $headersFromFile ) {
                    // The first line is the header - check they match the columns we are expecting
                    if (count($line) < count($headers)) {
                        throw new GeneralException("CSV file has less columns than expected!");
                    }
                    // Check the first few columns match what we expect. Allow the file to grow and have more columns in future.
                    $headersFromFile = array_slice($line, 0, count($headers));
                    if ( $headersFromFile != $headers ) {
                        throw new GeneralException("CSV file headers do not match what was expected!");
                    }
                } else {
                    // For the remainder of the file, yield the row to the caller
                    yield $line;
                }
            }
        } finally {
            fclose($memory);
        }
    }
}