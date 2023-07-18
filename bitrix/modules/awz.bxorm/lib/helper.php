<?php

namespace Awz\BxOrm;

use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\File;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Security\Random;

Loc::loadMessages(__FILE__);

class Helper {

    public static function getMethodsList(){
        static $methodsList = [];
        if(empty($methodsList)){
            $r = MethodsTable::getList([
                'select'=>['ID','NAME','CODE','PARAMS'],
                'filter'=>['=ACTIVE'=>'Y']
            ]);
            while($data = $r->fetch()){
                $active = [];
                if(isset($data['PARAMS']['methods']) && is_array($data['PARAMS']['methods'])){
                    foreach($data['PARAMS']['methods'] as $code=>$isActive){
                        if($isActive === 'Y'){
                            $active[] = $code;
                        }
                    }
                }
                $methodsList[$data['ID']] = $data['CODE'].'.('.implode('|',$active).') - '.$data['NAME'];
            }
        }
        return $methodsList;
    }

    public static function getModuleList(){
        static $moduleList = [];
        if(empty($moduleList)){
            $moduleList["main"] = "main";
            $moduleDir = Application::getInstance()->getContext()->getServer()->getDocumentRoot().'/bitrix/modules/';
            foreach(glob($moduleDir.'*/') as $dir){
                $dirOb = new Directory($dir);
                if($dirOb->isDirectory()){
                    //print_r($dirOb->getPath());die();
                    $versionFile = new File($dirOb->getPath().'/install/version.php');
                    if($versionFile->isExists()){
                        $moduleId = str_replace($moduleDir, '', $dirOb->getPath());
                        $moduleList[$moduleId] = $moduleId;
                    }
                }
            }
        }
        return $moduleList;
    }

    public static function getOrmMethods(\Bitrix\Main\ORM\Entity $entity){
        $codes = [
            'list','update','delete','add','fields',/*'batch',*/'get'
        ];
        $methods = [];
        foreach($codes as $code){
            $lang = Loc::getMessage('AWZ_BXORM_HELPER_METHOD_'.mb_strtoupper($code));
            $methods[$code] = $lang ?? $code;
        }
        return $methods;
    }

    public static function generateToken(): string
    {
        $token = Random::getStringByAlphabet(
            18,
            Random::ALPHABET_NUM|Random::ALPHABET_ALPHALOWER|Random::ALPHABET_ALPHAUPPER,
            true
        );
        return $token;
    }

    public static function checkServiceKey(int $appId, string $key): bool
    {
        if(!$key) return false;
        $r = HooksTable::getRowById($appId);
        if(!$r) return false;
        if($r['ACTIVE'] && $r['ACTIVE']!='Y') return false;
        if($r['TOKEN'] && ($r['TOKEN']===$key)) {
            return true;
        }
        return false;
    }

}