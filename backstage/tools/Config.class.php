<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/3/13
 * Time: 下午3:54
 */
define('CONFIG_INI_DIR',$_SERVER['DOCUMENT_ROOT']."/backstage/config.ini");
class Config
{
    /**
     * @return bool
     * @param override
     */
    public static function get(){
        $args = func_get_args();
        // 根据key取
        if(count($args)==1){
            $ini = parse_ini_file(CONFIG_INI_DIR,true)['PROPERTIES'];
            return trim($ini[$args[0]]);
        }

        // 根据section key取
        if(count($args)==2){
            $ini = parse_ini_file(CONFIG_INI_DIR,true)[$args[0]];
            return trim($ini[$args[1]]);
        }

        return false;
    }

    public static function getSection($section){
        return parse_ini_file(CONFIG_INI_DIR,true)[$section];
    }
}

