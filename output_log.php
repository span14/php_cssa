<?php 
$filepath = './log.xml';
if(file_exists($filepath)) {
    @header('Content-type: text/plain;charset=UTF-8');
    readfile($filepath);
}
else {
    @header("HTTP/1.0 404 Not Found");
}
