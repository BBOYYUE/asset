<?php


namespace Bboyyue\Asset\Util;


use Illuminate\Support\Facades\Redis;

class RedisUtil
{
    /**
     * 设置某个任务的进度
     * 进度使用有序列表进行保存
     * @param $key
     * @param $progress
     */
    static function setProgress($key, $progress){
        Redis::zadd( config('bboyyue-asset.redis.progress'),$progress, $key);
    }

    /**
     * 将某个 key 加入到 wait 队列
     * @param $key
     */
    static function addWaiting($key)
    {
        Redis::lpush(config('bboyyue-asset.redis.waiting'), $key);
    }

    /**
     * 为某个 key 设置描述信息
     * @param $key
     * @param $info
     */
    static function setInfo($key, $info)
    {
        Redis::hset(config('bboyyue-asset.redis.info'), $key, $info);
    }

    /**
     * 将最后一个等待的 key 加入到 pending 中
     */
    static function moveLastWaitToPending()
    {
        $pending = Redis::brpoplpush(config('bboyyue-asset.redis.waiting'), config('bboyyue-asset.redis.pending'));
        self::addWorking($pending, 0);
    }

    /**
     * 为某个key设置计时器
     * @param $key
     * @param $time
     */
    static function addWorking($key, $time)
    {
        Redis::zadd(config('bboyyue-asset.redis.working'), $time, $key);
    }

    static function incrWorking($time){
        $list = Redis::zrange(config('bboyyue-asset.redis.working'), 0 , '-1');
        foreach ($list as $val){
            self::addWorking($val, $time);
        }
    }
    static function removeWorking($key)
    {
        Redis::zrem(config('bboyyue-asset.redis.working'), $key);
    }

    /**
     *  将最后一个 pending 移入 已成功队列中
     */
    static function movePendingToSuccess()
    {
        Redis::brpoplpush(config('bboyyue-asset.redis.pending'), config('bboyyue-asset.redis.success'));
    }

    /**
     * 将最后一个 pending 移入 已失败队列中
     */
    static function movePendingToFailed()
    {
        Redis::brpoplpush(config('bboyyue-asset.redis.pending'), config('bboyyue-asset.redis.failed')) ;
    }
}