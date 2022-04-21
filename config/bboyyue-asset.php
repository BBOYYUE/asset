<?php

use Bboyyue\Asset\Repositiories\Services\GeneratePanoramaService;

return [
    "job"=> "asset_job",
    "command"=> [
        'listen' => [
            "host"=> "",
            "port"=> "",
            "maxProcesses"=> ""
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
         * pending
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