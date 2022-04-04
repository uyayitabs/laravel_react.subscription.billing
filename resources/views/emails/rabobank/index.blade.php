@extends('layouts.email')
@section('content')
    @if ($state == 'ok')
        @foreach ($files as $file)
            <p>{{ $file }}</p>
        @endforeach
    @else
        <p>{{ $message }}</p>
    @endif
@endsection