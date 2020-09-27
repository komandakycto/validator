<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Error;

class ResultTest extends TestCase
{
    /**
     * @test
     */
    public function isValidByDefault(): void
    {
        $result = new Error();
        $this->assertTrue($result->isValid());
    }

    /**
     * @test
     */
    public function errorsAreEmptyByDefault(): void
    {
        $result = new Error();
        $this->assertEmpty($result->getErrors());
    }

    /**
     * @test
     */
    public function errorIsProperlyAdded(): void
    {
        $result = new Error();
        $result->addError('Error');
        $this->assertContains('Error', $result->getErrors());
    }

    /**
     * @test
     */
    public function addingErrorChangesIsValid(): void
    {
        $result = new Error();
        $result->addError('Error');
        $this->assertFalse($result->isValid());
    }
}
