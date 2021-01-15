<?php


namespace app\core;


class View
{
    public string $title = 'Cybox CMS default title';

    public function renderView(string $view, array $params = []): string
    {
        $viewContent = $this->renderOnlyView($view, $params);
        $layoutContent = $this->layoutContent();


        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    public function renderContent(string $viewContent): string
    {
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }


    public function layoutContent(): string
    {
        $layout = Application::$app->layout;

        if (Application::$app->controller){
            $layout = Application::$app->controller->layout;
        }

        ob_start();
        include_once Application::$ROOT_DIR . '/views/layouts/' . $layout . '.php';
        return ob_get_clean();
    }

    public function renderOnlyView(string $view, array $params): string
    {
        foreach ($params as $key => $value){
            $$key = $value; // named variable ($$)
        }

        ob_start();
        include_once Application::$ROOT_DIR . '/views/' . $view . '.php';
        return ob_get_clean();
    }

}