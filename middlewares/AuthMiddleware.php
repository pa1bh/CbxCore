<?php
namespace cybox\cbxcore\middlewares;

use cybox\cbxcore\Application;
use cybox\cbxcore\exception\ForbiddenException;

/**
 * Class AuthMiddleware
 * @package cybox\cbxcore\middlewares
 */
class AuthMiddleware extends BaseMiddleware
{
    public array $actions = [];

    /**
     * AuthMiddleware constructor.
     * @param array $actions
     */
    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    /**
     * @throws ForbiddenException
     */
    public function execute(): void
    {
        if(Application::isGuest()){
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)){
                throw new ForbiddenException();
            }
        }
    }
}