<?php

namespace Awz\BxOrm\AdminPages;

use Awz\Admin\Helper;
use Awz\BxOrm\Helper as MainHelper;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\Localization\Loc;
use Awz\Admin\IForm;
use Awz\Admin\IParams;
use Bitrix\Main\Application;
use Bitrix\Main\IO\File;
use Awz\BxOrm\MethodsTable;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\Result;
use Bitrix\Main\Error;

Loc::loadMessages(__FILE__);

class MethodsEdit extends IForm implements IParams {

    public function __construct($params){
        parent::__construct($params);
    }

    public function trigerCheckActionAdd($func){
        return $func;
    }

    public function trigerCheckActionUpdate($func){
        return $func;
    }

    public static function getTitle(): string
    {
        return Loc::getMessage('AWZ_BXORM_METHODS_EDIT_TITLE');
    }

    public function getFieldValue($name){

        $value = "";
        $defValue = isset($this->fieldsValues[$name]) ? $this->fieldsValues[$name] : "";

        if(!$this->saved) {
            $value = (isset($_REQUEST[$name]) && array_key_exists($name, $_REQUEST)) ? $_REQUEST[$name] : $defValue;
        }else{
            $value = (isset($this->fieldsValues[$name])) ? $this->fieldsValues[$name] : "";
        }

        if(!$value && $name==='FIELD_TOKEN'){
            $value = MainHelper::generateToken();
        }

        if(!$value && $name==='FIELD_MODULES'){
            return [];
        }
        if(!$value && $name==='FIELD_PARAMS'){
            return [];
        }
        return $value;
    }

    public function getElValue(){
        $id = $this->getParam("ID");
        $entity = $this->getParam("ENTITY");
        $arData = $entity::getRowById($id);
        $fields = $this->getParam("FIELDS");
        foreach($fields as $group=>$fl){
            foreach($fl as $field){
                $arField = array(
                    "ID" => $field["NAME"],
                    "NAME" => "FIELD_".$field["NAME"],
                );
                $this->fieldsValues[$arField["NAME"]] = $arData[$arField["ID"]];
            }
        }
        //print_r($this->fieldsValues);
        //die();
        if(!is_array($this->fieldsValues["FIELD_MODULES"])){
            $this->fieldsValues["FIELD_MODULES"] = [];
        }
        if(!is_array($this->fieldsValues["FIELD_PARAMS"])){
            $this->fieldsValues["FIELD_PARAMS"] = [];
        }
    }

    public static function checkCorrectField(Fields\Field $field): Result
    {
        $resultCheck = new Result();
        if($field instanceof Fields\IntegerField){
            return $resultCheck;
        }
        if($field instanceof Fields\StringField){
            return $resultCheck;
        }
        if($field instanceof Fields\DatetimeField){
            return $resultCheck;
        }
        if($field instanceof Fields\DateField){
            return $resultCheck;
        }
        if($field instanceof Fields\FloatField){
            return $resultCheck;
        }
        $resultCheck->addError(new Error(Loc::getMessage('AWZ_BXORM_METHODS_EDIT_ERR2')));
        return $resultCheck;
    }
    public function paramsFieldViewIsRequired($field, $arField){
        $fieldCode = $arField['NAME'].'[fields]['.$field->getName().']';
        $value = $this->getFieldValue($arField['NAME'])['fields'][$field->getName()];
        $readOnly = false;
        if($field->getParameter('required')) {
            if(!isset($value['isRequired'])){
                $value['isRequired'] = ($field->getParameter('required')) ? "Y" : "N";
            }
        }
        ?>
        <?if($readOnly){?>
            <input type="checkbox" name="<?=$fieldCode?>[isRequired]" value="Y" checked="checked" disabled="disabled" readonly="readonly"> - <?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_IS_REQ')?>
        <?}else{?>
            <input type="checkbox" name="<?=$fieldCode?>[isRequired]" value="Y"<?=($value['isRequired']=='Y' ? ' checked="checked"' : '')?>> - <?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_IS_REQ')?>
        <?}?>

        <?php
    }
    public function paramsFieldViewReadonly($field, $arField){
        $fieldCode = $arField['NAME'].'[fields]['.$field->getName().']';
        $value = $this->getFieldValue($arField['NAME'])['fields'][$field->getName()];
        $readOnly = false;
        //if($field->getParameter('isReadonly')) $readOnly = true;
        //if($field->getParameter('primary')) $readOnly = true;
        ?>
        <?if($readOnly){?>
            <input type="checkbox" name="<?=$fieldCode?>[isReadonly]" value="Y" checked="checked" disabled="disabled" readonly="readonly"> - <?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_ONLY_READ')?>
        <?}else{?>
            <input type="checkbox" name="<?=$fieldCode?>[isReadonly]" value="Y"<?=($value['isReadonly']=='Y' ? ' checked="checked"' : '')?>> - <?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_ONLY_READ')?>
        <?}?>

        <?php
    }
    public function paramsFieldViewActive($field, $arField){
        $fieldCode = $arField['NAME'].'[fields]['.$field->getName().']';
        $value = $this->getFieldValue($arField['NAME'])['fields'][$field->getName()];
        $readOnly = false;
        //if($field->getParameter('private')) $readOnly = true;
        //if($field->getParameter('primary')) $readOnly = true;
        ?>
        <?if($readOnly){?>
            <input type="checkbox" name="<?=$fieldCode?>[isActive]" value="Y" checked="checked" disabled="disabled" readonly="readonly"> - <?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_FIELDS_ACTIVE_PRM')?>
        <?}else{?>
            <input type="checkbox" name="<?=$fieldCode?>[isActive]" value="Y"<?=($value['isActive']=='Y' ? ' checked="checked"' : '')?>> - <?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_FIELDS_ACTIVE_PRM')?>
        <?}?>

        <?php
    }
    public function paramsFieldViewIsSortable($field, $arField){
        $fieldCode = $arField['NAME'].'[fields]['.$field->getName().']';
        $value = $this->getFieldValue($arField['NAME'])['fields'][$field->getName()];
        $readOnly = false;
        //if($field->getParameter('private')) $readOnly = true;
        //if($field->getParameter('primary')) $readOnly = true;
        ?>
        <?if($readOnly){?>
            <input type="checkbox" name="<?=$fieldCode?>[isSortable]" value="Y" checked="checked" disabled="disabled" readonly="readonly"> - <?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_FIELDS_SORTABLE_PRM')?>
        <?}else{?>
            <input type="checkbox" name="<?=$fieldCode?>[isSortable]" value="Y"<?=($value['isSortable']=='Y' ? ' checked="checked"' : '')?>> - <?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_FIELDS_SORTABLE_PRM')?>
        <?}?>

        <?php
    }
    public function paramsFieldView($arField){
        $r_style = 'style="width:25%;padding:5px;border-bottom:1px solid #87919c;border-right:1px solid #87919c;"';
        $valueField = $this->getFieldValue($arField['NAME']);
        $entity = $this->getFieldValue('FIELD_ENTITY');
        $entityCl = $this->getParam("ENTITY");
        $modules = $this->getFieldValue('FIELD_MODULES');
        if(is_array($modules)){
            $errModules = 'main';
            foreach ($modules as $module){
                if(!Loader::includeModule($module)){
                    $errModules = $module;
                    break;
                }else{
                    $errModules = '';
                }
            }
        }
        if($errModules){
            ?><p style="color:red;"><?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_ERR1')?> [<?=$errModules?>]</p><?php
        }
        ?>
        <?
        $methods = MainHelper::getOrmMethods($entityCl::getEntity());
        ?>
        <h2><?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_PRM22')?></h2>
        <table>
            <tr>
                <th style="padding:5px;text-align:left;border-bottom:1px solid #87919c;border-right:1px solid #87919c;"><?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_PRM22_3')?></th>
                <th style="padding:5px;text-align:left;border-bottom:1px solid #87919c;border-right:1px solid #87919c;"><?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_PRM22_1')?></th>
                <th style="padding:5px;text-align:left;border-bottom:1px solid #87919c;"><?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_PRM22_2')?></th>
            </tr>
            <?foreach($methods as $code=>$name){?>
                <tr>
                    <td <?=$r_style?>>
                        <input type="checkbox" name="<?=$arField['NAME']?>[methods][<?=$code?>]" value="Y"<?=($valueField['methods'][$code]=='Y' ? ' checked="checked"' : '')?>>
                    </td>
                    <td <?=$r_style?>><?=$code?></td>
                    <td style="width:50%;padding:5px;border-bottom:1px solid #87919c;">
                        <?=$name?>
                    </td>
                </tr>
            <?}?>
        </table>
        <h2><?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_PRM11')?></h2>
        <table>
        <tr>
            <th style="padding:5px;text-align:left;border-bottom:1px solid #87919c;border-right:1px solid #87919c;"><?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_PRM11_1')?></th>
            <th style="padding:5px;text-align:left;border-bottom:1px solid #87919c;"><?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_PRM11_2')?></th>
        </tr>
        <?php
        if(is_string($entity) && $entity && class_exists($entity) && method_exists($entity, 'getEntity')){
            $fields = $entity::getMap();
            /* @var $field \Bitrix\Main\ORM\Fields\Field */
            foreach($fields as $field){
                $fieldCode = $arField['NAME'].'[fields]['.$field->getName().']';
                ?>
                <tr>
                    <td <?=$r_style;?>>
                        [<?=$field->getName()?>] - <?=$field->getTitle()?>
                    </td>
                    <td style="width:75%;padding:5px;border-bottom:1px solid #87919c;">
                        <b><?=get_class($field)?></b><br><br>
                        <?
                        $fieldVal = $this->getFieldValue($arField['NAME'])['fields'][$field->getName()];
                        $showDef = false;
                        $checkResult = $this->checkCorrectField($field);
                        if(!$checkResult->isSuccess()){
                            ?>
                            <p style="color:red;"><?=implode("<br>",$checkResult->getErrorMessages())?></p>
                            <?
                        }elseif($field->getParameter('primary')===true){
                            $showDef = true;
                            ?>
                            <?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_FIELDS_TYPE')?>:
                            <b>primary</b><br>
                            <input type="hidden" name="<?=$fieldCode?>[type]" value="primary">
                        <?}elseif($field instanceof Fields\StringField){
                            $showDef = true;
                            ?>
                            <?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_FIELDS_TYPE')?>:
                            <b>string</b><br>
                            <input type="hidden" name="<?=$fieldCode?>[type]" value="string">
                        <?}elseif($field instanceof Fields\IntegerField){
                            $showDef = true;
                            ?>
                            <?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_FIELDS_TYPE')?>:
                            <b>integer</b><br>
                            <input type="hidden" name="<?=$fieldCode?>[type]" value="integer">
                        <?}elseif($field instanceof Fields\FloatField){
                            $showDef = true;
                            ?>
                            <?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_FIELDS_TYPE')?>:
                            <b>float</b><br>
                            <input type="hidden" name="<?=$fieldCode?>[type]" value="float">
                        <?}elseif($field instanceof Fields\DateField){
                            $showDef = true;
                            ?>
                            <?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_FIELDS_TYPE')?>:
                            <b>date</b><br>
                            <input type="hidden" name="<?=$fieldCode?>[type]" value="date">
                        <?}elseif($field instanceof Fields\DateTimeField){
                            $showDef = true;
                            ?>
                            <?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_FIELDS_TYPE')?>:
                            <b>date</b><br>
                            <input type="hidden" name="<?=$fieldCode?>[type]" value="datetime">
                        <?}?>
                        <?if($showDef){?>
                            <?=$this->paramsFieldViewReadonly($field, $arField)?>
                            <?=$this->paramsFieldViewActive($field, $arField)?><br>
                            <?=$this->paramsFieldViewIsRequired($field, $arField)?>
                            <?=$this->paramsFieldViewIsSortable($field, $arField)?>
                            <br>
                            <input style="margin-top:5px;" type="text" name="<?=$fieldCode?>[title]" value="<?=(!isset($fieldVal['title']) ? $field->getTitle() : $fieldVal['title'])?>">
                        <?}?>
                    </td>
                </tr>
                <?php
            }
            //echo'<pre>';print_r($fields);echo'</pre>';
        }elseif(is_string($entity) && $entity){
            if(!class_exists($entity)){
                ?><p style="color:red;"><?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_ERR3')?> [<?=$entity?>]</p><?
            }else{
                ?><p style="color:red;"><?=Loc::getMessage('AWZ_BXORM_METHODS_EDIT_ERR4')?> [<?=$entity?>]</p><?
            }
        }
        ?></table><?
    }

    public static function getParams(): array
    {
        //print_r(\Awz\BxOrm\MethodsTable::getEntity()->getField("MODULES")->getTitle());
        //die();
        $modules = MainHelper::getModuleList();

        $arParams = array(
            "ENTITY" => "\\Awz\\BxOrm\\MethodsTable",
            "BUTTON_CONTEXTS"=>array('btn_list'=>false),
            "LIST_URL"=>'/bitrix/admin/awz_bxorm_methods_list.php',
            "TABS"=>array(
                "edit1" => array(
                    "NAME"=>Loc::getMessage('AWZ_BXORM_METHODS_EDIT_EDIT1'),
                    "FIELDS" => array(
                        "NAME",
                        "CODE",
                        "ENTITY",
                        "MODULES"=>[
                            "NAME"=>"MODULES",
                            "VALUES"=>$modules,
                            "TYPE"=>"SELECT",
                            "ADD_STR"=>'multiple="Y" size="10"',
                        ],
                        "ACTIVE",
                        "PARAMS"=>[
                            "TYPE"=>"CUSTOM",
                            "NAME"=>"PARAMS",
                            "FUNC_VIEW"=>"paramsFieldView"
                        ],
                    )
                )
            )
        );
        return $arParams;
    }
}