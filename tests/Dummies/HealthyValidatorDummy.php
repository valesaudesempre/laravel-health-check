<?php

namespace ValeSaude\LaravelHealthCheck\Tests\Dummies;

use ValeSaude\LaravelHealthCheck\Contracts\ValidatorInterface;
use ValeSaude\LaravelHealthCheck\Result;

class HealthyValidatorDummy implements ValidatorInterface
{
    public function validate(): Result
    {
        return Result::healthy();
    }
}