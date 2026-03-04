<?php

/**
 * Locale Configuration
 *
 * Defines supported languages and their settings for the application.
 * This provides a single source of truth for locale management.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used when no session locale is set.
    | Arabic (ar) is the default as it's the primary language.
    |
    */
    'default' => 'ar',

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | List of locales that are supported by the application.
    | Each locale must have corresponding language files:
    * - resources/lang/{locale}/
    *
    | 'ar' = Arabic (RTL)
    | 'en' = English (LTR)
    |
    */
    'supported' => ['ar', 'en'],

    /*
    |--------------------------------------------------------------------------
    | Locale to Direction Mapping
    |--------------------------------------------------------------------------
    |
    | Maps locales to their text direction (RTL/LTR).
    | Used by the SetLocale middleware to set view direction.
    |
    */
    'directions' => [
        'ar' => 'rtl',
        'en' => 'ltr',
    ],

    /*
    |--------------------------------------------------------------------------
    | Locale Names
    |--------------------------------------------------------------------------
    |
    | Human-readable names for supported locales.
    | Useful for language switchers and UI display.
    |
    */
    'names' => [
        'ar' => 'العربية',
        'en' => 'English',
    ],
];
