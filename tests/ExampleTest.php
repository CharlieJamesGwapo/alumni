<?php
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testHelloWorld()
    {
        $this->assertEquals('Hello, World!', 'Hello, World!');
    }
}