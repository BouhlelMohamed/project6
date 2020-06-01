<?php

namespace App\Tests\Validator;

use App\Validator\EmailDomain;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\MissingOptionsException as ExceptionMissingOptionsException;

class EmailDomainTest extends TestCase
{
    public function testRequiredParameters() {
        $this->expectException(ExceptionMissingOptionsException::class);
        new EmailDomain();
    }

}