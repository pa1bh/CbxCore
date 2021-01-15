<?php


namespace app\core\exception;


class ForbiddenException extends \Exception
{
    protected $message = "Geen toegang tot deze pagina";
    protected $code = 403;
}