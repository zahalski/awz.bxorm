<?php
namespace Awz\BxOrm\Api;

use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\Result;
use Bitrix\Main\Type\Dictionary;

final class BinderResult extends Result implements Errorable {

    protected Dictionary $parameters;

    public function __construct(array $params = [])
    {
        parent::__construct();
        $this->parameters = new Dictionary($params);
    }

    /**
     * app id
     *
     * @return int
     */
    public function getApp(): int
    {
        return $this->getParameters()->get('app');
    }

    /**
     * method name
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->getParameters()->get('method');
    }

    /**
     * element id
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->getParameters()->get('id');
    }

    /**
     * order [key=>'desc|asc', key2=>'desc|asc']
     *
     * @return array
     */
    public function getOrder(): array
    {
        return $this->getParameters()->get('order');
    }

    /**
     * selected fields
     *
     * @return array
     */
    public function getSelect(): array
    {
        return $this->getParameters()->get('select');
    }

    /**
     * filters
     *
     * @return array
     */
    public function getFilter(): array
    {
        return $this->getParameters()->get('filter');
    }

    /**
     * offset
     *
     * @return int
     */
    public function getStart(): int
    {
        return $this->getParameters()->get('start');
    }

    /**
     * fields values for update element
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->getParameters()->get('fields');
    }

    /**
     * additional params or custom api
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->getParameters()->get('params');
    }

    /**
     * @param int $app
     * @return $this
     */
    public function setApp(int $app = 0): BinderResult
    {
        $this->getParameters()->set('app', $app);
        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method = ''): BinderResult
    {
        $this->getParameters()->set('method', $method);
        return $this;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId(string $id = ''): BinderResult
    {
        $this->getParameters()->set('id', $id);
        return $this;
    }

    /**
     * @param array $order
     * @return $this
     */
    public function setOrder(array $order = []): BinderResult
    {
        $this->getParameters()->set('order', $order);
        return $this;
    }

    /**
     * @param array $select
     * @return $this
     */
    public function setSelect(array $select = []): BinderResult
    {
        $this->getParameters()->set('select', $select);
        return $this;
    }

    /**
     * @param array $filter
     * @return $this
     */
    public function setFilter(array $filter = []): BinderResult
    {
        $this->getParameters()->set('filter', $filter);
        return $this;
    }

    /**
     * @param int $start
     * @return $this
     */
    public function setStart(int $start = 0): BinderResult
    {
        $this->getParameters()->set('start', $start);
        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields = []): BinderResult
    {
        $this->getParameters()->set('fields', $fields);
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setParams(array $params = []): BinderResult
    {
        $this->getParameters()->set('params', $params);
        return $this;
    }

    /**
     * @return Dictionary
     */
    protected function getParameters(): Dictionary
    {
        return $this->parameters;
    }

    /**
     * Getting once error with the necessary code.
     * @param int|string $code Code of error.
     * @return Error|null
     */
    public function getErrorByCode($code)
    {
        return $this->getErrorCollection()->getErrorByCode($code);
    }
}