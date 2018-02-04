<?php
//set time zone
date_default_timezone_set("America/Chicago");
//define(string name, mixed value) -> mixed means many type, this function defines the constant 
define("TOKEN", "benpan");

//difference between require_once and require is that it check whether the file has been included
require_once("Utils.php");

$wechatObj = new wechatCallBackapiTest();

//isset check if a variable is set or not, $_GET -> associative array to access all the sent information 
//guess echostr here is a special parameter for URL authentication from wechat
if (isset($_GET["echostr"])) {
    $wechatObj->valid();
} else {
    $wechatObj->responseMsg();
}

class wechatCallBackapiTest {
    /* 
        for wechat identification 
        from the documentation, echo back the echostr content, reorder token, timestamp, nonce and sha1 encryption 
    */
    public function valid() {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()) {
            echo $echoStr;
            //exit ends a current script and output a message
            exit;
        }
    }
    
    //this is the code from wechat documentation
    private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        //implode(array) -> glue array to be string
        $tmpStr = implode($tmpArr);
        //sha1(string) -> calculate the sha1 hash of a string
        $tmpStr = sha1($tmpStr);
        if($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
    
    // response message to user
    public function responseMsg() {
        //$GLOBALS is the super global variables array
        //HTTP_RAW_POST_DATA can get the post data
        //libxml_disable_entity_loader prevent loading external entities to be safer
        libxml_disable_entity_loader(true);
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        Utils::logger($postStr);
        if(!empty($postStr)) {
            //analyze the data as xml into xml obj
            //simplexml_load_string(string data, string class_name, option) -> simplexmlelement 
            //LIBXML_NOCDATA flag merge CDATA as text nodes
            /*
            
            text format is as followed:
            <xml>  <ToUserName>< ![CDATA[toUser] ]></ToUserName>  <FromUserName>< ![CDATA[fromUser] ]></FromUserName>  <CreateTime>1348831860</CreateTime>  <MsgType>< ![CDATA[text] ]></MsgType>  <Content>< ![CDATA[this is a test] ]></Content>  <MsgId>1234567890123456</MsgId>  </xml>
            
            this is sent when user sends text
            
            we have 3 chances to respond and 5 second for each chance
            
            empty string can be recognized as respond and wechat server will not send anything back
            
            */
            
            /*
            
            but here, in the test example here, we only care about user subscribe or not
        
            */
            
            
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            //trim = strip, MsgType is one textnode
            $RX_TYPE = trim($postObj -> MsgType);
            switch($RX_TYPE) {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                default:
                    $result = "Unknown message type: ".$RX_TYPE
                    break;
            }
            Utils::logger($result, "Official account");
            
            // seems like echo is the response back to the server
            echo $result;
        } else {
            echo "";
            exit;
        }
    }
    
    //Receive the event
    private function receiveEvent($object) {
        switch(trim($object->Event)) {
            case "subscribe":
                $content = "Welcome for the testing";
                break;
            default:
                $content = "";
                break;
        }
        $result = $this->transmitEvent($object, $content);
        return $result;
    }
    
    //Respond the subscribe message
    private function transmitEvent($object, $content) {
        $xmlTpl = "<xml> <ToUserName>< ![CDATA[%s] ]></ToUserName> <FromUserName>< ![CDATA[%s] ]></FromUserName> <CreateTime> %d </CreateTime> <MsgType>< ![CDATA[text] ]></MsgType> <Content>< ![CDATA[%s] ]></Content> </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
}