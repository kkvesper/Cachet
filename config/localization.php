<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Date format
    |--------------------------------------------------------------------------
    |
    | Date formats for `formatted_date()` helper.
    | If the locale is not found, it will use `setting.date_format`.
    */

    'date_format' => [
        'ja' => 'Y年m月d日 l',
        'en' => 'jS F Y',
    ],

    /*
    |--------------------------------------------------------------------------
    | Incident Date format
    |--------------------------------------------------------------------------
    |
    | Date formats used for incident, see `TimestampsTrait::incidentDateFormat()` method.
    | If the locale is not found, it will use `setting.incident_date_format`.
    */

    'incident_date_format' => [
        'ja' => 'Y年m月d日 l H:i:s',
        'en' => 'l jS F Y H:i:s',
    ],

];
