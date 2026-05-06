@isset($pageConfigs)
  {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/commonMaster')

@section('layoutContent')
  <!-- Content -->
  @yield('content')
  <!--/ Content -->
@endsection
