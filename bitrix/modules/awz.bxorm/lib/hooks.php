<?php

namespace Awz\BxOrm;

use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\Result;
use Bitrix\Main\Type\DateTime;

Loc::loadMessages(__FILE__);

class HooksTable extends Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'b_awz_bxorm_hooks';
        /*
        CREATE TABLE IF NOT EXISTS `b_awz_bxorm_hooks` (
        `ID` int(18) NOT NULL AUTO_INCREMENT,
        `NAME` varchar(65) NOT NULL,
        `TOKEN` varchar(18) NOT NULL,
        `ACTIVE` varchar(1) NOT NULL,
        `METHODS` varchar(6255) NOT NULL,
        PRIMARY KEY (`ID`)
        ) AUTO_INCREMENT=1;
        */
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                    'primary' => true,
                    'autocomplete' => true,
                    'title'=>Loc::getMessage('AWZ_BXORM_HOOKS_ENTITY_FIELD_ID')
                )
            ),
            new Entity\StringField('NAME', array(
                    'required' => true,
                    'title'=>Loc::getMessage('AWZ_BXORM_HOOKS_ENTITY_FIELD_NAME')
                )
            ),
            new Entity\StringField('TOKEN', array(
                    'required' => true,
                    'title'=>Loc::getMessage('AWZ_BXORM_HOOKS_ENTITY_FIELD_TOKEN')
                )
            ),
            new Entity\BooleanField('ACTIVE', array(
                    'required' => true,
                    'title'=>Loc::getMessage('AWZ_BXORM_HOOKS_ENTITY_FIELD_ACTIVE'),
                    'values' => array('N', 'Y'),
                    'default_value' => 'Y'
                )
            ),
            new Entity\StringField('METHODS', array(
                    'required' => true,
                    'serialized' => true,
                    'title'=>Loc::getMessage('AWZ_BXORM_HOOKS_ENTITY_FIELD_METHODS')
                )
            )
        );
    }

}