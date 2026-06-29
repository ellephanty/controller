<?php

namespace Ellephanty\Controller;

use Ellephanty\Model\Model;
use function Ellephanty\Alerty\exception_email;
use Ellephanty\Controller\Response;

function controller($callback)
{
    try {
        $data = json_decode(file_get_contents("php://input"), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(
                'JSON inválido en el cuerpo de la solicitud: ' . json_last_error_msg()
            );
        }

        $request = [
            'body' => $data,
            'query' => $_GET,
        ];

        $response = call_user_func($callback, $request, $response = new Response(), Model::connection());

        if (Model::database()) {
            Model::database()->close();
        }

        // imprime el tipo de variable

        if (is_object($response) && method_exists($response, 'toArray')) {
            $response = $response->toArray();
        }

        if (!is_array($response) && !is_object($response)) {
            if (!$response || $response == 'false' || $response == false || $response == null || $response == 'null') {
                return;
            }
        }

        if (is_array($response) || is_object($response)) {
            utf8ize($response);
        }

        $json = json_encode($response, JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            throw new \Exception(
                'Error al generar JSON: ' . json_last_error_msg()
            );
        }

        echo $json;
    } catch (\Exception $e) {

        if (Model::database()) {
            Model::database()->close();
        }
        exception_email($e);

        return Response::status(500)->json([
            'message' => getenv('APP_DEBUG') ? $e->getMessage() : 'Error interno',
        ]);
    }
}

function utf8ize(&$data)
{
    if (is_array($data)) {
        foreach ($data as &$value) {
            utf8ize($value);
        }
    } elseif (is_object($data)) {
        foreach ($data as &$value) {
            utf8ize($value);
        }
    } elseif (is_string($data)) {
        if (!mb_check_encoding($data, 'UTF-8')) {
            $data = mb_convert_encoding($data, 'UTF-8', 'ISO-8859-1');
        }
    }
}
