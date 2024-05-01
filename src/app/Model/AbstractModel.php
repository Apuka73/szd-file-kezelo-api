<?php

namespace Model;

use Phalcon\Mvc\Model\ResultsetInterface;

class AbstractModel extends \Phalcon\Mvc\Model
{
    const RECORD_STATUS_ACTIVE = 1;
    const RECORD_STATUS_INACTIVE = 0;
    const RECORD_STATUS_DELETED = -1;

    protected static $cache;
    protected static $useStatusFilter = true;

    protected $id;
    protected $record_status;
    protected $created_at;

    public function onConstruct()
    {
        $this->record_status = self::RECORD_STATUS_ACTIVE;
        $this->created_at = date('Y-m-d H:i:s');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRecordStatus()
    {
        return $this->record_status;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public static function getById($id):?static
    {
        return static::findFirst([
            'id = :id:',
            'bind' => [
                'id' => $id
            ]
        ]);
    }

    public function delete():bool
    {
        $this->record_status = static::RECORD_STATUS_DELETED;
        return $this->save();
    }

    protected static function addConditionToFind($condition, &$findParameters)
    {
        if (!is_array($findParameters)) {
            $findParameters = array($findParameters);
        }

        if(isset($findParameters['conditions'])){
            $findParameters['conditions'] .= ' ' . $condition . ' ';
            return;
        }

        if (empty($findParameters[0])) {
            $findParameters[0] = ' 1 ';
        }

        $findParameters[0] .= ' ' . $condition . ' ';
    }

    public static function find($parameters = null): ResultsetInterface
    {
        $key = md5(static::class . 'find' . serialize($parameters));

        if (static::$useStatusFilter) {
            static::addConditionToFind(static::notActiveCondition(), $parameters);
        }

        if (empty(self::$cache[$key])) {
            self::$cache[$key] = parent::find($parameters);
        }

        return self::$cache[$key];
    }

    public static function findFirst($parameters = null):?static
    {
        $key = md5(static::class . 'findfirst' . serialize($parameters));

        if (static::$useStatusFilter) {
            static::addConditionToFind(static::notActiveCondition(), $parameters);
        }

        if (empty(self::$cache[$key])) {
            self::$cache[$key] = parent::findFirst($parameters);
        }

        return self::$cache[$key];
    }

    public static function count($parameters = null):int
    {
        $key = md5(static::class . 'count' . serialize($parameters));

        if (static::$useStatusFilter) {
            static::addConditionToFind(static::notActiveCondition(), $parameters);
        }

        if (empty(self::$cache[$key])) {
            self::$cache[$key] = parent::count($parameters);
        }

        return self::$cache[$key];
    }

    protected static function notActiveCondition()
    {
        return 'AND record_status = ' . static::RECORD_STATUS_ACTIVE;
    }
}
