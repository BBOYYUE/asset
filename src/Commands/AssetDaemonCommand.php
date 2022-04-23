<?php


namespace Bboyyue\Asset\Commands;

use Bboyyue\Asset\Util\RedisUtil;
use Illuminate\Support\Facades\Redis;
use Illuminate\Console\Command;

class
AssetDaemonCommand extends Command
{
    /**
     * 命令名称及签名
     *
     * @var string
     */
    protected $signature = 'asset:daemon {drive?}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '资源管理器的 daemon';

    /**
     * 创建命令
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 执行命令
     *
     * @return mixed
     */
    public function handle()
    {
        while(true){
            $this->checkPendingJob(1);
            $this->addWork();
            RedisUtil::incrWorking(5);
        }
    }

    protected function addWork(){
        /**
         * 获取当前正在进行的任务数量, 如果小于最大处理条数的话, 就将 pending 最后一项移到处理中
         */
        $len = Redis::llen(config('bboyyue-asset.redis.pending'));
        for($i=0; $i < 3 - $len; $i++){
            RedisUtil::moveLastWaitToPending();
        }
    }

    protected function checkPendingJob($index){
        $len = Redis::llen(config('bboyyue-asset.redis.pending'));
        $pending = Redis::lindex(config('bboyyue-asset.redis.pending'), $len - $index);
        $time = Redis::hget(config('bboyyue-asset.redis.working'), $pending);
        $progress = Redis::zscore(config('bboyyue-asset.redis.progress'),$pending);
        if($progress === 100){
            /**
             * 将最后一项移出到成功列表
             */
            RedisUtil::movePendingToSuccess();
            /**
             * 移除计时器
             */
            RedisUtil::removeWorking($pending);
            /**
             * 判断下一项
             */
            $this->checkPendingJob($index + 1);
        }elseif($time > config('bboyyue-asset.redis.timeout')){
            /**
             * 将最后一项移出到失败列表
             */
            RedisUtil::movePendingToFailed();
            /**
             * 移除计时器
             */
            RedisUtil::removeWorking($pending);
            /**
             * 判断下一项
             */
            $this->checkPendingJob($index + 1);
        }
        /**
         * 如果当前项既没有成功, 也没有超时, 那么终止递归
         */
    }
}