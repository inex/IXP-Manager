<?php

namespace Tests\Utils\Validation;

use IXP\Utils\Validation\FailureInfo;
use Tests\TestCase;

class FailureInfoTest extends TestCase
{
    public function testFailureInfo()
    {
        $exception = new \Exception("oops");
        $failureInfo = FailureInfo::fromThrowable($exception);

        $this->assertEquals(get_class($exception), $failureInfo->class);
        $this->assertEquals($exception->getLine(), $failureInfo->line);
        $this->assertEquals($exception->getFile(), $failureInfo->file);
        $this->assertEquals($exception->getMessage(), $failureInfo->message);
    }
}