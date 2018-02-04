<?php
$filepath = './query.xml';
if(file_exists($filepath)) {
    //@header means suppress any error in the header function
    @header('Content-type: text/plain;charset=UTF-8');
    //readfile(file path) -> it seems like it will output stuff into current php, this seems to do with header, for this, content-type
    //readfile read file into buffer, file_get_contents load file into memory
    readfile($filepath);
} else {
    @header("HTTP/1.0 404 Not Found");
}
