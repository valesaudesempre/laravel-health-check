<?php

namespace ValeSaude\LaravelHealthCheck\Events;

use ValeSaude\LaravelHealthCheck\ResultSet;

class UnhealthyApplicationComponentsEvent
{
    /** @var ResultSet */
    private $resultSet;

    public function __construct(ResultSet $resultSet)
    {
        $this->resultSet = $resultSet;
    }

    public function getResultSet(): ResultSet
    {
        return $this->resultSet;
    }
}