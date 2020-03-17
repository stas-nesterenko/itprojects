@extends('index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>{{ _('Регистрация') }}</h1>
            <form action="{{ SITE_URL }}{{ CURRENT_LANG }}/registration.php" method="POST">

                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>
        </div>
    </div>
@endsection
