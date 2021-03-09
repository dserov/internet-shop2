<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 03.03.2021
 * Time: 11:17
 */

class Http
{
    public static function header_code_verbose($code)
    {
        switch ($code):
            case 200:
                return '200 OK';
            case 201:
                return '201 Created';
            case 204:
                return '204 No Content';
            case 205:
                return '205 Reset Content';
            case 301:
                return '301 Moved Permanently';
            case 400:
                return '400 Bad Request';
            case 401:
                return '401 Unauthorized';
            case 403:
                return '403 Forbidden';
            case 404:
                return '404 Not Found';
            case 405:
                return '405 Method Not Allowed';
            case 416:
                return '416 Requested Range Not Satisfiable';
            case 418:
                return "418 I'm a teapot";
            case 422:
                return '422 Unprocessable Entity';
            default:
                return '500 Internal Server Error';
        endswitch;
    }

    public static function response($code, $data = [], $contentType = 'application/json', $charset = 'UTF-8')
    {
        header('HTTP/1.1 ' . self::header_code_verbose($code));
        header('Status: ' . self::header_code_verbose($code) . "\r\n");
        header("Connection: Close\r\n");
        $ct = "Content-Type: $contentType";
        if ($charset)
            $ct .= "; charset=$charset";
        header($ct);
        $content = json_encode($data);
        if ($content) {
            header('Content-Length: ' . strlen($content) . "\r\n\r\n");
            print $content;
            exit;
        }
    }
}
