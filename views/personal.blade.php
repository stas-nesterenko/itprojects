@extends('index')

@section('content')
    <div class="row">
        <div class="card col-md-6 mx-auto">
            @if ($user->image)
                <img src="/public/storage/users/{{ $user->id }}/{{ $user->image }}" class="card-img-top" alt="...">
            @endif
            <div class="card-body">
                <h5 class="card-title">{{ $user->name }} {{ $user->secondname }}</h5>
                <p class="card-text">Email: {{ $user->email }}</p>
                <a href="{{ SITE_URL }}{{ CURRENT_LANG }}/logout" class="btn btn-primary">{{ _('Выход') }}</a>
            </div>
        </div>
    </div>
@endsection
