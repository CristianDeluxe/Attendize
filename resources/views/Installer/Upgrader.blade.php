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

                    <h1>Upgrade</h1>
                    @if(version_compare($local_version, $remote_version) === -1)
                        New version available for download.
                    @elseif(version_compare($local_version, $remote_version) === 0 && version_compare($installed_version, $local_version) === -1)
                        New version ready to install.
                    @else
                        Nothing to upgrade!
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
