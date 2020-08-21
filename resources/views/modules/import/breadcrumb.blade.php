<div class="nav-wrapper">
    <div class="col s12">
        <div class="breadcrumb-container left">
            {{-- Admin --}}
            @if ($admin_env)
            <span class="breadcrumb">
                <a class="btn-flat" href="{{ ucroute('uccello.settings.dashboard', $domain) }}">
                    <i class="material-icons left">settings</i>
                    <span class="hide-on-small-only">{{ uctrans('breadcrumb.admin', $module) }}</span>
                </a>
            </span>
            @endif

            {{-- Module icon --}}
            <span class="breadcrumb" style="margin-right: 15px">
                <a class="btn-flat" href="{{ ucroute('uccello.list', $domain, $module) }}">
                    <i class="material-icons left">{{ $module->icon ?? 'extension' }}</i>
                    <span class="hide-on-small-only">{{ uctrans($module->name, $module) }}</span>
                </a>
            </span>

            <span class="breadcrumb active">{{ trans('import::import.label.import') }}</span>
        </div>
    </div>
</div>
