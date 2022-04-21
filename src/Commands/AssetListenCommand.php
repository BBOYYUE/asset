<?php


namespace Bboyyue\Asset\Commands;


use Illuminate\Console\Command;

class AssetListenCommand extends Command
{
    /**
     * 命令名称及签名
     *
     * @var string
     */
    protected $signature = 'asset:listen {drive?}';

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
        $host = config('bboyyue-asset.command.listen.host');
        $port = config('bboyyue-asset.command.listen.port');
        $maxProcesses = config('bboyyue-asset.command.listen.maxProcesses');

        $service = config('bboyyue-asset.service');
        $pool = new Pool($maxProcesses, SWOOLE_IPC_SOCKET);
        $pool->on('WorkerStart', function (Pool $pool, int $workerId) use ($host, $port) {
            echo "listen:" . $host . ":" . $port . "\n";
            echo "asset #{$workerId} is started\n";
            $pool->workerId = $workerId;
        });

        $pool->on("Message", function ($pool, $message) use($host, $port, $service) {
            try {
                $data = json_decode($message);
                $method = $data->method;
                $service = $service[$method];
                $pool->write(
                    json_encode([
                        'code' => 200,
                        'msg' => 'ok!',
                        'body' => $service::run($data->body,  $pool->workerId)
                    ])
                );
            } catch (\Exception $e) {
                echo $e->__toString();
                $pool->write(
                    json_encode([
                        'code' => 500,
                        'msg' => $e->__toString()
                    ])
                );
            }
        });
        $pool->listen($host, $port);
        $pool->start();
    }
}