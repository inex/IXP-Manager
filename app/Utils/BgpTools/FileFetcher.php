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

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use IXP\Exceptions\GeneralException;

/**
 * FileFetcher loads and returns the contents of the given URL or file.
 * @author     Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class FileFetcher
{
    /**
     * Provided a url, load and return its contents.
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws GeneralException
     */
    public function fetch( string $url ): string
    {
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $this->fetchHttp($url);
        }
        return $this->fetchFile($url);
    }

    /**
     * Handle an HTTP url. Throws an exception if the HTTP request fails.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws GeneralException
     */
    private function fetchHttp( string $url ): string
    {
        $response = Http::withHeader( "User-Agent", "IXP-Manager" )
            ->get( $url );

        if ( ! $response->successful() ) {
            Log::warning( "HTTP error [{$response->getStatusCode()}] while fetching file" );
            throw new GeneralException( "HTTP error [{$response->getStatusCode()}] while fetching file: " . $response->body() );
        }

        return $response->body();
    }

    /**
     * Reads the contents of a file. Throws an exception if the file cannot be read.
     *
     * @throws GeneralException
     */
    private function fetchFile( string $file ): string
    {
        if ( ( $text = file_get_contents( $file ) ) === false ) {
            throw new GeneralException("Could not fetch file: $file");
        }
        return $text;
    }
}