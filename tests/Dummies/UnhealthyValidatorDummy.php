<?php

namespace ValeSaude\LaravelHealthCheck\Tests\Dummies;

use ValeSaude\LaravelHealthCheck\Contracts\ValidatorInterface;
use ValeSaude\LaravelHealthCheck\Result;

class UnhealthyValidatorDummy implements ValidatorInterface
{
    public function validate(): Result
    {
        return Result::unhealthy('Some unhealthy message.');
    }
}