<?php

declare(strict_types=1);

namespace Tests\Rules;

use Illuminate\Foundation\Testing\TestCase;
use IXP\Rules\NoIllegalChars;
use Tests\TestUtils\ValidationErrorLogger;

class NoIllegalCharsTest extends TestCase
{
    public function testRule()
    {
        $rule = new NoIllegalChars();

        $rule->validate("", "Hello World", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(0, $fails->fails);

        $rule->validate("", "|`¬!£$%^&*())_-={}[]:@~;'#<>?,./", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(0, $fails->fails);

        $rule->validate("", "Привет, мир", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(0, $fails->fails);

        $rule->validate("", "|`¬!£$%^&*())_-={}[]:@~;'#<>?,./", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(0, $fails->fails);

        $rule->validate("", "Hello\"", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals("The :attribute contains invalid characters (double quotes, black-slashes, and new lines are not allowed).", $fails->fails[0]);

        $rule->validate("", "Hello\r", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals("The :attribute contains invalid characters (double quotes, black-slashes, and new lines are not allowed).", $fails->fails[0]);

        $rule->validate("", "Hello\n", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals("The :attribute contains invalid characters (double quotes, black-slashes, and new lines are not allowed).", $fails->fails[0]);

        $rule->validate("", "Hello\0", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals("The :attribute contains invalid characters (double quotes, black-slashes, and new lines are not allowed).", $fails->fails[0]);

        $rule->validate("", "Hello\\", ($fails = new ValidationErrorLogger)(...));
        $this->assertCount(1, $fails->fails);
        $this->assertEquals("The :attribute contains invalid characters (double quotes, black-slashes, and new lines are not allowed).", $fails->fails[0]);
    }
}