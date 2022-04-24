<?php


namespace Bboyyue\Asset\Commands;

use Bboyyue\Asset\Util\RedisUtil;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class AssetDaemonCommand extends Command
{
    /**
     * 命令名称及签名
     *
     * @var string
     */
    protected $signature = 'asset:daemon';

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
        echo "服务已启动 \t\n";
        while (true) {
            $this->checkPendingJob();
            $this->addWork();
            RedisUtil::incrByDegreesWorking(5);
            sleep(5);
        }
    }

    protected function checkPendingJob()
    {
        echo "校验正在进行中的任务进度 \t\n";
        $len = Redis::llen(config('bboyyue-asset.redis.pending'));
        $pending = Redis::lindex(config('bboyyue-asset.redis.pending'), $len - 1);
        $time = Redis::zscore(config('bboyyue-asset.redis.working'), $pending);
        $progress = Redis::zscore(config('bboyyue-asset.redis.progress'), $pending);
        if ($progress == 100) {
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
            $this->checkPendingJob();
        } elseif ($time > config('bboyyue-asset.redis.timeout')) {
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
            $this->checkPendingJob();
        }
        /**
         * 如果当前项既没有成功, 也没有超时, 那么终止递归
         */
    }

    protected function addWork()
    {
        /**
         * 获取当前正在进行的任务数量, 如果小于最大处理条数的话, 就将 pending 最后一项移到处理中
         */

        $len = Redis::llen(config('bboyyue-asset.redis.pending'));
        for ($i = 0; $i < 3 - $len; $i++) {
            if (Redis::llen(config('bboyyue-asset.redis.waiting')) > 0) {
                if (3 - $len > 0) {
                    echo "将等待中的任务移入操作中队列 \t\n";
                }
                RedisUtil::moveLastWaitToPending();
            }
        }
    }
}