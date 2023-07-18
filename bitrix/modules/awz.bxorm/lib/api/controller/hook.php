<?php
namespace Awz\BxOrm\Api\Controller;

use Awz\BxOrm\Api\Scopes\Parameters;
use Awz\BxOrm\Api\Scopes\Scope;
use Awz\BxOrm\Api\Scopes\Controller;
use Awz\BxOrm\Api\Filters\CheckMethod;
use Awz\BxOrm\Api\Filters\AppAuth;
use Awz\BxOrm\HooksTable;
use Awz\BxOrm\MethodsTable;
use Awz\BxOrm\Helper;
use Bitrix\Main\Request;
use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Response;
use Bitrix\Main\Web\Json;

Loc::loadMessages(__FILE__);

class Hook extends Controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function configureActions()
    {
        $config = [
            'methods' => [
                'prefilters' => [
                    new AppAuth(
                        [], [],
                        Scope::createFromCode('user'),
                        []
                    )
                ]
            ],
            'batch' => [
                'prefilters' => [
                    new AppAuth(
                        [], [],
                        Scope::createFromCode('user'),
                        []
                    )
                ]
            ],
            'call' => [
                'prefilters' => [
                    //фильтр для проверки авторизации по app и key
                    new AppAuth(
                        [], [],
                        Scope::createFromCode('user'),
                        []
                    ),
                    //фильтр для проверки доступности метода (не проверяет авторизацию)
                    new CheckMethod(
                        [], [],
                        [
                            new Scope(
                                'method',
                                Scope::STATUS_OK,
                                new Parameters(['METHOD_ID'=>0])
                            ),
                            new Scope(
                                'method',
                                Scope::STATUS_ERR,
                                new Parameters(['METHOD_ID'=>0])
                            )
                        ],
                        ['user']
                    )
                ]
            ],
            /*'forward' => [
                'prefilters' => []
            ]*/
        ];

        return $config;
    }

    public function add(string $entityClass, array $fieldsParams = [], array $fields = []){
        $primaryKey = '';
        foreach($fieldsParams as $fieldKey=>$fieldValue){
            if($fieldValue['isActive'] === 'Y' && $fieldValue['type']==='primary'){
                $primaryKey = $fieldKey;
            }
        }
        if(!$primaryKey){
            $this->addError(new Error('primary key not active'));
            return null;
        }

        $activeFields = [];
        foreach($fieldsParams as $fieldKey=>$fieldValue){
            if($fieldValue['isActive'] === 'Y'){
                $activeFields[] = $fieldKey;
            }
        }
        $fieldsFin = [];
        foreach($fields as $k=>$v){
            if(in_array($k, $activeFields)){
                $fieldsFin[$k] = $v;
            }
        }
        $res = $entityClass::add($fieldsFin);
        if(!$res->isSuccess()){
            foreach($res->getErrors() as $err){
                $this->addError($err);
            }
            return null;
        }
        return [
            'item'=>[$primaryKey=>$res->getId()]
        ];
    }

    public function update(string $entityClass, array $fieldsParams = [], string $id, array $fields = []){
        $primaryKey = '';
        foreach($fieldsParams as $fieldKey=>$fieldValue){
            if($fieldValue['isActive'] === 'Y' && $fieldValue['type']==='primary'){
                $primaryKey = $fieldKey;
            }
        }
        if(!$primaryKey){
            $this->addError(new Error('primary key not active'));
            return null;
        }

        $activeFields = [];
        foreach($fieldsParams as $fieldKey=>$fieldValue){
            if($fieldValue['isActive'] === 'Y' && $fieldValue['isReadOnly'] !== 'Y'){
                $activeFields[] = $fieldKey;
            }
        }

        $fieldsFin = [];
        foreach($fields as $k=>$v){
            if(in_array($k, $activeFields)){
                $fieldsFin[$k] = $v;
            }
        }

        $res = $entityClass::update([$primaryKey=>$id], $fieldsFin);
        //print_r()
        if(!$res->isSuccess()){
            foreach($res->getErrors() as $err){
                $this->addError($err);
            }
            return null;
        }
        return [
            'item'=>[$primaryKey=>$id]
        ];
    }

    public function delete(string $entityClass, array $fieldsParams = [], string $id){
        $primaryKey = '';
        foreach($fieldsParams as $fieldKey=>$fieldValue){
            if($fieldValue['isActive'] === 'Y' && $fieldValue['type']==='primary'){
                $primaryKey = $fieldKey;
            }
        }
        if(!$primaryKey){
            $this->addError(new Error('primary key not active'));
            return null;
        }
        /* @var $entityClass \Bitrix\Main\ORM\Data\DataManager */
        $res = $entityClass::delete([$primaryKey=>$id]);
        if(!$res->isSuccess()){
            foreach($res->getErrors() as $err){
                $this->addError($err);
            }
            return null;
        }
        return [
            'item'=>[$primaryKey=>$id]
        ];
    }

    public function getList(string $entityClass, array $fieldsParams = [], array $params){

        $limit = 50;
        $offset = ceil($params['start']/$limit)*$limit;

        $activeFields = [];
        $sortableFields = [];
        $defSort = [];
        foreach($fieldsParams as $fieldKey=>$fieldValue){
            if($fieldValue['isActive'] === 'Y'){
                $activeFields[] = $fieldKey;
                if($fieldValue['type']==='primary'){
                    $defSort = [$fieldKey => 'asc'];
                }
                if($fieldValue['isSortable'] == 'Y'){
                    $sortableFields[] = $fieldKey;
                }
            }
        }

        if(!isset($params['select']) || empty($params['select'])){
            $query['select'] = $activeFields;
        }else{
            $query['select'] = [];
            foreach($params['select'] as $k){
                if(in_array($k, $activeFields)){
                    $query['select'][] = $k;
                }
            }
        }
        $query['order'] = $defSort;
        if(isset($params['order']) && is_array($params['order'])){
            $keyOrder = array_keys($params['order']);
            if(in_array($keyOrder[0], $sortableFields)){
                $query['order'] = $params['order'];
            }
        }
        if(isset($params['filter']) && is_array($params['filter'])){
            $query['filter'] = $params['filter'];
        }

        $query['limit'] = $limit;
        $query['offset'] = $offset;
        $query['count_total'] = true;
        $res = $entityClass::getList($query);
        return [
            'result'=> [
                'items'=>$res->fetchAll(),
            ],
            'total'=>$res->getCount()
        ];

    }

    public function getFields(string $entityClass, array $fieldsParams = []){
        $fieldsData = [];
        $fields = $entityClass::getMap();
        /* @var $field \Bitrix\Main\ORM\Fields\Field */
        foreach($fields as $field){
            //FIELD_PARAMS[fields][PARAMS][isActive]
            if(!isset($fieldsParams[$field->getName()]['isActive'])) continue;
            if($fieldsParams[$field->getName()]['isActive']!='Y') continue;
            /*
             * [type] => string
             * [isRequired] => 1
             * [isReadOnly] =>
             * [isImmutable] =>
             * [isMultiple] =>
             * [isDynamic] =>
             * [title] => Имя
             * */
            $noLink = false;
            if($fieldsParams[$field->getName()]['type'] === 'primary'){
                $fieldsParams[$field->getName()]['type'] = 'integer';
                if($field instanceof \Bitrix\Main\ORM\Fields\StringField){
                    $fieldsParams[$field->getName()]['type'] = 'string';
                }
                $noLink = true;
            }
            $fieldsData[$field->getName()] = [
                'type'=>$fieldsParams[$field->getName()]['type'],
                'isRequired'=>$fieldsParams[$field->getName()]['isRequired'] == 'Y' ? 1 : 0,
                'isReadOnly'=>$fieldsParams[$field->getName()]['isReadonly'] == 'Y' ? 1 : 0,
                'sort'=>$fieldsParams[$field->getName()]['isSortable'] == 'Y' ? $field->getName() : '',
                'title'=>$fieldsParams[$field->getName()]['title'],
                'noLink'=>1
            ];
            if($noLink){
                $fieldsData[$field->getName()]['noLink'] = 1;
            }
        }
        return [
            'result'=>$fieldsData
        ];
    }

    public function batchAction(int $app, array $cmd = [], bool $halt = false){

        $actionsResult = [];
        $actionsErrors = [];

        $startRequest = $this->getRequest();
        $requestValuesStart = $this->getRequest()->getValues();
        unset($requestValuesStart['cmd']);
        unset($requestValuesStart['action']);

        foreach($cmd as $key=>$row){
            $rowAr = explode("?", $row);
            parse_str($rowAr[1], $rowArQuery);
            $rowArQuery['method'] = $rowAr[0];
            $controller = new $this;
            $requestValues = $requestValuesStart;
            foreach($rowArQuery as $k=>$v){
                $requestValues[$k] = $v;
            }
            //$requestValues['key'] .= '1'; //тест кривого ключа
            $requestValues['action'] = 'awz:bxorm.api.hook.call';
            $controller->request = $startRequest;
            //TODO replace to request->addFilter \Bitrix\Main\Type\IRequestFilter
            $controller->request->set($requestValues);
            $result = $controller->run(
                'call',
                [new \Bitrix\Main\Type\ParameterDictionary($requestValues)]
            );
            $actionsResult[$key] = $result;
            if($controller->getErrors()){
                $actionsErrors[$key] = [];
                foreach($controller->getErrors() as $err){
                    $actionsErrors[$key][] = ['code'=>$err->getCode(), 'message'=>$err->getMessage()];
                }
            }
        }
        return [
            'result'=>$actionsResult,
            'result_error'=>$actionsErrors
        ];

    }

    public function callAction(int $app, string $method, array $order = [], array $select = [], array $filter = [], int $start = 0, string $id = "", array $fields = []){

        $METHOD_ID = $this->getScopeCollection()->getByCode('method')->getCustomData()->get('METHOD_ID');

        if(!$METHOD_ID){
            $this->addError(new Error('Параметры метода не найдены'));
            return null;
        }

        $methodsParams = MethodsTable::getRowById($METHOD_ID);
        if(!$methodsParams){
            $this->addError(new Error('Параметры метода не найдены'));
            return null;
        }

        $methodAr = explode('.',$method);
        if(is_array($methodAr) && isset($methodAr[1]) && isset($methodAr[1])){

            $methodName = $methodAr[1];
            $appName = $methodAr[0];

            //подключение модулей
            if(is_array($methodsParams['MODULES'])){
                foreach($methodsParams['MODULES'] as $module){
                    if(!Loader::includeModule($module)){
                        $this->addError(new Error($module.' - модуль не подключен'));
                        return null;
                    }
                }
            }

            $entityClass = $methodsParams['ENTITY'];
            if(!class_exists($entityClass)){
                $this->addError(new Error($appName.' - классы метода не доступны'));
                return null;
            }

            if($methodName === 'fields'){
                return $this->getFields($entityClass, $methodsParams['PARAMS']['fields']);
            }
            if($methodName === 'delete'){
                return $this->delete($entityClass, $methodsParams['PARAMS']['fields'], $id);
            }
            if($methodName === 'list'){
                return $this->getList($entityClass, $methodsParams['PARAMS']['fields'],
                    ['order'=>$order, 'select'=>$select, 'filter'=>$filter, 'start'=>$start]
                );
            }
            if($methodName === 'update'){
                return $this->update($entityClass, $methodsParams['PARAMS']['fields'], $id, $fields);
            }
            if($methodName === 'add'){
                return $this->add($entityClass, $methodsParams['PARAMS']['fields'], $fields);
            }
            $this->addError(new Error('Запрещенный формат именования метода '.$method));
            return null;


        }else{
            $this->addError(new Error('Запрещенный формат именования метода '.$method));
            return null;
        }

        print_r($method);die();
    }

    public function methodsAction(int $app){

        $methodNames = Helper::getOrmMethods(HooksTable::getEntity());

        $hookData = HooksTable::getRowById($app);
        $methods = [];
        $methodsList = [];
        if(!empty($hookData['METHODS'])){
            foreach($hookData['METHODS'] as $methodId){
                $methods[] = $methodId;
            }
        }

        if(!empty($methods)){
            $methodsParamsRes = MethodsTable::getList([
                'select'=>['*'],
                'filter'=>['=ID'=>$methods, '=ACTIVE'=>'Y']
            ]);
            while($data = $methodsParamsRes->fetch()){
                $methodsList[$data['CODE']] = $data['NAME'];
                if(!empty($data['PARAMS']['methods'])){
                    foreach($data['PARAMS']['methods'] as $code=>$active){
                        if($active==='Y'){
                            $addName = $data['NAME'];
                            if(isset($methodNames[$code])){
                                $addName .= ' - '.$methodNames[$code];
                            }else{
                                $addName .= ' - '.$code;
                            }
                            $methodsList[$data['CODE'].'.'.$code] = $addName;
                        }
                    }
                }

            }
        }

        return [
            'methods'=> $methodsList
        ];
    }
}