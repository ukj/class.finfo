<?php

if(!class_exists('finfo')) {
    define('BASEDIR', pathinfo( __FILE__,PATHINFO_DIRNAME ).DIRECTORY_SEPARATOR );
    require_once(BASEDIR.'class.finfo.php');
}