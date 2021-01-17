<?php
namespace cybox\cbxcore\form;


use cybox\cbxcore\Model;
use JetBrains\PhpStorm\Pure;

/**
 * Class Form
 * @package cybox\cbxcore\form
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