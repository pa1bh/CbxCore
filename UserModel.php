<?php


namespace app\core;


use app\core\db\DbModal;

abstract class UserModel extends DbModal
{
    abstract public function getDisplayName(): string;
}