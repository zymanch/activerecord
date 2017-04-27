<?php

namespace ActiveRecord;

class Generator {

    protected $_database;

    /** @var  \ActiveRecord\db\Connection */
    protected $_db;

    public function __construct($database, \ActiveRecord\db\Connection $db) {
        $this->_database = $database;
        $this->_db = $db;
    }

    /**
     * @param string $path Example: HOME.'ar'
     */
    public function generate($namepsace, $path) {
        $namespace = trim($namepsace,'\\');
        $path = rtrim($path,'/');
        $files = [];
        $tables = $this->_getTables();
        @exec('rm -fR '.$path.'/_base');
        foreach ($tables as $table => $class) {
            $generator = new \ActiveRecord\gii\generators\model\Generator();
            $generator->setDbConnection($this->_db);
            $generator->db = $this->_database;
            $generator->tableName = $table;
            $generator->modelClass = 'C'.$class;
            $generator->queryClass = 'C'.$class.'Query';
            $generator->modelNs = $namespace.'\\'.$class;
            $generator->ns = $namespace.'\\_base';
            $generator->queryNs = $namespace.'\\_base';
            foreach ($tables as $relationTableName => $relationClass) {
                $generator->classNames[$relationTableName] = '\\'.$namespace.'\\'.$relationClass;
            }
            $files = array_merge($files, $generator->generate($path));
            $originPath = $path.'/'.$class.'.php';
            if (!file_exists($originPath)) {
                $file = new \ActiveRecord\gii\CodeFile($originPath, $this->_getOriginFile($namespace,$class,'C'.$class));
                $files[] = $file;
            }
            $originQueryPath = $path.'/'.$class.'Query.php';
            if (!file_exists($originQueryPath)) {
                $file = new \ActiveRecord\gii\CodeFile($originQueryPath, $this->_getOriginFile($namespace, $class.'Query','C'.$class.'Query'));
                $files[] = $file;
            }
        }
        /** @var \ActiveRecord\gii\CodeFile $file */
        foreach ($files as $file) {
            $success = $file->save();
            if ($success !== true) {
                printf("File %s: %s\n",$file->path, $success);
            } else {
                printf("File %s: ok\n",$file->path);
            }
        }
        @exec(sprintf('git add %s',$path.'/'));
    }

    protected function _getTables() {
        $tables = $this->_db->createCommand('show tables from '.$this->_database)->queryColumn();
        $result = [];
        foreach ($tables as $table) {
            if (!in_array($table,['migration'])) {
                $result[$table] = implode('', array_map('ucfirst', explode('_', $table)));
            }
        }
        return $result;
    }

    protected function _getOriginFile($namespace, $class, $extends) {
        return '<?'."php\n\nnamespace $namespace;\n\n".
            "class $class extends \\$namespace\\_base\\$extends {\n\n}";
    }

}


