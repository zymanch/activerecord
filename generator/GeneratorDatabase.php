<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 27.04.2017
 * Time: 12:51
 */
namespace ActiveRecord\generator;

class GeneratorDatabase {

    protected $_database;
    protected $_tables = [];

    public function __construct($database) {
        $this->_database = $database;
    }

    public function addTable($tableName) {
        $this->_tables[] = $tableName;
    }

    public function getDatabase() {
        return $this->_database;
    }

    public function getTables() {
        return $this->_tables;
    }

}