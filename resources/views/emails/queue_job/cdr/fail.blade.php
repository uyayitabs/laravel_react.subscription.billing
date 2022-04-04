@extends('layouts.email')
@section('content')
<p>Filename: {{ $filename }}</p>
<p>Processed records: {{ $processed }}</p>
<p>Error: {{ $failed }}</p>
@endsection
