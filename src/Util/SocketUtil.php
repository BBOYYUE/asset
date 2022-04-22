<?php


namespace Bboyyue\Asset\Util;


class SocketUtil
{
    static function sendInfo($info)
    {
        $host = config('bboyyue-asset.command.listen.host');
        $port = config('bboyyue-asset.command.listen.port');
        $fp = stream_socket_client("tcp://" . $host . ":" . $port, $errno, $errstr) or die("error: $errstr\n");
        /**
         * 这里如果是数组的话就打包
         * 如果是字符串的话就直接发
         */
        if(is_array($info) || is_object($info)){
            $info = json_encode($info);
        }
        fwrite($fp, pack('N', strlen($info)) . $info);
    }
}