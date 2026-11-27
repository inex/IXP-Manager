<?php
/**
 * Focused regression checks without Laravel, a database, or the RRD extension.
 * Run: php tests/standalone/sflow-no-data.php
 * Framework services and the renderer are stubbed; the production RRD loader and
 * sFlow backend are loaded below. This is not the full application test suite.
 */
namespace IXP\Services\Grapher {
    class Graph {
        public const CATEGORY_BITS = 'bits';
        private static int $nextKey = 0;
        private string $key;
        public function __construct() { $this->key = 'test-' . ++self::$nextKey; }
        public function key(): string { return $this->key; }
        public function category(): string { return 'bits'; }
        public function protocol(): string { return 'ipv4'; }
        public function period(): string { return 'day'; }
        public function title(): string { return 'P2P test'; }
        public function watermark(): string { return 'Test'; }
        public function classType(): string { return 'P2p'; }
        public function svli(): object { return (object)['id' => 2]; }
        public function dvli(): object { return (object)['id' => 3]; }
        public function backend(): object { return new class { public function name(): string { return 'sflow'; } }; }
    }
}
namespace IXP\Services\Grapher\Graph { class P2p extends \IXP\Services\Grapher\Graph {} }
namespace IXP\Utils\Grapher {
    class Mrtg { public const PERIOD_TIME = ['day' => 86400]; }
    function rrd_graph(string $path, array $options): array|false {
        if ($GLOBALS['renderFails']) { return false; }
        file_put_contents($path, 'rendered-existing-graph');
        return [];
    }
}

namespace {
    $base = dirname(__DIR__, 2);
    $tmp = sys_get_temp_dir() . '/ixpm-sflow-test-' . bin2hex(random_bytes(6));
    mkdir($tmp, 0700, true);
    mkdir($tmp . '/grapher');
    $root = $tmp . '/rrds';
    mkdir($root, 0700);
    $renderFails = false;
    class Log {
        public static array $warnings = [];
        public static function warning(string $message): void { self::$warnings[] = $message; }
        public static function notice(string $message): void {}
    }
    function storage_path(): string { return $GLOBALS['tmp']; }
    function resource_path(string $path): string { return $GLOBALS['base'] . '/resources/' . $path; }
    function config(string $key): array { return ['root' => $GLOBALS['root']]; }
    require $base . '/app/Exceptions/Utils/Grapher/FileError.php';
    require $base . '/app/Contracts/Grapher/Backend.php';
    require $base . '/app/Services/Grapher/Backend.php';
    require $base . '/app/Utils/Grapher/Rrd.php';
    require $base . '/app/Services/Grapher/Backend/Sflow.php';
    class RemoteRrd extends \IXP\Utils\Grapher\Rrd {
        public static array $response;
        protected function fetchRemoteRrd(): array { return self::$response; }
    }
    $checks = 0;
    function check(bool $ok, string $message): void {
        if (!$ok) { throw new \RuntimeException($message); }
        ++$GLOBALS['checks'];
        echo "PASS: $message\n";
    }
    function remoteFailure(array $response, bool $missing): void {
        RemoteRrd::$response = $response;
        try {
            new RemoteRrd('https://rrd.example.test/pair.rrd', new \IXP\Services\Grapher\Graph());
            throw new \RuntimeException('Expected a file error');
        } catch (\IXP\Exceptions\Utils\Grapher\FileError $e) {
            check($e->isMissingRrd() === $missing, 'Remote status ' . $response[1] . ' classified correctly');
        }
    }
    try {
        remoteFailure([false, 404], true);
        remoteFailure(['not found', 404], true);
        remoteFailure([false, 403], false);
        remoteFailure([false, 500], false);
        remoteFailure([false, 0], false);
        remoteFailure(['', 200], false);
        remoteFailure([false, 200], false);
        RemoteRrd::$response = ['valid-rrd-bytes', 200];
        $rrd = new RemoteRrd('https://rrd.example.test/pair.rrd', new \IXP\Services\Grapher\Graph());
        check($rrd->rrd() === 'valid-rrd-bytes', 'Successful remote download is preserved');
        try {
            new \IXP\Utils\Grapher\Rrd($root . '/absent/traffic.rrd', new \IXP\Services\Grapher\Graph());
            throw new \RuntimeException('Expected missing local RRD');
        } catch (\IXP\Exceptions\Utils\Grapher\FileError $e) {
            check($e->isMissingRrd(), 'Absent local pair classified as missing');
        }
        try {
            new \IXP\Utils\Grapher\Rrd($root, new \IXP\Services\Grapher\Graph());
            throw new \RuntimeException('Expected directory error');
        } catch (\IXP\Exceptions\Utils\Grapher\FileError $e) {
            check(!$e->isMissingRrd(), 'Directory supplied as RRD is an error, not no traffic');
        }
        $backend = new \IXP\Services\Grapher\Backend\Sflow();
        $graph = new \IXP\Services\Grapher\Graph\P2p();
        $png = $backend->png($graph);
        check($png === file_get_contents(resource_path('images/grapher/no-measurable-traffic.png')), 'Missing P2P data returns the no-traffic PNG');
        check(str_starts_with($png, "\x89PNG\r\n\x1a\n"), 'Placeholder is a real PNG');
        check(Log::$warnings === [], 'Expected missing data does not produce an operational warning');
        $file = $backend->dataPath($graph);
        mkdir(dirname($file), 0700, true);
        file_put_contents($file, 'existing-rrd');
        check($backend->png($graph) === 'rendered-existing-graph', 'Existing RRD renders normally when samples arrive');
        $renderFails = true;
        check($backend->png($graph) === file_get_contents(resource_path('images/grapher/temporarily-unavailable.png')), 'Renderer failure returns the unavailable PNG');
        check(count(Log::$warnings) === 1, 'Renderer failure is logged for operators');
        echo "\n$checks checks passed.\n";
    } finally {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tmp, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $file) { $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname()); }
        rmdir($tmp);
    }
}
