@extends('Shared.Layouts.MasterWithoutMenus')

@section('title')
    @lang("Installer.title")
@stop
@section('content')
    <div class="row">
        <div class="col-md-7 col-md-offset-2">
            <div class="panel">
                <div class="panel-body">
                    <div class="logo">
                        {!!Html::image('assets/images/logo-dark.png')!!}
                    </div>

                    <h1>@lang("Installer.upgrade")</h1>
                    @if($upgrade_done === true)
                        <h3>@lang("Installer.upgrade_complete")</h3>
                        <p>@lang("Installer.current_version", ["version" => $local_version])</p>
                    @elseif(version_compare($local_version, $remote_version) === -1)
                        <h3>@lang("Installer.new_version")</h3>
                        <p>@lang("Installer.current_version", ["version" => $local_version])</p>
                        <p>@lang("Installer.download_version", ["version" => $remote_version])</p>
                    @elseif(version_compare($local_version, $remote_version) === 0 && version_compare($installed_version, $local_version) === -1)
                        <h3>@lang("Installer.new_version_ready")</h3>
                        <p>@lang("Installer.current_version", ["version" => $installed_version])</p>
                        <p>@lang("Installer.now_installing", ["version" => $local_version])</p>
                        {!! Form::open(array('url' => route('postUpgrader'), 'class' => 'upgrader_form')) !!}
                        {!! Form::submit(trans("Installer.upgrade_button"), ['class'=>" btn-block btn btn-success"]) !!}
                        {!! Form::close() !!}
                    @else
                        <h3>@lang("Installer.no_upgrade")</h3>
                        <p>@lang("Installer.current_version", ["version" => $local_version])</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
