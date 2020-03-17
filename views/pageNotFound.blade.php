@extends('index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="error-template">
                <h1>{{ _('Упс!') }}</h1>
                <h2>{{ _('Страница не найдена!') }}</h2>
            </div>
        </div>
    </div>
@endsection
