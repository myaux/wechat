<?php
/**
 * Created by PhpStorm.
 * User: XY
 * Date: 2017/10/29
 * Time: 16:36
 */

namespace App\Http\Controllers;


use function foo\func;

class WechatController
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            switch ($RX_TYPE)
            {
                case "text":
                    $resultStr = $this->receiveText($postObj);
                    break;
                case "event":
                    $resultStr = $this->receiveEvent($postObj);
                    break;
            }
            echo $resultStr;
        }else {
            echo "";
            exit;
        }
    }
//处理接受到用户消息的事件
    private function receiveText($object)
    {
        $funcFlag = 0;
        $keyword = trim($object->Content);
        $contentStr = callTuling($keyword);
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }
//处理公众号被关注的事件
    private function receiveEvent($object)
    {
        $contentStr = "";
        switch ($object->Event)
        {
            case "subscribe":
                $contentStr = "你终于来了";
        }
        $resultStr = $this->transmitText($object, $contentStr);
        return $resultStr;
    }
}
//创建函数调用图灵机器人接口
function callTuling($keyword)
{
    $apiKey = "be2452788545490582c706a8d8fe0318"; //填写后台提供的key
    $apiURL = "http://www.tuling123.com/openapi/api?key=be2452788545490582c706a8d8fe0318";

    $reqInfo = $keyword;
    $url = str_replace("INFO", $reqInfo, str_replace("KEY", $apiKey, $apiURL));
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    $file_contents = curl_exec($ch);
    curl_close($ch);
//获取图灵机器人返回的数据，并根据code值的不同获取到不用的数据
    $message = json_decode($file_contents,true);
    $result = "";
    if ($message['code'] == 100000){
        $result = $message['text'];
    }else if ($message['code'] == 200000){
        $text = $message['text'];
        $url = $message['url'];
        $result = $text . " " . $url;
    }else if ($message['code'] == 302000){
        $text = $message['text'];
        $url = $message['list'][0]['detailurl'];
        $result = $text . " " . $url;
    }else {
        $result = "好好说话我们还是基佬";
    }
    return $result;
}
?>