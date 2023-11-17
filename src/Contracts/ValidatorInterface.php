<?php

namespace ValeSaude\LaravelHealthCheck\Contracts;

use ValeSaude\LaravelHealthCheck\Result;

interface ValidatorInterface
{
    public function validate(): Result;
}