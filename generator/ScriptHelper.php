<?php
namespace ActiveRecord\generator;

use ActiveRecord\db\Connection;

class ScriptHelper {

    /**
     * @param $tables Example: shared:website,rest_query,script_log;geoip:geo_zone
     */
    public function generate($tables) {
        $tables = explode(';',$tables);
        /** @var Connection $db */
        $db = \ActiveRecord\db\Query::getDb();
        $generator = new Generator($db);
        foreach ($tables as $databaseAndTables) {
            $databaseAndTables = explode(':',$databaseAndTables,2);
            $database = $databaseAndTables[0];
            if (isset($databaseAndTables[1])) {
                $tables = array_filter(explode(',', $databaseAndTables[1]));
            } else {
                $tables = $this->_getTables($db, $database);
            }
            $database = new GeneratorDatabase($database);
            foreach ($tables as $table) {
                $database->addTable($table);
            }
            $generator->addDatabase($database);
        }
        $generator->generate('Model',__DIR__.'/../src/Model');
    }

    protected function _getTables(Connection $db, $database) {
        return $db->createCommand('show tables from '.$database)->queryColumn();
    }

}