<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
Loc::loadMessages(__FILE__);

global $APPLICATION;
$POST_RIGHT = $APPLICATION->GetGroupRight("awz.bxorm");
if ($POST_RIGHT == "D") return;

if(Loader::includeModule('awz.bxorm')){
    $aMenu[] = array(
        "parent_menu" => "global_menu_settings",
        "section" => "awz_bxorm",
        "sort" => 100,
        "module_id" => "awz.bxorm",
        "text" => Loc::getMessage('AWZ_BXORM_ADMIN_MENU_TITLE'),
        "title" => Loc::getMessage('AWZ_BXORM_ADMIN_MENU_TITLE'),
        "items_id" => "awz_bxapi",
        "items" => array(
            array(
                "text" => Loc::getMessage('AWZ_BXORM_ADMIN_MENU_HOOK'),
                "url" => "awz_bxorm_hooks_list.php?lang=".LANGUAGE_ID,
                "more_url" => Array("awz_bxorm_hooks_edit.php?lang=".LANGUAGE_ID),
                "title" => Loc::getMessage('AWZ_BXORM_ADMIN_MENU_HOOK'),
                "sort" => 100,
            ),
            array(
                "text" => Loc::getMessage('AWZ_BXORM_ADMIN_MENU_METHODS'),
                "url" => "awz_bxorm_methods_list.php?lang=".LANGUAGE_ID,
                "more_url" => Array("awz_bxorm_methods_edit.php?lang=".LANGUAGE_ID),
                "title" => Loc::getMessage('AWZ_BXORM_ADMIN_MENU_METHODS'),
                "sort" => 110,
            )
        ),
    );
    return $aMenu;
}