<?php

namespace ActiveRecord;

class Generator {

    /** @var  GeneratorDatabase[] */
    protected $_databases;

    /** @var  \ActiveRecord\db\Connection */
    protected $_db;

    public function __construct(\ActiveRecord\db\Connection $db) {
        $this->_db = $db;
    }

    public function addDatabase(GeneratorDatabase $database) {
        $this->_databases[] = $database;
    }

    /**
     * @param string $path Example: HOME.'ar'
     */
    public function generate($namepsace, $path) {
        $namespace = trim($namepsace,'\\');
        $path = rtrim($path,'/');
        $files = [];
        @exec('rm -fR ' . $path . '/_base');
        foreach ($this->_databases as $database) {
            foreach ($database->getTables() as $tableName) {
                $class = $this->_tableToClass($tableName);
                $generator = $this->_getGenerator($database, $namespace);
                $generator->tableName = $database->getDatabase().'.'.$tableName;
                $generator->modelClass = 'C' . $class;
                $generator->queryClass = 'C' . $class . 'Query';
                $generator->modelNs = $namespace . '\\' . $class;
                $files = array_merge($files, $generator->generate($path));
                $originPath = $path . '/' . $class . '.php';
                if (!file_exists($originPath)) {
                    $file = new \ActiveRecord\gii\CodeFile($originPath, $this->_getOriginFile($namespace, $class, 'C' . $class));
                    $files[] = $file;
                }
                $originQueryPath = $path . '/' . $class . 'Query.php';
                if (!file_exists($originQueryPath)) {
                    $file = new \ActiveRecord\gii\CodeFile($originQueryPath, $this->_getOriginFile($namespace, $class . 'Query', 'C' . $class . 'Query'));
                    $files[] = $file;
                }
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

    /**
     * @param GeneratorDatabase $database
     * @return gii\generators\model\Generator
     */
    protected function _getGenerator(GeneratorDatabase $database, $namespace) {
        $generator = new \ActiveRecord\gii\generators\model\Generator();
        $generator->setDbConnection($this->_db);
        $generator->db = $database->getDatabase();
        $generator->ns = $namespace . '\\_base';
        $generator->queryNs = $namespace . '\\_base';
        foreach ($this->_databases as $currentDatabase) {
            foreach ($currentDatabase->getTables() as $tableName) {
                $relationTableName = $currentDatabase->getDatabase().'.'.$tableName;
                $generator->classNames[$relationTableName] = '\\' . $namespace . '\\' . $this->_tableToClass($tableName);
            }
        }
        return $generator;
    }

    protected function _tableToClass($table) {
        return implode('', array_map('ucfirst', explode('_', $table)));
    }

    protected function _getOriginFile($namespace, $class, $extends) {
        return '<?'."php\n\nnamespace $namespace;\n\n".
            "class $class extends \\$namespace\\_base\\$extends {\n\n}";
    }

}


