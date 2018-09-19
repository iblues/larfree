<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

//require_once __DIR__.'/../app/functions.php';

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
}
