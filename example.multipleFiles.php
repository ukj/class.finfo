<?php

require_once('./loader.finfo.php');

$files = scandir('./dir');

//$Yo = new myfinfo(FILEINFO_MIME_ENCODING);
//$Yo = new myfinfo(FILEINFO_MIME_TYPE);
//$Yo = new myfinfo(FILEINFO_MIME);
$Yo = new finfo(FILEINFO_MIME_ENCODING|FILEINFO_MIME_TYPE);
foreach($files as $file) {
    //copy($file, './dir/'.end(explode('/',$file)));
    echo '<b><a href="./dir/' . end(explode('/',rtrim($file,'/'))) .'">'. end(explode('/',rtrim($file,'/'))) .'</a></b> '. $Yo->file('./dir/'.$file) . "<br />\n";
    //echo "<p> {$Yo->log} </p>\n";
}