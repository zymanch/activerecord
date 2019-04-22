<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace ActiveRecord\db;

class Collection implements \IteratorAggregate {

    /** @var ActiveRecord[]  */
    protected $_children = [];

    public function __construct(array $children = []) {
        $this->_children = $children;
    }

    public function add(ActiveRecord $record) {
        $this->_children[] = $record;
    }

    public function count() {
        return count($this->_children);
    }

    public function getIterator() {
        return new \ArrayIterator($this->_children);
    }

    public function validate() {
        $result = true;
        foreach ($this->_children as $child) {
            $result = $result && $child->validate();
        }
        return $result;
    }

    public function save($runValidation = true, $columns = []) {
        if (!$this->_children) {
            return true;
        }
        if ($runValidation && !$this->validate()) {
            return false;
        }
        return ActiveRecord::getDb()->transaction(function() use($columns) {
            $result = true;
            foreach ($this->_children as $child) {
                $result = $result && $child->save(false, $columns);
            }
            return $result;
        });
    }

    public function batchSave(array $columns = []) {
        if (!$this->_children) {
            return true;
        }


        $firstChild = reset($this->_children);
        $tableName = $firstChild::tableName();
        $columns = $columns ? $columns : $firstChild->attributes();
        $diff = array_diff($columns, $firstChild->attributes());
        if ($diff) {
            throw new Exception('Unknown columns: '.implode(', ',$diff));
        }


        $insertRows = [];
        foreach ($this->_children as $child) {
            if ($child->primaryKey) {
                throw new Exception('Batch save available only for new models');
            }
            if ($child::tableName() !== $tableName) {
                throw new Exception('Batch insert can save only to one table '.$tableName.', but '.$child::tableName().' added');
            }
            $insertRows[] = $child->getAttributes($columns);
        }


        return ActiveRecord::getDb()->createCommand()->batchInsert(
            $tableName,
            $columns,
            $insertRows
        )->execute();
    }
}
