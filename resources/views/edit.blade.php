@extends('statamic::layout')

@section('title', __('statamic-ai-social::cp.title'))

@section('content')
    <publish-form
        title='{{ __('statamic-ai-social::cp.title') }}'
        action={{ cp_route('statamic-ai-social.config') }}
        :blueprint='@json($blueprint)'
        :meta='@json($meta)'
        :values='@json($values)'
    ></publish-form>
@stop
