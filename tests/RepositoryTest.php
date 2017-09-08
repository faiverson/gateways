<?php

namespace faiverson\gateways\tests;

use PHPUnit_Framework_TestCase;

class RepositoryTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        require __DIR__.'/../vendor/autoload.php';
    }

    public function tearDown(){}

    public function testRepository()
    {
        $this->assertTrue(true);
    }
}