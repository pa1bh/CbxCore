<?php
namespace app\core;

use app\core\middlewares\BaseMiddleware;

class Controller
{
    public string $layout = 'main';
    public string $action = '';

    /**
     * @var BaseMiddleware[]
     */
    protected array $middlewares = [];

    /**
     * @return BaseMiddleware[]
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function render(string $view, array $params = []): string
    {
        return Application::$app->view->renderView($view, $params);
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function registerMiddleware(BaseMiddleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }
}