<?php
error_reporting(0);
require_once(BACKSTAGE_DIR.'/DO/DBC.class.php');
/**
 * Created by PhpStorm.
 * User: chenkuan
 * Date: 16/3/16
 * Time: 下午12:16
 */

class photosDAO extends DBC{
    public function __construct(){
        return parent:: __construct(get_class($this));
    }

}