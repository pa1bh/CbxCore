<?php


namespace app\core;


use JetBrains\PhpStorm\Pure;

class Request
{
    #[Pure] public function getPath(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');

        //Debug::dump($path);

        if ($position === false) {
            return $path;
        }

        return substr($path, 0 , $position);
    }

    #[Pure] public function method(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    #[Pure] public function isGet(): bool
    {
        return $this->method() === 'get';
    }

    #[Pure] public function isPost(): bool
    {
        return $this->method() === 'post';
    }

    #[Pure] public function getBody(): array
    {
        $body = [];
        if ($this->method() === 'get'){
            foreach ($_GET as $key => $value){
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->method() === 'post'){
            foreach ($_POST as $key => $value){
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }
}