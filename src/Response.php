<?php

namespace Ellephanty\Controller;

class Response
{
    private static $instance = null;
    public $status = 200;

    public function __construct($status = 200)
    {
        $this->status = $status;
    }

    public static function status($code)
    {

        if (self::$instance == null) {
            self::$instance = new self($code);
        } else {
            self::$instance->status = $code;
        }

        http_response_code($code);

        return self::$instance;
    }

    public static function json($data)
    {
        if( !headers_sent() ) {
            header('Content-Type: application/json');
        }
        echo json_encode($data);
    }
}
