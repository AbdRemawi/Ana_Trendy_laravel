<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

/**
 * Flash Message Service
 *
 * Centralizes flash message handling across the application.
 * Reduces duplication and ensures consistent message patterns.
 */
class FlashMessageService
{
    /**
     * Flash success message to session.
     *
     * @param string $key Translation key (e.g., 'brand.created')
     * @param array $params Translation parameters
     * @return void
     */
    public static function success(string $key, array $params = []): void
    {
        $messageKey = self::buildMessageKey($key, 'success');

        Session::flash('success', __($messageKey, $params));
    }

    /**
     * Flash error message to session.
     *
     * @param string $key Translation key (e.g., 'brand.deleted')
     * @param array $params Translation parameters
     * @return void
     */
    public static function error(string $key, array $params = []): void
    {
        $messageKey = self::buildMessageKey($key, 'error');

        Session::flash('error', __($messageKey, $params));
    }

    /**
     * Flash warning message to session.
     *
     * @param string $key Translation key
     * @param array $params Translation parameters
     * @return void
     */
    public static function warning(string $key, array $params = []): void
    {
        $messageKey = self::buildMessageKey($key, 'warning');

        Session::flash('warning', __($messageKey, $params));
    }

    /**
     * Flash info message to session.
     *
     * @param string $key Translation key
     * @param array $params Translation parameters
     * @return void
     */
    public static function info(string $key, array $params = []): void
    {
        $messageKey = self::buildMessageKey($key, 'info');

        Session::flash('info', __($messageKey, $params));
    }

    /**
     * Build standardized message key from resource and action.
     * Examples:
     * - 'brand.created' -> 'admin.brand_created_successfully'
     * - 'category.updated' -> 'admin.category_updated_successfully'
     *
     * @param string $key Dot-separated key (resource.action)
     * @param string $type Message type
     * @return string
     */
    protected static function buildMessageKey(string $key, string $type): string
    {
        [$resource, $action] = explode('.', $key);

        $suffix = match ($type) {
            'success' => match ($action) {
                'created' => 'created_successfully',
                'updated' => 'updated_successfully',
                'deleted' => 'deleted_successfully',
                'restored' => 'restored_successfully',
                default => $action,
            },
            'error' => match ($action) {
                'deleted' => 'delete_failed',
                'updated' => 'update_failed',
                default => $action,
            },
            default => $action,
        };

        return "admin.{$resource}_{$suffix}";
    }

    /**
     * Get redirect response with success message.
     *
     * @param string $route Route name
     * @param string $key Translation key
     * @param array $params Translation parameters
     * @param array $routeParams Route parameters
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function redirectSuccess(
        string $route,
        string $key,
        array $params = [],
        array $routeParams = []
    ): \Illuminate\Http\RedirectResponse {
        self::success($key, $params);

        return redirect()->route($route, $routeParams);
    }

    /**
     * Get redirect response with error message.
     *
     * @param string|null $route Route name (null for back())
     * @param string $key Translation key
     * @param array $params Translation parameters
     * @param array $routeParams Route parameters
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function redirectError(
        ?string $route,
        string $key,
        array $params = [],
        array $routeParams = []
    ): \Illuminate\Http\RedirectResponse {
        self::error($key, $params);

        return $route ? redirect()->route($route, $routeParams) : back();
    }
}
