<?php

declare(strict_types=1);

$sourceDir = getenv('IXPM_SOURCE_DIR');

if (!$sourceDir || !is_dir($sourceDir)) {
    fwrite(STDERR,
        "ERROR: Set IXPM_SOURCE_DIR to a patched IXP Manager checkout.\n"
    );
    exit(2);
}


require getenv('IXPM_SOURCE_DIR')
    . '/app/Optics/AristaTransceiverParser.php';

use IXP\Optics\AristaTransceiverParser;

$cases = [
    '400G nested interfaces' => [
        [
            'Xcvr for Ethernet8/1: model TEST type QSFP-DD media 400GBASE-AR8',
            'Xcvr for Ethernet8/3: model TEST type QSFP-DD media 400GBASE-SR8',
        ],
        [
            'Ethernet8/1' => '400GBASE-AR8',
            'Ethernet8/3' => '400GBASE-SR8',
        ],
    ],

    '10G DWDM' => [
        [
            'Xcvr for Ethernet1: model SFP-10GDWER-39 type SFP media 10GBASE-ER',
            'Xcvr for Ethernet2: model SFP-10GDWZR-27 type SFP media Unknown',
        ],
        [
            'Ethernet1' => '10GBASE-ER',
        ],
    ],

    '400G coherent optics' => [
        [
            'Xcvr for Ethernet25: model QDD-400G-ZR type QSFP-DD media 400GBASE-ZR',
        ],
        [
            'Ethernet25' => '400GBASE-ZR',
        ],
    ],

    'Arista optical amplifier' => [
        [
            'Xcvr for Ethernet23: model QSFP-AMP-ZR type QSFP media AMP-ZR',
        ],
        [
            'Ethernet23' => 'AMP-ZR',
        ],
    ],

    'Unknown classifications' => [
        [
            'Xcvr for Ethernet3: model TEST type SFP media Unknown',
            'Xcvr for Ethernet4: model TEST type SFP media UnknownOptical10G',
        ],
        [],
    ],

    'Conflicting descriptions' => [
        [
            'Xcvr for Ethernet5: model TEST type QSFP media 100GBASE-LR4',
            'Xcvr for Ethernet5: model TEST type QSFP media 100GBASE-SR4',
        ],
        [],
    ],
];

$failed = 0;

foreach ($cases as $name => [$descriptions, $expected]) {
    $actual = AristaTransceiverParser::parse($descriptions);

    if ($actual === $expected) {
        echo "PASS: $name\n";
    } else {
        echo "FAIL: $name\n";
        $failed++;
    }
}

echo "\nTests: " . count($cases) . "\n";
echo "Failures: $failed\n";

exit($failed === 0 ? 0 : 1);
