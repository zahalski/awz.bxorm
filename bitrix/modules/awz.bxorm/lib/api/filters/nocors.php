<?php

namespace Awz\BxOrm\Api\Filters;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Security;
use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Awz\BxOrm\Helper;
use Awz\BxOrm\Api\Scopes\BaseFilter;

Loc::loadMessages(__FILE__);

class NoCors extends BaseFilter {

    /**
     * AppAuth constructor.
     * @param array $params
     * @param string[] $scopesBx
     * @param Scope[] $scopes
     * @param string[] $scopesRequired
     */
    public function __construct(
        array $params = array(), array $scopesBx = array(),
        array $scopes = array(), array $scopesRequired = array()
    ){
        parent::__construct($params, $scopesBx, $scopes, $scopesRequired);
    }

    public function onBeforeAction(Event $event)
    {
        $response = \Bitrix\Main\Context::getCurrent()->getResponse();
        $origin = $this->origin ?: \Bitrix\Main\Context::getCurrent()->getRequest()->getHeader('Origin');
        if ($origin)
        {
            $response->addHeader('Access-Control-Allow-Origin', $origin);
        }else{
            $response->addHeader('Access-Control-Allow-Origin', "*");
        }
    }

}