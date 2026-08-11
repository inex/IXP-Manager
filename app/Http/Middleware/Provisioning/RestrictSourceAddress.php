<?php

namespace IXP\Http\Middleware\Provisioning;

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
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

use Auth, Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use IXP\Models\ApiKey;

/**
 * Middleware: RestrictSourceAddress
 *
 * Limits the provisioning endpoints to known source addresses.
 *
 * Two independent layers, either or both of which may be unset:
 *
 *   - `ixp_api.provisioning.allowed_ips` applies to every provisioning request
 *   - `api_keys.allowed_ips` applies to the key presented
 *
 * The second is worth explaining. That column has existed for years and is exposed in the
 * key management UI, but nothing in IXP Manager ever reads it - a key restricted to one
 * address today works from anywhere. These endpoints honour it, so an operator who has
 * filled it in gets what they expected.
 *
 * This runs after `api/v4`, because the key has to be resolved before its allow-list can be
 * read.
 *
 * A note on where this belongs: the primary place to restrict these endpoints is the web
 * server, which is what the `/admin` prefix introduced in v7.1.0 is for. This is a second
 * lock on the same door - useful when the application sits behind a proxy whose ACL you do
 * not control, and harmless when it does not.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    IXP\Http\Middleware\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RestrictSourceAddress
{
    /**
     * @param  Request  $r
     * @param  Closure  $next
     *
     * @return mixed
     */
    public function handle( Request $r, Closure $next )
    {
        // request()->ip() honours TrustProxies; the helper ixp_get_client_ip() trusts
        // caller-supplied headers ahead of REMOTE_ADDR and must not be used for a decision.
        $source = $r->ip();

        if( !$this->permitted( $source, config( 'ixp_api.provisioning.allowed_ips', '' ) ) ) {
            return $this->refuse( $r, $source, 'the configured allow-list' );
        }

        if( ( $key = $this->apiKey() ) && !$this->permitted( $source, $key->allowed_ips ?? "" ) ) {
            return $this->refuse( $r, $source, 'the allow-list of the API key used' );
        }

        return $next( $r );
    }

    /**
     * The API key behind this request, if one was resolved.
     *
     * @return ApiKey|null
     */
    private function apiKey(): ?ApiKey
    {
        if( !Auth::check() || !( $token = request()->header( 'X-IXP-Manager-API-Key' ) ) ) {
            return null;
        }

        // Match on the identifier rather than the secret: keys are stored hashed.
        if( !str_starts_with( $token, ApiKey::PREFIX ) ) {
            return null;
        }

        $parts = explode( '_', $token );

        return isset( $parts[ 1 ] )
            ? ApiKey::where( 'token_identifier', $parts[ 1 ] )->first()
            : null;
    }

    /**
     * Whether a source address is covered by an allow-list.
     *
     * An empty list means no restriction at that layer - not "deny everything".
     *
     * @param  string  $source
     * @param  string  $list    comma separated addresses and CIDR ranges
     *
     * @return bool
     */
    private function permitted( string $source, string $list ): bool
    {
        $entries = array_filter( array_map( 'trim', explode( ',', $list ) ) );

        if( $entries === [] ) {
            return true;
        }

        foreach( $entries as $entry ) {
            if( $this->matches( $source, $entry ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether an address falls within a single allow-list entry.
     *
     * Handles a bare address or CIDR notation, IPv4 and IPv6. Comparison is on packed form,
     * so notation differences do not matter.
     *
     * @param  string  $source
     * @param  string  $entry
     *
     * @return bool
     */
    private function matches( string $source, string $entry ): bool
    {
        $packedSource = @inet_pton( $source );

        if( $packedSource === false ) {
            return false;
        }

        if( !str_contains( $entry, '/' ) ) {
            return @inet_pton( $entry ) === $packedSource;
        }

        [ $network, $bits ] = explode( '/', $entry, 2 );

        $packedNetwork = @inet_pton( $network );
        $bits          = (int)$bits;

        // Comparing an IPv4 address against an IPv6 range - or the reverse - is never a match.
        if( $packedNetwork === false || strlen( $packedNetwork ) !== strlen( $packedSource ) ) {
            return false;
        }

        if( $bits < 0 || $bits > strlen( $packedNetwork ) * 8 ) {
            return false;
        }

        $wholeBytes = intdiv( $bits, 8 );
        $oddBits    = $bits % 8;

        if( $wholeBytes > 0 && strncmp( $packedSource, $packedNetwork, $wholeBytes ) !== 0 ) {
            return false;
        }

        if( $oddBits === 0 ) {
            return true;
        }

        $mask = ~( ( 1 << ( 8 - $oddBits ) ) - 1 ) & 0xff;

        return ( ord( $packedSource[ $wholeBytes ] ) & $mask )
            === ( ord( $packedNetwork[ $wholeBytes ] ) & $mask );
    }

    /**
     * @param  Request  $r
     * @param  string   $source
     * @param  string   $which
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function refuse( Request $r, string $source, string $which )
    {
        Log::warning( "Provisioning API: refused {$source} - not covered by {$which} ({$r->path()})" );

        return response()->json( [
            'message' => 'This source address is not permitted to use the provisioning API.',
        ], 403 );
    }
}
