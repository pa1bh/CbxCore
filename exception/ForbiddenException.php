<?php


namespace cybox\cbxcore\exception;


class ForbiddenException extends \Exception
{
    protected $message = "Geen toegang tot deze pagina";
    protected $code = 403;
}