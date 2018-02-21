<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Http\Middleware;

use CachetHQ\Cachet\Settings\Repository as SettingsRepository;
use Closure;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;

/**
 * This is the localize middleware class.
 *
 * @author James Brooks <james@alt-three.com>
 * @author Joseph Cohen <joe@alt-three.com>
 * @author Graham Campbell <james@alt-three.com>
 */
class Localize
{
    /**
     * The config repository instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * The settings repository instance.
     *
     * @var \CachetHQ\Cachet\Settings\Repository
     */
    protected $settings;

    /**
     * Constructs a new localize middleware instance.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param \CachetHQ\Cachet\Settings\Repository    $settings
     *
     * @return void
     */
    public function __construct(ConfigRepository $config, SettingsRepository $settings)
    {
        $this->config = $config;
        $this->settings = $settings;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!(bool) $this->settings->get('automatic_localization')) {
            return $next($request);
        }

        $userLanguage = null;

        // Check if a cookie with the locale is set
        if ($request->hasCookie('locale')) {
            $locale = $request->cookie('locale');

            if (array_key_exists($locale, config('localization.locales'))) {
                $userLanguage = $locale;
            }
        }

        if (!$userLanguage) {
            $requestedLanguages = $request->getLanguages();
            $userLanguage = $this->config->get('app.locale');
            $langs = $this->config->get('langs');

            foreach ($requestedLanguages as $language) {
                $language = str_replace('_', '-', $language);

                if (strpos($language, '-')) {
                    list($left, $right) = explode('-', $language);
                    $left = strtolower($left);
                    $right = strtoupper($right);

                    $languageCode = $left;
                    $lcid = "{$left}-{$right}";
                } else {
                    $languageCode = strtolower($language);
                    $lcid = false;
                }

                if (isset($langs[$languageCode])) {
                    $userLanguage = $language;
                    break;
                }

                if ($lcid && isset($langs[$lcid])) {
                    $userLanguage = $lcid;
                    break;
                }
            }
        }

        app('translator')->setLocale($userLanguage);
        Date::setLocale($userLanguage);

        return $next($request);
    }
}
