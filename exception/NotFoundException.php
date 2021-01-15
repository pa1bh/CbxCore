<?php


namespace app\core\exception;


class NotFoundException extends \Exception
{
    protected $message = "Pagina niet gevonden";
    protected $code = 404;
}