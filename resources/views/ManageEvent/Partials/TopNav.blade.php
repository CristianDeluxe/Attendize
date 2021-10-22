@section('pre_header')
    @if(!$event->is_live)
        <style>
            .sidebar {
                top: 63px;
            }
        </style>
        <div class="alert alert-warning top_of_page_alert">
            {{ @trans("ManageEvent.event_not_live") }}
            {!! Form::open(['url' => route('MakeEventLive', ['event_id' => $event->id]), 'id' => 'make-event-live-form', 'style' => 'display:inline-block;']) !!}
                {!! Form::submit(trans('ManageEvent.publish_it'), ['class' => 'btn btn-success']) !!}
            {!! Form::close() !!}
        </div>
    @endif
@stop
<ul class="nav navbar-nav navbar-left">
    <!-- Show Side Menu -->
    <li class="navbar-main">
        <a href="javascript:void(0);" class="toggleSidebar" title="Show sidebar">
            <span class="toggleMenuIcon">
                <span class="icon ico-menu"></span>
            </span>
        </a>
    </li>
    <!--/ Show Side Menu -->
    <li class="nav-button">
        <a target="_blank" href="{{$event->event_url}}">
            <span>
                <i class="ico-eye2"></i>&nbsp;@lang("ManageEvent.event_page")
            </span>
        </a>
    </li>
</ul>