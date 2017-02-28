<?php

namespace Dal;

/**
 * Database classes generator
 */
class ModelGenerator {

    var $profile;

    function __construct($profile = 'default') {
        $this->profile = $profile;
    }

    function getTableClassName($tableName) {
        $parts = explode('_', $tableName);
        foreach($parts as $key => $value){
            $parts[$key] = ucfirst($value);
        }
        return join('', $parts);
    }

    function getClassName($tableName) {
        if (strlen($tableName) > 1 && $tableName[strlen($tableName) - 1] == 's') {
            $tableName = substr($tableName, 0, strlen($tableName) - 1);
        }
        $parts = explode('_', $tableName);
        foreach($parts as $key => $value){
            $parts[$key] = ucfirst($value);
        }
        return join('', $parts);
    }

    function namespaceToPath($namespace) {
        $path = explode('\\', $namespace);
        array_walk($path, function(&$a) {
            $a = strtolower($a);
        });
        return implode('/', $path);
    }

    function run() {
        $databaseConfig = \Core\Config::load('database');
        $config = $databaseConfig->{$this->profile};
        $tables = db($this->profile)->query("SHOW TABLES FROM `{$config->dbname}`")->fetchAllArray();

        foreach($tables as $tc){
            $tableName = $tc[0];
            echo "Working on {$tableName}\n";
            $tableInfo = db($this->profile)->query("SHOW FULL COLUMNS FROM {$tableName}")->fetchAllAssoc();
            $tableClassName = $this->getTableClassName($tableName);
            $className = $this->getClassName($tableName);
            $pk = [];
            $methods = [];
            $indexes = [];
            $generated = [];
            foreach($tableInfo as $tableField){
                if($tableField['Key'] == 'PRI'){
                    $pk[] = sprintf("'%s'", $tableField['Field']);
                }
                if ($tableField['Comment'] == 'uid') {
                    $generated[$tableField['Field']] = 'uid';
                } elseif ($tableField['Comment'] == 'uint') {
                    $generated[$tableField['Field']] = 'uint';
                }
            }

            if (isset($config->namespace)) {
                $namespace = $config->namespace;
                $namespacePath = explode('\\', $namespace);
                $namespacePath[0] = strtolower($namespacePath[0]);
                $namespacePath = implode('/', $namespacePath);
            } else {
                $namespace = null;
                $namespacePath = 'classes';
            }

            ob_start();
            require DAL . '/resources/dbgen/table-class.tpl';
            $tableClassContent = sprintf("<?php \n\n%s", ob_get_clean());
            $tableClassPath = ROOT . "/$namespacePath/Table/$tableClassName.php";
            $classPath = ROOT . "/$namespacePath/$className.php";
            if (!is_dir(dirname($tableClassPath))) {
                mkdir(dirname($tableClassPath), 0755, true);
            }
            file_put_contents($tableClassPath, $tableClassContent);
            if (!file_exists($classPath)) {
                ob_start();
                require DAL . '/resources/dbgen/class.tpl';
                $classContent = sprintf("<?php \n\n%s", ob_get_clean());
                if (!is_dir(dirname($classPath))) {
                    mkdir(dirname($classPath), 0755, true);
                }
                file_put_contents($classPath, $classContent);
            }
        }

        echo "Done\n";
    }

}