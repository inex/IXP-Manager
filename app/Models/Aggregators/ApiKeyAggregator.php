<?php

namespace IXP\Models\Aggregators;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use IXP\Models\ApiKey;

/**
 * @property-read \IXP\Models\User|null $user
 * @method static Builder<static>|ApiKeyAggregator newModelQuery()
 * @method static Builder<static>|ApiKeyAggregator newQuery()
 * @method static Builder<static>|ApiKeyAggregator query()
 * @mixin \Eloquent
 */
class ApiKeyAggregator extends ApiKey
{

    /**
     * Authenticate an API key
     *
     * @param string $key
     * @return ApiKey|Response
     */
    public static function authenticate( string $key ): ApiKey|Response
    {

        // 1. Basic length and format check
        if( !$key || strlen( $key ) !== 56 || !str_starts_with( $key, self::PREFIX ) ) {
            return response( 'Malformed API key structure', 401 );
        }
        // 2. Slice the token into the Payload and the Checksum (last 6 chars)
        $payload  = substr( $key, 0, -6 );
        $checksum = substr( $key, -6 );

        // 3. Validate the checksum locally
        if( crc32( $payload ) !== base62_decode( $checksum ) ) {
            return response( 'Invalid API key checksum', 401 );
        }

        // 4. Checksum passed! Now extract parts for DB lookup
        // Format: ixpm_{identifier}_{secret}
        $parts = explode('_', $payload );
        $identifier = $parts[1];
        $secret = $parts[2];

        // 5. Query the database using the optimized index
        $apiKey = ApiKey::where('token_identifier', $identifier)->first();

        if( !$apiKey || !hash_equals( $apiKey->token_hash, hash('sha256', $secret ) ) ) {
            return response( 'Invalid credentials', 401 );
        }

        return $apiKey;
    }


}
