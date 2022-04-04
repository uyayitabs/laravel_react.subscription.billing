@extends('layouts.email')
@section('content')
<p>Date/time: {{ $dt }}</p>
<p>Environment: {{ $env }}</p>
<p>Job: {{ $job }}</p>
<p>Error:</p>
<p>{!! $message !!}</p>
@if ($username)
<p>Username: {{ $username }}</p>
@endif
@endsection
