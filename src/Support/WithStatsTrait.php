<?php

namespace Uccello\Import\Support;

use Illuminate\Support\Facades\Cache;

trait WithStatsTrait
{
    protected $notificationData;
    protected $cacheKey;

    public function initCache($filePath)
    {
        $this->cacheKey = 'importData' . basename($filePath);

        $this->notificationData = null;
    }

    public function getFromCache()
    {
        return $this->notificationData = Cache::get(
            $this->cacheKey,
            [
                'lines' => 0,
                'created' => 0,
                'updated' => 0,
                'ignored' => 0,
                'log' => '',
            ]
        );
    }

    public function getFromCacheAndClear()
    {
        $this->getFromCache();

        Cache::forget($this->cacheKey);

        return $this->notificationData;
    }

    public function updateCache()
    {
        Cache::forever($this->cacheKey, $this->notificationData);
    }

    protected function line($msg)
    {
        $this->notificationData['log'] .= $msg . PHP_EOL;

        if (env('IMPORT_PRINT_LOG_IN_CONSOLE')) {
            $isCli = php_sapi_name() == 'cli';

            echo $msg . ($isCli ? PHP_EOL : '</br>');

            if (!$isCli) {
                flush();
                ob_flush();
            }
        }
    }
}