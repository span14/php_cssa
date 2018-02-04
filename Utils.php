<?php 

class Utils 
{
    public static function traceHttp() {
        // date(string format)
        $content = date('Y-m-d H:i:s')."\n\rremote_ip:".$_SERVER["REMOTE_ADDR"]."\n\r".$_SERVER["QUERY_STRING"]."\n\r\n\r";
        $max_size = 1000;
        $log_filename = "./query.xml";
        // file_exists(file path), filesize(file path) ->bytes in the file
        if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)) {
            //unlink(filename) -> delete current file
            unlink($log_filename);
        }
        //file_put_content -> append content to the file. If file not exists, create one
        file_put_contents($log_filename, $content, FILE_APPEND);
    }
    
    public static function logger($log_content, $type='User') 
    {
        $max_size = 3000;
        $log_filename = "./log.xml";
        if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)) {
            unlink($log_file);
        }
        file_put_contents($log_filename, "$type ".date('Y-m-d H:i:s')."\n\r".$log_content."\n\r", FILE_APPEND);
    }
}