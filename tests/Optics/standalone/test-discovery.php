<?php
declare(strict_types=1);

namespace OSS_SNMP {
    class SNMP {
        public array $data = [];
        public bool $fail = false;

        public function useEntity(): object {
            return new class($this) {
                public function __construct(
                    private SNMP $snmp
                ) {}

                public function physicalDescription(): mixed {
                    if ($this->snmp->fail) {
                        throw new \RuntimeException(
                            'TEST_SECRET_DO_NOT_LOG'
                        );
                    }
                    return $this->snmp->data;
                }
            };
        }
    }
}

namespace {

$sourceDir = getenv('IXPM_SOURCE_DIR');

if (!$sourceDir || !is_dir($sourceDir)) {
    fwrite(STDERR,
        "ERROR: Set IXPM_SOURCE_DIR to a patched IXP Manager checkout.\n"
    );
    exit(2);
}

    require getenv('IXPM_SOURCE_DIR')
        . '/app/Optics/AristaTransceiverParser.php';

    require getenv('IXPM_SOURCE_DIR')
        . '/app/Optics/TransceiverDiscovery.php';

    use OSS_SNMP\SNMP;
    use IXP\Optics\TransceiverDiscovery;

    $snmp = new SNMP();
    $failed = 0;

    function check(
        string $name,
        bool $ok
    ): void {
        global $failed;

        echo ($ok ? 'PASS: ' : 'FAIL: ')
            . $name . PHP_EOL;

        if (!$ok) {
            $failed++;
        }
    }

    $snmp->data = [
        'Xcvr for Ethernet1: model TEST type QSFP-DD media 400GBASE-ZR',
        'Xcvr for Ethernet2: model TEST type SFP media Unknown'
    ];

    $r = TransceiverDiscovery::discoverWithStatus(
        $snmp, true
    );

    check(
        'Recognized optic',
        $r['success'] === true
        && $r['media'] === [
            'Ethernet1' => '400GBASE-ZR'
        ]
    );

    $snmp->data = [];

    $r = TransceiverDiscovery::discoverWithStatus(
        $snmp, true
    );

    check(
        'Successful empty inventory',
        $r['success'] === true
        && $r['media'] === []
    );

    $r = TransceiverDiscovery::discoverWithStatus(
        $snmp, false
    );

    check(
        'Unsupported vendor rejected',
        $r['success'] === false
        && $r['media'] === []
    );

    $snmp->data = ['invalid' => new \stdClass()];

    $r = TransceiverDiscovery::discoverWithStatus(
        $snmp, true
    );

    check(
        'Malformed entry ignored',
        $r['success'] === true
        && $r['media'] === []
    );

    $snmp->fail = true;

    $r = TransceiverDiscovery::discoverWithStatus(
        $snmp, true
    );

    check(
        'SNMP exception handled',
        $r['success'] === false
        && $r['media'] === []
        && $r['error'] === 'SNMP ENTITY-MIB query failed'
        && !str_contains(json_encode($r), 'TEST_SECRET_DO_NOT_LOG')
    );

    echo "\nFailures: $failed\n";
    exit($failed === 0 ? 0 : 1);
}
