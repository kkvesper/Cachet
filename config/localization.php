<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Locales supported
    |--------------------------------------------------------------------------
    |
    | List of locales used for the language selector and the incident translations.
    */

    'locales' => [
        'ja' => '日本語',
        'en' => 'English',
        'ko' => '한국어',
        'zh-CN' => '中文(简体)',
        'zh-TW' => '中文(台灣)',
        'de' => 'Deutsch',
        'es' => 'Español',
        'fr' => 'Français',
        'it' => 'Italiano',
        'id' => 'Bahasa Indonesia',
        'ms' => 'Bahasa Melayu',
        'th' => 'ไทย',
        'tl' => 'Tagalog',
        'vi' => 'Tiếng Việt',
    ],

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
