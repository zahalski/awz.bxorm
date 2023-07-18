<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
Loc::loadMessages(__FILE__);
global $APPLICATION;
$module_id = "awz.bxorm";
$MODULE_RIGHT = $APPLICATION->GetGroupRight($module_id);
$zr = "";
if (! ($MODULE_RIGHT >= "R"))
$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));

$APPLICATION->SetTitle(Loc::getMessage('AWZ_BXORM_OPT_TITLE'));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

Loader::includeModule($module_id);

if ($_SERVER["REQUEST_METHOD"] == "POST" && $MODULE_RIGHT == "W" && strlen($_REQUEST["Update"]) > 0 && check_bitrix_sessid())
{
    Option::set($module_id, "IBLOCK_ID", intval($_REQUEST["IBLOCK_ID"]));
}

$aTabs = array();

$aTabs[] = array(
    "DIV" => "edit1",
    "TAB" => Loc::getMessage('AWZ_BXORM_OPT_SECT1'),
    "ICON" => "vote_settings",
    "TITLE" => Loc::getMessage('AWZ_BXORM_OPT_SECT1')
);

$aTabs[] = array(
    "DIV" => "edit3",
    "TAB" => Loc::getMessage('AWZ_BXORM_OPT_SECT3'),
    "ICON" => "vote_settings",
    "TITLE" => Loc::getMessage('AWZ_BXORM_OPT_SECT3')
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>
    <style>.adm-workarea option:checked {background-color: rgb(206, 206, 206);}</style>
    <form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($module_id)?>&lang=<?=LANGUAGE_ID?>&mid_menu=1" id="FORMACTION">

        <?
        $tabControl->BeginNextTab();
        ?>
        <?if(Loader::includeModule('iblock')){?>
            <tr>
                <td><?=Loc::getMessage('AWZ_BXORM_OPT_IBLOCK')?></td>
                <td>
                    <?$val = Option::get($module_id, "IBLOCK_ID", "10", "");?>
                    <?
                    $els = \Bitrix\Iblock\IblockTable::getList();
                ?>
                    <select name="IBLOCK_ID">
                        <?while($iblock = $els->fetch()){
                            ?>
                            <option value="<?=$iblock['ID']?>"<?=($val == $iblock['ID'] ? 'selected="selected"' : '')?>>[<?=$iblock['ID']?>][<?=$iblock['IBLOCK_TYPE_ID']?>][<?=$iblock['LID']?>] - <?=$iblock['NAME']?></option>
                        <?}?>
                    </select>
                </td>
            </tr>
        <?}?>

        <?
        $tabControl->BeginNextTab();
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
        ?>

        <?
        $tabControl->Buttons();
        ?>
        <input <?if ($MODULE_RIGHT<"W") echo "disabled" ?> type="submit" class="adm-btn-green" name="Update" value="<?=Loc::getMessage('AWZ_BXORM_OPT_L_BTN_SAVE')?>" />
        <input type="hidden" name="Update" value="Y" />
        <?$tabControl->End();?>
    </form>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");