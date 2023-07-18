<?php
namespace Awz\BxOrm\Api\Scopes;

interface IScope
{
    public function enableScope();
    public function disableScope();
    public function checkRequire();
}