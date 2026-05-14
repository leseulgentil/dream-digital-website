@extends('layouts/layoutMaster')

@section('title', 'Editer lien -- Navigation')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <nav class="mb-2 small" aria-label="breadcrumb">
            <a href="{{ route('admin.navigation.index') }}">Navigation</a> / <span class="text-muted">Editer #{{ $item->id }}</span>
          </nav>
          <h1 class="h3 mb-0">{{ $item->label_fr }}</h1>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="card">
        @include('admin.navigation._form')
      </div>
    </div>
  </div>
@endsection
