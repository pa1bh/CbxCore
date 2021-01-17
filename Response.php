<?php


namespace cybox\cbxcore;


class Response
{
    public function setStatusCode(int $code): void
    {
        http_response_code($code);
    }

    public function redirect(string $location): void
    {
        header('Location: '.$location);
    }
}