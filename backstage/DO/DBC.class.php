<?php
//error_reporting(0);

require_once($_SERVER['DOCUMENT_ROOT'] . '/backstage/definitions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/backstage/TOOLS/Config.class.php');


class DBC{
    private $DAO,$SQL,$fuc;
    public function __construct($DAONAME){
        $DB = Config::getSection("DB");
        $TYPE = $DB['TYPE'];
        $HOST = $DB['HOST'];
        $NAME = $DB['NAME'];
        $USERNAME = $DB['USERNAME'];
        $PASSWORD = $DB['PASSWORD'];
        $PORT = $DB['PORT'];
        $dsn = $TYPE.":host=".$HOST.";port=".$PORT.";dbname=".$NAME;

        $this->pdo = new PDO($dsn, $USERNAME, $PASSWORD);
        $this->DAO = simplexml_load_file(DATABASE_DAO_DIR . $DAONAME.'.xml');
    }

    public function getResult($id,$args=array()){
        $sql = trim($this->DAO->xpath('/mapper/*[@id=\'' . $id . '\']/text()')[0]);
        // 当参数存在
        // 遍历替换所有占位符
        // 可能需要先进行占位符转义
        foreach($args as $argkey=>$argval){
            $sql = str_replace('#{'.$argkey.'}',$argval,$sql);
        }
        $type = ($this->DAO->xpath('/mapper/*[@id=\'' . $id . '\']/text()')[0]->getName());
        $this->pdo->query('set names utf8;');
        switch(strtolower($type)){
            case 'select':return $this->pdo->query($sql); break;
            case 'insert':return $this->pdo->exec($sql); break;
            case 'delete':return $this->pdo->exec($sql); break;
            case 'update':return $this->pdo->exec($sql); break;
            default :return $this->pdo->query($sql);break;
        }
    }
}
