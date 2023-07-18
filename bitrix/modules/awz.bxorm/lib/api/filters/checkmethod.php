<?php

namespace Awz\BxOrm\Api\Filters;

use Awz\BxOrm\HooksTable;
use Awz\BxOrm\MethodsTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Security;
use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Awz\BxOrm\Helper;
use Awz\BxOrm\Api\Scopes\BaseFilter;

Loc::loadMessages(__FILE__);

class CheckMethod extends BaseFilter {

    /**
     * CheckMethod constructor.
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
        if(!$this->checkRequire()){
            return null;
        }
        $this->disableScope();
        $appId = null;
        $method = null;

        if(!$appId){
            $appId = $this->getAction()->getController()->getRequest()->get('app');
        }
        if(!$method){
            $method = $this->getAction()->getController()->getRequest()->get('method');
        }

        $checkMethod = null;
        if(!$method || !$appId){
            $checkMethod = false;
        }
        $methodsKeys = [];
        if($method){
            $method = str_replace('.json','',$method);
            $methodsKeys = explode(".",preg_replace('/([^0-9a-z.])/','', $method));
            if(!is_array($methodsKeys)) $checkMethod = false;
            if(is_array($methodsKeys) && count($methodsKeys)!=2) {
                $checkMethod = false;
            }
        }

        $METHOD_ID = 0;
        if($checkMethod !== false){
            $hookData = HooksTable::getRowById($appId);
            $methods = [];
            if(!empty($hookData['METHODS'])){
                foreach($hookData['METHODS'] as $methodId){
                    $methods[] = $methodId;
                }
            }
            if(!empty($methods)){
                $methodsParamsRes = MethodsTable::getList([
                    'select'=>['*'],
                    'filter'=>['=ID'=>$methods, '=CODE'=>$methodsKeys[0], '=ACTIVE'=>'Y']
                ]);
                while($data = $methodsParamsRes->fetch()){
                    //echo'<pre>';print_r($data);echo'</pre>';
                    //die();
                    if($checkMethod === true) continue;
                    if(!empty($data['PARAMS']['methods'])){
                        foreach($data['PARAMS']['methods'] as $code=>$active){
                            if($active != 'Y') continue;
                            if($methodsKeys[1] === $code){
                                $checkMethod = true;
                                $METHOD_ID = $data['ID'];
                                break;
                            }
                        }
                    }
                }
            }
        }

        if($checkMethod !== true){
            $this->addError(new Error(
                Loc::getMessage('AWZ_BXORM_API_FILTER_CHECKFILTER_ERR'),
                'err_method'
            ));
            return new EventResult(EventResult::ERROR, null, 'awz.bxorm', $this);
        }
        foreach($this->scopesCollection as $scope){
            if($scope->getCode() === 'method'){
                /* @var $scopeCustomData \Awz\BxOrm\Api\Scopes\Parameters */
                $scopeCustomData = $scope->getCustomData();
                if($scopeCustomData instanceof \Awz\BxOrm\Api\Scopes\Parameters){
                    $scopeCustomData->set('METHOD_ID', $METHOD_ID);
                }
            }
        }
        $this->enableScope();

        return new EventResult(EventResult::SUCCESS, null, 'awz.bxorm', $this);
    }

}