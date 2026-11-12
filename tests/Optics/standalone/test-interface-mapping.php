<?php

declare(strict_types=1);

function resolveMedia(string $ifName, array $opticMedia): ?string
{
    $entityMedia = null;

    // Match exact interface or its immediate physical parent.
    // Never climb multiple levels in the interface hierarchy.
    if (preg_match('/^Ethernet[0-9]+(?:\/[0-9]+)*$/', $ifName)) {
        if (isset($opticMedia[$ifName])) {
            $entityMedia = $opticMedia[$ifName];
        } elseif (str_contains($ifName, '/')) {
            $parent = substr(
                $ifName,
                0,
                strrpos($ifName, '/')
            );

            $entityMedia = $opticMedia[$parent] ?? null;
        }
    }

    return $entityMedia;
}

$optics = [
    'Ethernet8' => '100GBASE-LR4',
    'Ethernet8/1' => '400GBASE-AR8',
    'Ethernet8/3' => '400GBASE-SR8',
    'Ethernet23' => 'AMP-ZR',
    'Ethernet40' => '400GBASE-ZR',
];

$cases = [
    ['Exact nested match', 'Ethernet8/1', '400GBASE-AR8'],
    ['Second nested match', 'Ethernet8/3', '400GBASE-SR8'],
    ['Base interface match', 'Ethernet8', '100GBASE-LR4'],
    ['Parent fallback', 'Ethernet23/1', 'AMP-ZR'],
    ['Unrelated interface', 'Ethernet24/1', null],
    ['Non-Ethernet interface', 'Management1', null],
    ['Unknown interface', 'Ethernet99', null],
    ['Immediate parent only', 'Ethernet23/1/2', null],
    ['Exact match precedence', 'Ethernet8/1/1', '400GBASE-AR8'],
    ['No prefix collision', 'Ethernet4', null],
];

$failures = 0;

foreach ($cases as [$name, $interface, $expected]) {
    $actual = resolveMedia($interface, $optics);

    if ($actual === $expected) {
        echo "PASS: $name\n";
    } else {
        echo "FAIL: $name\n";
        $failures++;
    }
}

echo "\nTests: " . count($cases) . "\n";
echo "Failures: $failures\n";

exit($failures === 0 ? 0 : 1);
