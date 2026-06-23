<?php

require_once __DIR__ . '/../vendor/autoload.php';

use function Ellephanty\Controller\controller;

$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();

class ControllerTest extends PHPUnit_Framework_TestCase
{
    public function testController()
    {
        ob_start();

        controller(function ($data) {
            return ['ok' => true];
        });

        $output = ob_get_clean();

        $this->assertJson($output);
        $this->assertContains('ok', $output);
    }

    public function testControllerException()
    {
        ob_start();

        controller(function () {
            throw new Exception('error');
        });

        $output = ob_get_clean();

        $this->assertJson($output);
        $this->assertContains('message', $output);
    }
}
