<?php


namespace Bboyyue\Asset\Commands;

use Exception;
use Illuminate\Console\Command;
use Swoole\Process\Manager;
use Swoole\Process\Pool;

class AssetListenCommand extends Command
{
    /**
     * 命令名称及签名
     *
     * @var string
     */
    protected $signature = 'asset:listen';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '资源管理器的 listen';

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
        $this->listen();
    }

    function listen()
    {
        $host = '0.0.0.0';
        $port = config('bboyyue-asset.command.listen.port');
        $maxProcesses = config('bboyyue-asset.command.listen.maxProcesses');
        $service = config('bboyyue-asset.service');
        $pool = new Pool($maxProcesses, SWOOLE_IPC_SOCKET);
        $pool->on('WorkerStart', function (Pool $pool, int $workerId) use ($host, $port) {
            echo "listen:" . $host . ":" . $port . "\t\n";
            echo "asset #{$workerId} is started \t\n";
            $pool->workerId = $workerId;
        });

        $pool->on("Message", function ($pool, $message) use ($host, $port, $service) {
            echo "进程 ". $pool->workerId . ':'.$message . "\t\n";
            $json = json_decode($message, true);
            if($message && count($json) > 0 && isset($json['work_type']) && isset($json['method'])){
                $methods = $service[$json['work_type']];
                $method = $methods[$json['method']];
                $impl = $method[$json['asset_type']];
                return $impl::run($json, $pool->workerId);
            }
        });
        $pool->listen($host, $port);
        $pool->start();
    }
}