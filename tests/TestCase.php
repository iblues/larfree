<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

//require_once __DIR__.'/../app/functions.php';

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication,DatabaseTransactions;
}
