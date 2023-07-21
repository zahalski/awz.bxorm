<?php

namespace Awz\BxOrm;

use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\Result;
use Bitrix\Main\Type\DateTime;

Loc::loadMessages(__FILE__);

class MethodsTable extends Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'b_awz_bxorm_methods';
        /*
        CREATE TABLE IF NOT EXISTS `b_awz_bxorm_methods` (
        `ID` int(18) NOT NULL AUTO_INCREMENT,
        `NAME` varchar(65) NOT NULL,
        `CODE` varchar(65) NOT NULL,
        `ENTITY` varchar(255) NOT NULL,
        `ACTIVE` varchar(1) NOT NULL,
        `PARAMS` varchar(6255) NOT NULL,
        `MODULES` varchar(1250) NOT NULL,
        PRIMARY KEY (`ID`)
        ) AUTO_INCREMENT=1;
        */
        //ALTER TABLE `b_awz_bxorm_methods` ADD `MODULES` varchar(1250) NOT NULL
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                    'primary' => true,
                    'autocomplete' => true,
                    'title'=>Loc::getMessage('AWZ_BXORM_METHODS_ENTITY_FIELD_ID')
                )
            ),
            new Entity\StringField('NAME', array(
                    'required' => true,
                    'title'=>Loc::getMessage('AWZ_BXORM_METHODS_ENTITY_FIELD_NAME')
                )
            ),
            new Entity\StringField('CODE', array(
                    'required' => true,
                    'title'=>Loc::getMessage('AWZ_BXORM_METHODS_ENTITY_FIELD_CODE')
                )
            ),
            new Entity\StringField('ENTITY', array(
                    'required' => true,
                    'title'=>Loc::getMessage('AWZ_BXORM_METHODS_ENTITY_FIELD_ENTITY')
                )
            ),
            new Entity\StringField('MODULES', array(
                    'required' => true,
                    'serialized' => true,
                    'title'=>Loc::getMessage('AWZ_BXORM_METHODS_ENTITY_FIELD_MODULES')
                )
            ),
            new Entity\BooleanField('ACTIVE', array(
                    'required' => true,
                    'title'=>Loc::getMessage('AWZ_BXORM_METHODS_ENTITY_FIELD_ACTIVE'),
                    'values' => array('N', 'Y'),
                    'default_value' => 'Y',
                )
            ),
            new Entity\StringField('PARAMS', array(
                    'required' => true,
                    //'serialized' => true,
                    'title'=>Loc::getMessage('AWZ_BXORM_METHODS_ENTITY_FIELD_PARAMS'),
                    'save_data_modification' => function(){
                        return [
                            function ($value) {
                                return serialize($value);
                            }
                        ];
                    },
                    'fetch_data_modification' => function(){
                        return [
                            function ($value) {
                                return unserialize($value, ["allowed_classes" => false]);
                            }
                        ];
                    },
                )
            )
        );
    }

}