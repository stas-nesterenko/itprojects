@extends('index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>{{ _('Авторизация') }}</h1>
            <form action="{{ SITE_URL }}{{ CURRENT_LANG }}/login" method="POST">
                <div class="form-group">
                    <label for="email">{{ _('Ваш email') }}</label>
                    <input type="text" class="form-control" id="email" name="email">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                    <label for="password">{{ _('Пароль') }}</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <div class="invalid-feedback"></div>
                </div>
                <button type="submit" class="btn btn-primary">{{ _('Войти') }}</button>
                <a href="{{ SITE_URL }}{{ CURRENT_LANG }}/registration" class="btn btn-default">{{ _('Зарегистрироваться') }}</a>
            </form>
        </div>
    </div>
@endsection
