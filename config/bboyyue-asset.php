<?php


use Bboyyue\Asset\Enum\ThreeTypeEnum;
use Bboyyue\Asset\Repositiories\Services\Panorama\GeneratePanoramaDirXmlService;
use Bboyyue\Asset\Repositiories\Services\Panorama\GeneratePanoramaService;
use Bboyyue\Asset\Repositiories\Services\Panorama\GeneratePanoramaWorkXmlService;
use Bboyyue\Asset\Repositiories\Services\Panorama\GeneratePanoramaXmlService;
use Bboyyue\Asset\Repositiories\Services\Panorama\RefreshPanoramaDirService;
use Bboyyue\Asset\Repositiories\Services\Panorama\RefreshPanoramaDirXmlService;
use Bboyyue\Asset\Repositiories\Services\Panorama\RefreshPanoramaService;
use Bboyyue\Asset\Repositiories\Services\Panorama\RefreshPanoramaWorkService;
use Bboyyue\Asset\Repositiories\Services\Panorama\RefreshPanoramaWorkXmlService;
use Bboyyue\Asset\Repositiories\Services\Panorama\RefreshPanoramaXmlService;
use Bboyyue\Asset\Repositiories\Services\Three\GenerateThreeService;

return [
    "job" => "asset_job",
    "command" => [
        'listen' => [
            "host" => "127.0.0.1",
            "port" => "9505",
            "maxProcesses" => "4"
        ]
    ],

    "service" => [
        \Bboyyue\Asset\Enum\AssetTypeEnum::PANORAMA => [
            "generate" => [
                \Bboyyue\Asset\Enum\PanoramaTypeEnum::ASSET => GeneratePanoramaService::class,
                \Bboyyue\Asset\Enum\PanoramaTypeEnum::WORK => GeneratePanoramaWorkXmlService::class,
                \Bboyyue\Asset\Enum\PanoramaTypeEnum::DIR => GeneratePanoramaDirXmlService::class,
            ],
            "generateXml" => [
                \Bboyyue\Asset\Enum\PanoramaTypeEnum::ASSET => GeneratePanoramaXmlService::class,
                \Bboyyue\Asset\Enum\PanoramaTypeEnum::WORK => GeneratePanoramaWorkXmlService::class,
                \Bboyyue\Asset\Enum\PanoramaTypeEnum::DIR => GeneratePanoramaDirXmlService::class,
            ],
            "refresh" => [
                \Bboyyue\Asset\Enum\PanoramaTypeEnum::ASSET => RefreshPanoramaService::class,
                \Bboyyue\Asset\Enum\PanoramaTypeEnum::WORK => RefreshPanoramaWorkService::class,
                \Bboyyue\Asset\Enum\PanoramaTypeEnum::DIR => RefreshPanoramaDirService::class,
            ],
            "refreshXml" => [
                \Bboyyue\Asset\Enum\PanoramaTypeEnum::ASSET => RefreshPanoramaXmlService::class,
                \Bboyyue\Asset\Enum\PanoramaTypeEnum::WORK => RefreshPanoramaWorkXmlService::class,
                \Bboyyue\Asset\Enum\PanoramaTypeEnum::DIR => RefreshPanoramaDirXmlService::class,
            ]
        ],
        \Bboyyue\Asset\Enum\AssetTypeEnum::THREE => [
            "generate" => [
                \Bboyyue\Asset\Enum\ThreeTypeEnum::ASSET => GenerateThreeService::class,
                \Bboyyue\Asset\Enum\ThreeTypeEnum::WORK => GenerateThreeService::class,
                \Bboyyue\Asset\Enum\ThreeTypeEnum::DIR => GenerateThreeService::class,
            ]
        ]
    ],
    "redis" => [
        /**
         * 任务进度
         */
        "progress" => "asset_progress",

        /**
         * interceptor
         */
        "interceptor" => "asset_interceptor",

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

        /**
         * 最大等待数量
         */
        "max_waiting_len" => 60
    ],
];