<?php

namespace EventyClassic\Tests;

class DummyClass
{
    public function filter($value)
    {
        return $value.' Filtered';
    }

    public function write()
    {
        echo 'Action Fired, Baby!';
    }
}
