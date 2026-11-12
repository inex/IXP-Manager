<?php

namespace IXP\Optics;

use OSS_SNMP\SNMP;

class TransceiverDiscovery
{
    /**
     * Discover transceiver media from Arista ENTITY-MIB.
     *
     * Distinguishes successful empty inventory from polling failure.
     *
     * @return array{success: bool, media: array<string,string>, error: ?string}
     */
    public static function discoverWithStatus(
        SNMP $host,
        bool $isArista
    ): array {
        if (!$isArista) {
            return [
                'success' => false,
                'media' => [],
                'error' => 'Unsupported vendor',
            ];
        }

        try {
            $descriptions = $host->useEntity()->physicalDescription();

            if (!is_array($descriptions)) {
                return [
                    'success' => false,
                    'media' => [],
                    'error' => 'Invalid ENTITY-MIB response',
                ];
            }

            return [
                'success' => true,
                'media' => AristaTransceiverParser::parse($descriptions),
                'error' => null,
            ];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'media' => [],
                'error' => 'SNMP ENTITY-MIB query failed',
            ];
        }
    }

    /**
     * Backward-compatible discovery method.
     *
     * @return array<string,string>
     */
    public static function discover(
        SNMP $host,
        bool $isArista
    ): array {
        return self::discoverWithStatus($host, $isArista)['media'];
    }
}
