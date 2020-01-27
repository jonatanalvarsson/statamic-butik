@extends('statamic::layout')
@section('title', __('butik::shippings.create'))

@section('content')
    <publish-form
        title="{{ __('butik::general.title') }}"
        action="{{ cp_route('butik.shippings.update', ['shipping' => $id]) }}"
        method="patch"
        :blueprint='@json($blueprint)'
        :meta='@json($meta)'
        :values='@json($values)'
    ></publish-form>
@stop
