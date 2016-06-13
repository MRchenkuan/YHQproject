<?php
/**
 * Created by PhpStorm.
 * User: chenkuan
 * Date: 16/3/15
 * Time: 下午3:13
 */
error_reporting(0);
// 定义网站根目录
if(!defined(DB_TYPE))define('DB_TYPE','MYSQL');
if(!defined(APP_DIR))define('APP_DIR',__DIR__."/../");
if(!defined(BACKSTAGE_DIR))define('BACKSTAGE_DIR',__DIR__ );
// 数据库配置文件
if(!defined(CONFIG_INI_DIR))define('CONFIG_INI_DIR',BACKSTAGE_DIR."/config.ini");
if(!defined(DATA_TABLE_DIR))define('DATA_TABLE_DIR',BACKSTAGE_DIR."/DO/Data/");
if(!defined(DATABASE_DIR))define('DATABASE_DIR',BACKSTAGE_DIR."/DO/");
if(!defined(DATABASE_DAO_DIR))define('DATABASE_DAO_DIR',BACKSTAGE_DIR."/DO/DAO/");
if(!defined(WIDGETS_DIR))define('WIDGETS_DIR',BACKSTAGE_DIR."/widgets/");
if(!defined(KODBC_PATH))define('KODBC_PATH',BACKSTAGE_DIR."/DO/Kodbc.class.php");
if(!defined(STATIC_DIR))define('STATIC_DIR',BACKSTAGE_DIR."/PUBLIC/");
if(!defined(DUSTBIN_DIR))define('DUSTBIN_DIR',BACKSTAGE_DIR."/dustbin/");
if(!defined(NEWS_FILE_DIR))define('NEWS_FILE_DIR',BACKSTAGE_DIR."/news/");
if(!defined(IMAGE_BED_DIR))define('IMAGE_BED_DIR',BACKSTAGE_DIR."/image/");
if(!defined(TOOLS_PATH))define('TOOLS_PATH',BACKSTAGE_DIR."/tools/");