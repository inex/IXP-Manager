<?php

namespace Tests\Utils\Validation;

use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use IXP\Utils\Validation\Backend;
use IXP\Utils\Validation\Dto\FailureInfo;
use IXP\Utils\Validation\Enums\ResultType;
use Mockery;
use Tests\TestCase;

class BackendTest extends TestCase
{
    public function testValidationBackend()
    {
        $validator = Mockery::mock(Validator::class);
        $backend = new Backend($validator::class);
        $this->assertEmpty($backend->getResults());
        $this->assertEmpty($backend->getSoftware());

        $backend->software("MySQL", "v1.0.0");
        $backend->software("pdo-mysql", "v8.4.0");
        $backend->debug("debug message!");
        $backend->info("some informational message");
        $backend->warning("something isn't right");
        $backend->error("we got a big problem");

        $this->assertCount(2, $backend->getSoftware());
        $this->assertEquals("MySQL", $backend->getSoftware()[0]->software);
        $this->assertEquals("v1.0.0", $backend->getSoftware()[0]->version);

        $this->assertEquals("pdo-mysql", $backend->getSoftware()[1]->software);
        $this->assertEquals("v8.4.0", $backend->getSoftware()[1]->version);

        $this->assertCount(4, $backend->getResults());
        $this->assertEquals("debug message!", $backend->getResults()[0]->message);
        $this->assertEquals(ResultType::Debug, $backend->getResults()[0]->type);

        $this->assertEquals("some informational message", $backend->getResults()[1]->message);
        $this->assertEquals(ResultType::Info, $backend->getResults()[1]->type);

        $this->assertEquals("something isn't right", $backend->getResults()[2]->message);
        $this->assertEquals(ResultType::Warning, $backend->getResults()[2]->type);

        $this->assertEquals("we got a big problem", $backend->getResults()[3]->message);
        $this->assertEquals(ResultType::Error, $backend->getResults()[3]->type);
    }

    public function testValidationRunnerOk()
    {
        $validator = new class implements Validator {
            public function getDescription(): string
            {
                return "desc";
            }
            public function getName(): string
            {
                return "name";
            }
            public function getPriority(): int
            {
                return 1;
            }
            public function run( ValidationBackend $backend ): void
            {

            }
        };

        $backend = new Backend($validator::class);
        $this->assertInstanceOf($validator::class, $backend->getValidator());

        $this->assertFalse($backend->isComplete());
        $this->assertFalse($backend->isFailed());
        $this->assertFalse($backend->isTimedOut());
        $this->assertNull($backend->getFailureInfo());
        $backend->run();
        $this->assertTrue($backend->isComplete());
        $this->assertFalse($backend->isFailed());
        $this->assertFalse($backend->isTimedOut());
        $this->assertNull($backend->getFailureInfo());
    }

    public function testValidationRunnerFails()
    {
        $validator = new class implements Validator {
            public function getDescription(): string
            {
                return "desc";
            }
            public function getName(): string
            {
                return "name";
            }
            public function getPriority(): int
            {
                return 1;
            }
            public function run( ValidationBackend $backend ): void
            {
                throw new \RuntimeException("unexpected exception!");
            }
        };

        $backend = new Backend($validator::class);
        $this->assertInstanceOf($validator::class, $backend->getValidator());

        $this->assertFalse($backend->isComplete());
        $this->assertFalse($backend->isFailed());
        $this->assertFalse($backend->isTimedOut());
        $this->assertNull($backend->getFailureInfo());
        $backend->run();
        $this->assertTrue($backend->isComplete());
        $this->assertTrue($backend->isFailed());
        $this->assertFalse($backend->isTimedOut());
        $this->assertInstanceOf(FailureInfo::class, $backend->getFailureInfo());

        $failure = $backend->getFailureInfo();
        $this->assertEquals("unexpected exception!", $failure->message);
        $this->assertEquals(\RuntimeException::class, $failure->class);
        $this->assertNotEquals(0, $failure->line);
        $this->assertEquals(__FILE__, $failure->file);
    }

    public function testValidationRunnerMarkTimedOut()
    {
        $validator = new class implements Validator {
            public function getDescription(): string
            {
                return "desc";
            }
            public function getName(): string
            {
                return "name";
            }
            public function getPriority(): int
            {
                return 1;
            }
            public function run( ValidationBackend $backend ): void
            {
            }
        };

        $backend = new Backend($validator::class);
        $this->assertInstanceOf($validator::class, $backend->getValidator());

        $this->assertFalse($backend->isComplete());
        $this->assertFalse($backend->isFailed());
        $this->assertFalse($backend->isTimedOut());
        $this->assertNull($backend->getFailureInfo());
        $backend->markTimedOut();
        $this->assertFalse($backend->isComplete());
        $this->assertFalse($backend->isFailed());
        $this->assertTrue($backend->isTimedOut());
        $this->assertNull($backend->getFailureInfo());
    }
}