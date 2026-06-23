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

        controller(function ($request, $response) {
            return $response->status(200)->json(['ok' => true]);
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
        $this->assertEquals(500, http_response_code());
    }
}
