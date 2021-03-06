<!DOCTYPE html>
<html>
<head>

    @php
        $appLocale = app('translator')->getLocale();
    @endphp

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="env" content="{{ app('env') }}">
    <meta name="token" content="{{ csrf_token() }}">
    <meta name="locale" content="{{ $appLocale }}">

    <link rel="alternate" type="application/atom+xml" href="{{ cachet_route('feed.atom') }}" title="{{ $site_title }} - Atom Feed">
    <link rel="alternate" type="application/rss+xml" href="{{ cachet_route('feed.rss') }}" title="{{ $site_title }} - RSS Feed">

    <!-- Mobile friendliness -->
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="description" content="{{ trans('cachet.description', ['app' => $app_name]) }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $site_title }}">
    <meta property="og:image" content="/img/favicon.png">
    <meta property="og:description" content="{{ trans('cachet.description', ['app' => $app_name]) }}">

    <!-- Mobile IE allows us to activate ClearType technology for smoothing fonts for easy reading -->
    <meta http-equiv="cleartype" content="on">

    <meta name="msapplication-TileColor" content="{{ $theme_greens }}" />
    <meta name="msapplication-TileImage" content="{{ asset('/img/favicon.png') }}" />

    @if (isset($favicon))
    <link rel="icon" href="{{ asset("/img/{$favicon}.ico") }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset("/img/{$favicon}.png") }}" type="image/png">
    @else
    <link rel="icon" href="{{ asset('/img/favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('/img/favicon.png') }}" type="image/png">
    @endif

    <link rel="apple-touch-icon" href="{{ asset('/img/apple-touch-icon.png') }}">
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('/img/apple-touch-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('/img/apple-touch-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('/img/apple-touch-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('/img/apple-touch-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('/img/apple-touch-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('/img/apple-touch-icon-152x152.png') }}">

    <title>@yield('title', $site_title)</title>

    @if($enable_external_dependencies)
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&subset={{ $font_subset }}" rel="stylesheet" type="text/css">
    @endif
    <link rel="stylesheet" href="{{ mix('dist/css/app.css') }}">

    @include('partials.stylesheet')

    @include('partials.crowdin')

    @if($app_stylesheet)
    <style type="text/css">
    {!! $app_stylesheet !!}
    </style>
    @endif

    <style type="text/css">
        #locale-switcher {
            text-align: right;
        }

        #locales {
            border-color: transparent;
            background: transparent;
        }
    </style>

    <script type="text/javascript">
        var Global = {};

        var refreshRate = parseInt("{{ $app_refresh_rate }}");

        function refresh() {
                window.location.reload(true);
        }

        if(refreshRate > 0) {
                setTimeout(refresh, refreshRate * 1000);
        }

        Global.locale = '{{ $app_locale }}';
    </script>
    <script src="{{ mix('dist/js/manifest.js') }}"></script>
    <script src="{{ mix('dist/js/vendor.js') }}"></script>
</head>
<body class="status-page @yield('bodyClass')">
    @yield('outer-content')

    @include('partials.banner')

    <div class="container" id="app">

        <form action="{{ url('/locale') }}" id="locale-switcher" method="post">
            {{ csrf_field() }}
            <select name="locale" id="locales">
                @foreach(config('localization.locales', []) as $locale => $label)
                    @php
                        $isSelected = false;

                        if (strcasecmp($locale, $appLocale) === 0) {
                            $isSelected = true;
                        } elseif (strlen($locale) === 2) {
                            $localeSegments = explode('-', $appLocale);
                            $shortLocale = strtolower($localeSegments[0]);

                            $isSelected = ($locale === $shortLocale);
                        }
                    @endphp
                    <option value="{{ $locale }}" {{ $isSelected ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </form>

        @yield('content')
    </div>

    @yield('bottom-content')

    <script src="{{ mix('dist/js/all.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment-with-locales.min.js"></script>
    <script>
        $(function () {
            // App local
            const locale = $('meta[name="locale"]').attr('content');

            // Locale switcher
            const $locales = $('#locales');

            $locales.on('change', function () {
                $('#locale-switcher').submit();
            });

            // Display relative time using user's local time
            const relativeDates = $('.js-relative');

            function updateRelativeDates() {
                relativeDates.each(function () {
                    var $this = $(this);
                    var iso = $this.data('date-iso');

                    var localDate = moment(iso).utc().local().locale(locale);

                    var relativeTime = localDate.fromNow();
                    var date = localDate.format('LLLL');

                    $this
                        .attr('data-title', date)
                        .attr('data-original-title', date)
                        .html(relativeTime);
                });
            }

            updateRelativeDates();
            setInterval(updateRelativeDates, 60 * 1000); // Update every minutes

            // Rebuild "grouped incidents" based on user's local time
            function groupIncidents(type) {
                var $incidents = $('.section-' + type + ' .moment');
                var $jsTimelines = $('#js-' + type + '-incidents');

                $incidents.each(function () {
                    var $this = $(this);
                    var iso = $this.data('date');
                    var localDate = moment(iso).utc().local();
                    var date = localDate.format('Y-MM-DD');
                    var label = localDate.locale(locale).format('LL');
                    var timelineId = type + '-timeline-' + date;

                    var $timeline = $('#' + timelineId);

                    if ($timeline.length === 0) {
                        $jsTimelines.append('<h4>' + label + '</h4><div class="timeline" id="' + timelineId + '"><div class="content-wrapper"></div></div>');
                        $timeline = $('#' + timelineId);
                    }

                    $this.appendTo($timeline);
                });

                $jsTimelines.removeClass('hidden')
            }

            groupIncidents('stickied');
            groupIncidents('timeline');
        });
    </script>
</body>
</html>
