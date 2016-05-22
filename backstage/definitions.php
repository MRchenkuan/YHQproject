<?php
/**
 * Created by PhpStorm.
 * User: chenkuan
 * Date: 16/3/15
 * Time: 下午3:13
 */

// 定义网站根目录
define('DB_TYPE','MYSQL');
define('APP_DIR',__DIR__);
define('DATA_TABLE_DIR',$_SERVER['DOCUMENT_ROOT']."/backstage"."/DO/Data/");
define('DATABASE_DIR',$_SERVER['DOCUMENT_ROOT']."/backstage"."/DO/");
define('DATABASE_DAO_DIR',$_SERVER['DOCUMENT_ROOT']."/backstage/DO/DAO/");
//require_once($_SERVER['DOCUMENT_ROOT'].'/tools/Config.class.php');
define('WIDGETS_DIR',$_SERVER['DOCUMENT_ROOT']."/backstage"."/widgets/");
define('KODBC_PATH',$_SERVER['DOCUMENT_ROOT']."/backstage"."/DO/Kodbc.class.php");
define('STATIC_DIR',$_SERVER['DOCUMENT_ROOT']."/backstage"."/PUBLIC/");
define('DUSTBIN_DIR',$_SERVER['DOCUMENT_ROOT']."/backstage"."/dashbin/");
define('NEWS_FILE_DIR',$_SERVER['DOCUMENT_ROOT']."/backstage"."/news/");
define('IMAGE_BED_DIR',$_SERVER['DOCUMENT_ROOT']."/backstage"."/image/");