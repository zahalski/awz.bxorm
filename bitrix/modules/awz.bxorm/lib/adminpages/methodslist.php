<?php

namespace Awz\BxOrm\AdminPages;

use Bitrix\Main\Localization\Loc;
use Awz\Admin\IList;
use Awz\Admin\IParams;
use Awz\Admin\Helper;

Loc::loadMessages(__FILE__);

class MethodsList extends IList implements IParams {

    public function __construct($params){
        parent::__construct($params);
    }

    public function trigerGetRowListAdmin($row){
        Helper::viewListField($row, 'ID', ['type'=>'entity_link'], $this);
        Helper::viewListField($row, 'NAME', ['type'=>'entity_link'], $this);
        Helper::editListField($row, 'NAME', ['type'=>'string'], $this);
        Helper::editListField($row, 'CODE', ['type'=>'string'], $this);
        Helper::editListField($row, 'ACTIVE', ['type'=>'checkbox'], $this);
        $methods = [];
        $fields = [];
        if(isset($row->arRes['PARAMS']['methods']) && !empty($row->arRes['PARAMS']['methods'])){
            foreach($row->arRes['PARAMS']['methods'] as $k=>$v){
                if($v === 'Y')
                    $methods[] = $row->arRes['CODE'].'.'.$k;
            }
        }
        if(isset($row->arRes['PARAMS']['fields']) && !empty($row->arRes['PARAMS']['fields'])){
            foreach($row->arRes['PARAMS']['fields'] as $k=>$v){
                if($v['isActive'] === 'Y')
                    $fields[] = $k.' - '.$v['type'];
            }
        }
        $row->AddViewField('MODULES',implode(", ", $row->arRes["MODULES"]));
        $row->AddViewField('PARAMS', '<b>'.Loc::getMessage('AWZ_BXORM_METHODS_LIST_METHODS').'</b>:<br>'.implode(', ', $methods).'<br>'.'<b>'.Loc::getMessage('AWZ_BXORM_METHODS_LIST_FIELDS').'</b>:<br>'.implode(', ', $fields));
    }

    public function trigerInitFilter(){
    }

    public function trigerGetRowListActions(array $actions): array
    {
        return $actions;
    }

    public static function getTitle(): string
    {
        return Loc::getMessage('AWZ_BXORM_METHODS_LIST_TITLE');
    }

    public static function getParams(): array
    {
        $arParams = array(
            "ENTITY" => "\\Awz\\BxOrm\\MethodsTable",
            "FILE_EDIT" => "awz_bxorm_methods_edit.php",
            "BUTTON_CONTEXTS"=>array('btn_new'=>array(
                'TEXT'=>Loc::getMessage('AWZ_BXORM_METHODS_LIST_ADD_BTN'),
                'ICON'	=> 'btn_new',
                'LINK'	=> 'awz_bxorm_methods_edit.php?lang='.LANG
            )),
            "ADD_GROUP_ACTIONS"=>array("edit","delete"),
            "ADD_LIST_ACTIONS"=>array("delete","edit"),
            "FIND"=>[],
            "FIND_FROM_ENTITY"=>['ID'=>[],'NAME'=>[],'CODE'=>[],'ENTITY'=>[],'ACTIVE'=>[]]
        );
        return $arParams;
    }
}