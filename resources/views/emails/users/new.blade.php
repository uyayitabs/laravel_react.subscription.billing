@extends('layouts.email')

@section('content')
<p>
<p>
	Beste {{ $user_fullname }},
</p>
<p>
	Je kunt inloggen op <a href="{{Config::get('app.front_url')}}">{{Config::get('app.front_url')}}</a> met de username {{$username}}.
</p>
<p>
	Klik op deze link om je password in te stellen:
</p>
<p>
	<a href="{{Config::get('app.front_url')}}/#/auth/verify/{{ $code }}">{{Config::get('app.front_url')}}/#/auth/verify/{{ $code }}</a>
</p>
<p>Met vriendelijke groet,</p>
<p>Het GRID team</p>
</p>
@endsection