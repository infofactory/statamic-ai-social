@extends('statamic::layout')
@section('wrapper_class', 'max-w-full')

@section('title', __('statamic-ai-social::cp.generate.title'))

@section('content')
    <script id="csrf_token" type="text/plain">{{ csrf_token() }}</script>
    <script id="raw_html_page" type="text/html">{{ $entry_html }}</script>
    <script id="socials" type="application/json">@json($socials)</script>
    <social-post-generator :socials="JSON.parse(document.getElementById('socials').textContent)"></social-post-generator>
@endsection
