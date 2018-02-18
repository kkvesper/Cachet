@extends('layout.master')

@section('title', $incident->name.' | '.$site_title)

@section('bodyClass', 'no-padding')

@section('outer-content')
@include('partials.nav')
@stop

@php
    $appLocale = app('translator')->getLocale();

    $translation = array_first($incident->translations, function ($translation) use ($appLocale) {
        return ($translation->locale === $appLocale);
    });

    $englishTranslation = array_first($incident->translations, function ($translation) {
        return ($translation->locale === 'en');
    });

    $name = $incident->name;
    $message = $incident->formatted_message;

    if ($translation || ($appLocale !== 'ja' && $englishTranslation)) {
        if (!empty($translation->name)) {
            $name = $translation->name;
        } elseif (!empty($englishTranslation->name)) {
            $name = $englishTranslation->name;
        }

        if (!empty($translation->message)) {
            $message = \GrahamCampbell\Markdown\Facades\Markdown::convertToHtml($translation->message);
        } elseif (!empty($englishTranslation->message)) {
            $message = \GrahamCampbell\Markdown\Facades\Markdown::convertToHtml($englishTranslation->message);
        }
    }
@endphp

@section('content')
<h1>{{ $name }} <small>{{ $incident->occurred_at_formatted }}</small></h1>

<hr>

<div class="markdown-body">
    {!! $message !!}
</div>

@if($incident->updates)
<div class="timeline">
    <div class="content-wrapper">
        @foreach ($incident->updates as $update)
        <div class="moment {{ $loop->first ? 'first' : null }}" id="update-{{ $update->id }}">
            <div class="row event clearfix">
                <div class="col-sm-1">
                    <div class="status-icon status-{{ $update->status }}" data-toggle="tooltip" title="{{ $update->human_status }}" data-placement="left">
                        <i class="{{ $update->icon }}"></i>
                    </div>
                </div>
                <div class="col-xs-10 col-xs-offset-2 col-sm-11 col-sm-offset-0">
                    <div class="panel panel-message incident">
                        <div class="panel-body">
                            <div class="markdown-body">
                                {!! $update->formatted_message !!}
                            </div>
                        </div>
                        <div class="panel-footer">
                            <small>
                                <span data-toggle="tooltip" title="
                                    {{ trans('cachet.incidents.posted_at', ['timestamp' => $update->created_at_formatted]) }}">
                                    {{ trans('cachet.incidents.posted', ['timestamp' => $update->created_at_diff]) }}
                                </span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@stop

@section('bottom-content')
@include('partials.footer')
@stop
