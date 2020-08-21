@extends('uccello::modules.default.index.main')

@section('navbar-top')
<header class="navbar-fixed navbar-top">
    <nav class="transparent z-depth-0">
        <div class="row">
            <div class="col s12 m9">
                @section('breadcrumb')
                @include('import::modules.import.breadcrumb')
                @show
            </div>
            <div class="col s12 m3 hide-on-small-only">
                @section('top-action-buttons')&nbsp;@show
            </div>
        </div>
    </nav>
</header>
@endsection


@section('content')
<div class="row">
    <div class="col s12">
        <div class="card">
            <form action="{{ ucroute('uccello.import.process', $domain, $module) }}" method="POST">
                @csrf
                <div class="card-content">
                    <table>
                        <tr>
                            <th>{{ trans('import::import.field.header') }}</th>
                            <th>{{ trans('import::import.field.first_row') }}</th>
                            <th>{{ trans('import::import.field.module_fields') }}</th>
                            <th>{{ trans('import::import.field.default_value') }}</th>
                        </tr>
                        @foreach ($headings as $i => $heading)
                            <tr>
                                <td>{{ $heading }}</td>
                                <td>{{ $firstRow[$i] }}</td>
                                <td>
                                    <select name="fields[]">
                                        <option value=""></option>
                                    @foreach ($module->fields as $field)
                                        <option value="{{ $field->name }}">{{ uctrans('field.'.$field->name, $module)}}</option>
                                    @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="defaults[]">
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <div class="card-action center-align">
                    <button type="submit" class="btn green waves-effect">{{ trans('import::import.button.import') }}</button>
                    <input type="hidden" name="filepath" value="{{ $filePath }}">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection