<?php
namespace cybox\cbxcore;

use cybox\cbxcore\exception\NotFoundException;

/**
 * Class Router
 * @package cybox\cbxcore
 */
class Router
{
    public Request $request;
    public Response $response;

    protected array $routes = [];

    /**
     * Router constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }


    public function get(string $path, mixed $callback): void
    //public function get(string $path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post(string $path, mixed $callback): void
    {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve(): string
    {
        //Debug::dump($this->routes);

        $path = $this->request->getPath();
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? false;


        /**
         * create an instance from first argument (controller name) of callback array
         * maw eerste argument wordt omgezet van string (controller name) naar instance
         * dit om in $this context te komen in de controllor
         *
         * daarna wordt deze weer als eerste argument van callback gezet voor call_user_func
         */
        if (is_array($callback)) {
            /** @var  Controller $controller */
            $controller = new $callback[0]();
            Application::$app->controller = $controller;
            $controller->action = $callback[1];
            $callback[0] = $controller;

            foreach ($controller->getMiddlewares() as $middleware){
                $middleware->execute();
            }
        }

        // 404 handling
        if ($callback === false){
            throw new NotFoundException();
        }

        // render view
        if (is_string($callback)){
            return Application::$app->view->renderView($callback);
        }

        // Call the method
        return call_user_func($callback, $this->request, $this->response);
    }
}