<?php

namespace App\Traits;

use DB;

trait Loggable
{
    protected static $logTable = 'activity_logs';

    public static function logToDb($model, $logType)
    {
        if (! auth()->check()) {
            return;
        }
        if ($logType == 'create') {
            $originalData = json_encode($model);
        } else {
            $originalData = json_encode($model->getOriginal());
        }

        $excludeFields = ['display_order'];

        // Check if any excluded field has been modified
        // this is to exclude multiple data saved if course is created since it updates the display_order
        if ($logType == 'edit') {
            foreach ($excludeFields as $field) {
                if ($model->isDirty($field)) {
                    // Skip logging if excluded field is being updated
                    return;
                }
            }
        }

        $tableName = $model->getTable();
        $dateTime = date('Y-m-d H:i:s');
        $userId = auth()->user()->id;

        DB::table(self::$logTable)->insert([
            'user_id' => $userId,
            'log_date' => $dateTime,
            'table_name' => $tableName,
            'log_type' => $logType,
            'data' => $originalData,
        ]);
    }

    public static function bootLoggable()
    {
        self::updated(function ($model) {
            self::logToDb($model, 'edit');
        });

        self::deleted(function ($model) {
            self::logToDb($model, 'delete');
        });

        self::created(function ($model) {
            self::logToDb($model, 'create');
        });
    }
}
