<?php

namespace IXP\Optics;

class AristaTransceiverParser
{
    /**
     * Parse ENTITY-MIB descriptions into physical-port media mappings.
     *
     * @param array $descriptions entPhysicalDescr records
     * @return array<string, string>
     */
    public static function parse(array $descriptions): array
    {
        $result = [];
        $conflicts = [];

        foreach ($descriptions as $description) {
            if (!is_string($description)) {
                continue;
            }

            if (!preg_match(
                '~^Xcvr for (Ethernet[0-9]+(?:/[0-9]+)*):.*?\bmedia\s+([A-Za-z0-9][A-Za-z0-9._+-]*)\s*$~i',
                trim($description),
                $matches
            )) {
                continue;
            }

            $port = $matches[1];
            $media = $matches[2];

            // Ignore switch-reported unknown optical classifications.
            if (preg_match('/^Unknown(?:Optical.*)?$/i', $media)) {
                continue;
            }

            if (isset($result[$port]) && $result[$port] !== $media) {
                $conflicts[$port] = true;
                continue;
            }

            if (!isset($conflicts[$port])) {
                $result[$port] = $media;
            }
        }

        foreach (array_keys($conflicts) as $port) {
            unset($result[$port]);
        }

        return $result;
    }
}
