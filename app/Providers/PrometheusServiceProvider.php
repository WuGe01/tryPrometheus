<?php

namespace App\Providers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;
use Spatie\Prometheus\Collectors\Horizon\CurrentMasterSupervisorCollector;
use Spatie\Prometheus\Collectors\Horizon\CurrentProcessesPerQueueCollector;
use Spatie\Prometheus\Collectors\Horizon\CurrentWorkloadCollector;
use Spatie\Prometheus\Collectors\Horizon\FailedJobsPerHourCollector;
use Spatie\Prometheus\Collectors\Horizon\HorizonStatusCollector;
use Spatie\Prometheus\Collectors\Horizon\JobsPerMinuteCollector;
use Spatie\Prometheus\Collectors\Horizon\RecentJobsCollector;
use Spatie\Prometheus\Facades\Prometheus;

class PrometheusServiceProvider extends ServiceProvider
{
    public function register()
    {
        /*
         * Here you can register all the exporters that you
         * want to export to prometheus
         */
        Prometheus::addGauge('api_requests_total')
            ->value(function () {
                return Redis::llen('api_requests_log');
            });

        Prometheus::addGauge('api_requests_detail')
            ->label('status_code')
            ->value(function () {

            // 從 Redis 中獲取 status_code 的統計數據
            $statusCodeKey = 'api_requests_status_code';
            $statusCodes = Redis::hgetall($statusCodeKey); // 獲取所有 status_code 的統計

            $return = [];
            foreach ($statusCodes as $key => $statusCode) {
                $return[] = [ $statusCode, [$key]];
            }

            return $return;
        });

        /*
         * Uncomment this line if you want to export
         * all Horizon metrics to prometheus
         */
        // $this->registerHorizonCollectors();
    }

    public function registerHorizonCollectors(): self
    {
        Prometheus::registerCollectorClasses([
            CurrentMasterSupervisorCollector::class,
            CurrentProcessesPerQueueCollector::class,
            CurrentWorkloadCollector::class,
            FailedJobsPerHourCollector::class,
            HorizonStatusCollector::class,
            JobsPerMinuteCollector::class,
            RecentJobsCollector::class,
        ]);

        return $this;
    }
}
