<?php

use Bboyyue\Asset\Repositiories\Services\GeneratePanoramaService;

return [
    "job"=> "asset_job",
    "command"=> [
        'listen' => [
            "host"=> "127.0.0.1",
            "port"=> "9505",
            "maxProcesses"=> "4"
        ]
    ],
    "service" => [
        "generate_panorama" => GeneratePanoramaService::class
    ],
    "redis" => [
        /**
         * 任务进度
         */
        "progress" => "asset_progress",

        /**
         * info
         */
        "info" => "asset_info",

        /**
         * waiting
         */
        "waiting" => "asset_waiting",

        /**
         * pending 是一个列表
         */
        "pending" => "asset_pending",

        /**
         * working
         */
        "working" => "asset_working",


        /**
         * success
         */
        "success" => "asset_success",

        /**
         * failed
         */
        "failed" => "asset_failed",

        /**
         * 超时事件
         */
        "timeout" => 300,
    ]
];