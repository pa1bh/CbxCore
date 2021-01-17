<?php


namespace cybox\cbxcore;


use cybox\cbxcore\db\DbModal;

abstract class UserModel extends DbModal
{
    abstract public function getDisplayName(): string;
}