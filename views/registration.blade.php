@extends('index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1>{{ _('Регистрация') }}</h1>
            <form action="{{ SITE_URL }}{{ CURRENT_LANG }}/registration" method="POST">
                <input type="hidden" name="validationRules" value="{{ json_encode($validationRules) }}">
                <div class="form-group">
                    <label for="name">{{ _('Ваше имя') }}</label>
                    <input type="text" class="form-control" id="name" name="name">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                    <label for="secondName">{{ _('Ваша фамилия') }}</label>
                    <input type="text" class="form-control" id="secondName" name="secondName">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                    <label for="email">{{ _('Ваш email') }}</label>
                    <input type="text" class="form-control" id="email" name="email">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                    <label for="file">{{ _('Ваше фото') }}</label>
                    <input type="file" class="form-control" id="file" name="image" accept="{{ $validationRules['image']['types'] }}">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                    <label for="password">{{ _('Пароль') }}</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                    <label for="passwordConfirm">{{ _('Подтверждение пароля') }}</label>
                    <input type="password" class="form-control" id="passwordConfirm" name="passwordConfirm">
                    <div class="invalid-feedback"></div>
                </div>
                <button type="submit" class="btn btn-primary">{{ _('Зарегистрироваться') }}</button>
                <a href="{{ SITE_URL }}{{ CURRENT_LANG }}/login" class="btn btn-default">{{ _('Войти') }}</a>
            </form>
        </div>
    </div>
@endsection
