<?php
namespace app\core\form;


use app\core\Model;
use JetBrains\PhpStorm\Pure;

/**
 * Class Form
 * @package app\core\form
 */
class Form
{
    public static function begin(string $action = '', string $method = 'post'): Form
    {
        echo sprintf('<form action="%s" method="%s">', $action, $method);
        return new Form();
    }

    public static function end()
    {
        echo '</form>';
    }

    #[Pure] public function field(Model $model, $attribute): InputField
    {
        return new InputField($model, $attribute);
    }
}