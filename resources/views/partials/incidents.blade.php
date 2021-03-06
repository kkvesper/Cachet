@php
    $appLocale = app('translator')->getLocale();
@endphp
<h4>{{ formatted_date($date) }}</h4>
<div class="timeline">
    <div class="content-wrapper">
        @forelse($incidents as $incident)
        <div class="moment {{ $loop->first ? 'first' : null }}" data-date="{{ $incident->occurred_at_iso }}">
            <div class="row event clearfix">
                <div class="col-sm-1">
                    <div class="status-icon status-{{ $incident->latest_human_status }}" data-toggle="tooltip" title="{{ $incident->latest_human_status }}" data-placement="left">
                        <i class="{{ $incident->latest_icon }}"></i>
                    </div>
                </div>
                @php
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
                <div class="col-xs-10 col-xs-offset-2 col-sm-11 col-sm-offset-0">
                    <div class="panel panel-message incident">
                        <div class="panel-heading">
                            @if($current_user)
                            <div class="pull-right btn-group">
                                <a href="{{ cachet_route('dashboard.incidents.edit', ['id' => $incident->id]) }}" class="btn btn-default">{{ trans('forms.edit') }}</a>
                                <a href="{{ cachet_route('dashboard.incidents.delete', ['id' => $incident->id], 'delete') }}" class="btn btn-danger confirm-action" data-method='DELETE'>{{ trans('forms.delete') }}</a>
                            </div>
                            @endif
                            @if($incident->component)
                            <span class="label label-default">{{ $incident->component->name }}</span>
                            @endif
                            <strong>{{ $name }}</strong>{{ $incident->isScheduled ? trans("cachet.incidents.scheduled_at", ["timestamp" => $incident->scheduled_at_diff]) : null }}
                            <br>
                            <small class="date">
                                <a href="{{ cachet_route('incident', ['id' => $incident->id]) }}" class="links">
                                    <abbr class="js-relative" data-date-iso="{{ $incident->occurred_at_iso }}" data-toggle="tooltip" data-placement="right" title="{{ $incident->timestamp_formatted }}">
                                        &nbsp;
                                    </abbr>
                                </a>
                            </small>
                        </div>
                        <div class="panel-body markdown-body">
                            {!! $message !!}
                        </div>
                        @if($incident->updates->isNotEmpty())
                        <div class="list-group">
                            @foreach($incident->updates as $update)
                            <a class="list-group-item" href="{{ $update->permalink }}">
                                <i class="{{ $update->icon }}" title="{{ $update->human_status }}" data-toggle="tooltip"></i> <strong>{{ Str::limit($update->raw_message, 20) }}</strong>
                                <small>{{ $update->created_at_diff }}</small>
                                <span class="ion-ios-arrow-right pull-right"></span>
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
        @empty
        <div class="panel panel-message incident">
            <div class="panel-body">
                <p>{{ trans('cachet.incidents.none') }}</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
