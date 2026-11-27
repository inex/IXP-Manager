<?php

declare(strict_types=1);

namespace Tests\Diagnostics;

use IXP\Models\PhysicalInterface;
use IXP\Models\SwitchPort;
use IXP\Services\Diagnostics\DiagnosticResult;
use IXP\Services\Diagnostics\Suites\TransceiverDiagnosticSuite;
use OSS_SNMP\SNMP;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;

/** No application, database, SNMP transport or physical device is required. */
class TransceiverDiagnosticSuiteTest extends TestCase
{
    private function lightLevels(array $responses, int $speed = 100000): array
    {
        $pi = (new ReflectionClass(PhysicalInterface::class))->newInstanceWithoutConstructor();
        $pi->speed = $speed;
        $port = (new ReflectionClass(SwitchPort::class))->newInstanceWithoutConstructor();
        $port->name = 'Ethernet3/1';
        $pi->setRelation('switchPort', $port);

        $snmp = $this->createStub(SNMP::class);
        $snmp->method('get')->willReturnCallback(static function ($oid) use ($responses) {
            if (!array_key_exists($oid, $responses)) {
                throw new RuntimeException('Sensor is unavailable');
            }
            return $responses[$oid];
        });
        $suite = (new ReflectionClass(TransceiverDiagnosticSuite::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty($suite, 'pi'))->setValue($suite, $pi);
        (new ReflectionProperty($suite, 'snmpClient'))->setValue($suite, $snmp);
        return (new ReflectionMethod($suite, 'lightLevels'))->invoke($suite, $pi);
    }

    private static function oid(int $lane, int $sensor): string
    {
        return sprintf(TransceiverDiagnosticSuite::OID_XCVR_DOM_LANE_SENSOR_UNITS_THRESHOLD, 3, $lane, $sensor);
    }

    private static function responses(array $lanes): array
    {
        $responses = [];
        foreach ($lanes as $lane) {
            foreach ([1, 2, 3] as $sensor) {
                $responses[self::oid($lane, $sensor)] = 'Sensor value '
                    . ($sensor === 1 ? '42.21 mA' : '1.0 mW') . ' is within bounds';
            }
        }
        return $responses;
    }

    public function testAbsentLanesDoNotCrashTheSummary(): void
    {
        $results = $this->lightLevels(self::responses([1]));
        $summary = $results[array_key_last($results)]->narrativeHtml;
        self::assertSame(9, substr_count($summary, 'N/A'));
        self::assertStringContainsString('+42.21', $summary);
    }

    public function testMissingTransmitSensorDoesNotHideReceivePower(): void
    {
        $responses = self::responses([1]);
        unset($responses[self::oid(1, 2)]);
        $results = $this->lightLevels($responses);
        self::assertStringContainsString('ERR', $results[array_key_last($results)]->narrativeHtml);
        self::assertStringContainsString('+0.00', $results[array_key_last($results)]->narrativeHtml);
    }

    public function testAllMissingSensorsStillProduceASummary(): void
    {
        $results = $this->lightLevels([]);
        self::assertStringContainsString('N/A', $results[array_key_last($results)]->narrativeHtml);
    }

    public function testSingleLaneAndFourLaneReadingsArePreserved(): void
    {
        foreach ([[[1], 10000], [[1, 2, 3, 4], 100000]] as [$lanes, $speed]) {
            $results = $this->lightLevels(self::responses($lanes), $speed);
            $summary = $results[array_key_last($results)]->narrativeHtml;
            self::assertStringNotContainsString('N/A', $summary);
            self::assertSame(count($lanes), substr_count($summary, '+42.21'));
        }
    }

    public static function sensorValues(): array
    {
        return [
            'signed dBm' => ['Sensor value -12.5 dBm is within bounds', '-12.50', false],
            'scientific mW' => ['Sensor value 1.0e-3 mW is within bounds', '-30.00', false],
            'zero mW' => ['Sensor value 0 mW is outside bounds', '-Inf', false],
            'malformed status' => ['Sensor unavailable', 'N/A', true],
            'wrong optical unit' => ['Sensor value 5 mA is within bounds', 'N/A', true],
            'negative mW' => ['Sensor value -1 mW is outside bounds', 'N/A', true],
            'nonfinite mW' => ['Sensor value 1e999 mW is within bounds', 'N/A', true],
        ];
    }

    #[DataProvider('sensorValues')]
    public function testOpticalValuesAreValidated(string $value, string $expected, bool $unknown): void
    {
        $responses = self::responses([1]);
        $responses[self::oid(1, 2)] = $value;
        $results = $this->lightLevels($responses, 10000);
        self::assertStringContainsString($expected, $results[array_key_last($results)]->narrativeHtml);
        if ($unknown) {
            self::assertTrue((bool)array_filter($results, static fn ($r) => $r->result === DiagnosticResult::TYPE_UNKNOWN));
        }
    }
}
