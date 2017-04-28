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
    public function generate($namespace, $path) {
        $absolutePath = realpath($path);
        if (!$absolutePath) {
            throw new \Exception('Folder not found:'.$path);
        }
        $namespace = trim($namespace,'\\');
        $absolutePath = rtrim($absolutePath,'/');
        @exec('rm -fR ' . $absolutePath . '/Base');
        $generator = $this->_getGenerator($namespace, $absolutePath);
        $files = $generator->generate();
        /** @var \ActiveRecord\gii\CodeFile $file */
        foreach ($files as $file) {
            $success = $file->save();
            if ($success !== true) {
                printf("File %s: %s\n",$file->path, $success);
            } else {
                printf("File %s: ok\n",$file->path);
            }
        }
        @exec(sprintf('git add %s',$absolutePath.'/'));
    }

    /**
     * @param GeneratorDatabase $database
     * @param $namespace
     * @param $path
     * @return gii\generators\model\Generator
     */
    protected function _getGenerator($namespace, $path) {
        $generator = new \ActiveRecord\gii\generators\model\Generator();
        $generator->setDbConnection($this->_db);
        $generator->ns = $namespace;
        $generator->path = $path;
        $generator->sub  = 'Base';
        foreach ($this->_databases as $currentDatabase) {
            foreach ($currentDatabase->getTables() as $tableName) {
                $relationTableName = $currentDatabase->getDatabase().'.'.$tableName;
                $generator->classNames[$relationTableName] = $this->_tableToClass($tableName);
            }
        }
        return $generator;
    }

    protected function _tableToClass($table) {
        return implode('', array_map('ucfirst', explode('_', $table)));
    }

    protected function _getOriginFile($namespace, $class, $extends) {
        return '<?'."php\n\nnamespace $namespace;\n\n".
            "class $class extends \\$namespace\\Base\\$extends {\n\n}";
    }

}


