<?php


namespace Bboyyue\Asset\Util;


use Illuminate\Support\Facades\Redis;

/**
 * Bboyyue\Asset\Util\RedisUtil
 * 一些常用操作
 * waiting, pending, progress, success, failed, working, info
 * Illuminate\Support\Facades\Redis::lrange(config('bboyyue-asset.redis.waiting'), 0 ,-1);
 * Illuminate\Support\Facades\Redis::lrange(config('bboyyue-asset.redis.pending'), 0 ,-1);
 * Illuminate\Support\Facades\Redis::zrange(config('bboyyue-asset.redis.progress'), 0, -1);
 * Illuminate\Support\Facades\Redis::lrange(config('bboyyue-asset.redis.success'), 0 ,-1);
 * Illuminate\Support\Facades\Redis::lrange(config('bboyyue-asset.redis.failed'), 0 ,-1);
 * Illuminate\Support\Facades\Redis::zrange(config('bboyyue-asset.redis.working'), 0 , '-1');
 * Illuminate\Support\Facades\Redis::hgetall(config('bboyyue-asset.redis.info'));
 * Illuminate\Support\Facades\Redis::zscore(config('bboyyue-asset.redis.progress'), 3)
 * Illuminate\Support\Facades\Redis::hdel(config('bboyyue-asset.redis.info'), $key);
 * Class RedisUtil
 * @package Bboyyue\Asset\Util
 */

class RedisUtil
{

    static function clearAll()
    {
        for($i = 0; $i < Redis::llen(config('bboyyue-asset.redis.pending')); $i++){
            Redis::lpop(config('bboyyue-asset.redis.pending'));
        }
        for($i = 0; $i < Redis::llen(config('bboyyue-asset.redis.waiting')); $i++){
            Redis::lpop(config('bboyyue-asset.redis.waiting'));
        }
        for($i = 0; $i < Redis::llen(config('bboyyue-asset.redis.success')); $i++){
            Redis::lpop(config('bboyyue-asset.redis.success'));
        }
        for($i = 0; $i < Redis::llen(config('bboyyue-asset.redis.failed')); $i++){
            Redis::lpop(config('bboyyue-asset.redis.failed'));
        }
        $progress = Redis::zrange(config('bboyyue-asset.redis.progress'), 0, -1);
        $working = Redis::zrange(config('bboyyue-asset.redis.working'), 0 , '-1');
        $info = Redis::hgetall(config('bboyyue-asset.redis.info'));

        foreach ($progress as $key => $val){
            Redis::zrem(config('bboyyue-asset.redis.progress'), $key);
        }
        foreach ($working as $key => $val){
            Redis::zrem(config('bboyyue-asset.redis.working'), $key);
        }
        foreach ($info as $key => $val){
            Redis::hdel(config('bboyyue-asset.redis.info'), $key);
        }
    }
    /**
     * 设置某个任务的进度
     * 进度使用有序列表进行保存
     * @param $key
     * @param $progress
     */
    static function setProgress($key, $progress)
    {
        return Redis::zadd(config('bboyyue-asset.redis.progress'), $progress, $key);
    }

    /**
     * 将某个 key 加入到 wait 队列
     * @param $key
     */
    static function addWaiting($key)
    {
        return Redis::lpush(config('bboyyue-asset.redis.waiting'), $key);
    }

    /**
     * 为某个 key 设置描述信息
     * @param $key
     * @param $info
     */
    static function setInfo($key, $info)
    {
        return Redis::hset(config('bboyyue-asset.redis.info'), $key, $info);
    }

    /**
     * 将最后一个等待的 key 加入到 pending 中
     */
    static function moveLastWaitToPending()
    {
        $pending = Redis::brpoplpush(config('bboyyue-asset.redis.waiting'), config('bboyyue-asset.redis.pending'), 1);
        echo "正在处理: " . $pending . " \t\n";
        self::addWorking($pending, 0);
        $info = Redis::hget(config('bboyyue-asset.redis.info'), $pending);
        SocketUtil::sendInfo($info);
    }

    static function moveLastWaitToWait()
    {
        $pending = Redis::brpoplpush(config('bboyyue-asset.redis.waiting'), config('bboyyue-asset.redis.waiting'), 1);
        echo "任务被阻塞,所以跳过: " . $pending . " \t\n";
    }

    /**
     * 为某个key设置计时器
     * @param $key
     * @param $time
     */
    static function addWorking($key, $time)
    {
        return Redis::zadd(config('bboyyue-asset.redis.working'), $time, $key);
    }

    static function incrByDegreesWorking($time)
    {
        $list = Redis::zrange(config('bboyyue-asset.redis.working'), 0, '-1');
        foreach ($list as $val) {
            Redis::zincrby(config('bboyyue-asset.redis.working'), 5, $val);
        }
    }

    static function removeWorking($key)
    {
        return Redis::zrem(config('bboyyue-asset.redis.working'), $key);
    }

    static function removeProgress($key)
    {
        return Redis::zrem(config('bboyyue-asset.redis.progress'), $key);
    }

    static function removeInfo($key)
    {
        return Redis::hdel(config('bboyyue-asset.redis.info'), $key);
    }

    /**
     *  将最后一个 pending 移入 已成功队列中
     */
    static function movePendingToSuccess()
    {
        return Redis::brpoplpush(config('bboyyue-asset.redis.pending'), config('bboyyue-asset.redis.success'), 1);
    }

    /**
     * 将最后一个 pending 移入 已失败队列中
     */
    static function movePendingToFailed()
    {
        return Redis::brpoplpush(config('bboyyue-asset.redis.pending'), config('bboyyue-asset.redis.failed'), 1);
    }

    static function getWaitingLen()
    {
        return Redis::llen(config('bboyyue-asset.redis.waiting'));
    }

    static function setInterceptor($key, $val)
    {
        return Redis::hset(config('bboyyue-asset.redis.interceptor'), $key, $val);
    }

    static function getInterceptor()
    {
        return Redis::hvals(config('bboyyue-asset.redis.interceptor'));
    }

    static function getWaitingLast()
    {
        return Redis::lindex(config('bboyyue-asset.redis.waiting'), -1);
    }

    static function removeInterceptor($key)
    {
        return Redis::hdel(config('bboyyue-asset.redis.interceptor'), $key);
    }
}