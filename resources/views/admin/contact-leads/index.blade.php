@extends('layouts/layoutMaster')

@section('title', 'Leads -- Admin Dream Digital')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Leads</h1>
            <p class="mb-0 text-muted">Demandes entrantes depuis les formulaires publics Dream Digital.</p>
          </div>
          <form method="GET" action="{{ route('admin.contact-leads.index') }}" class="d-flex gap-2 align-self-start">
            <select name="status" class="form-select">
              <option value="">Tous les statuts</option>
              <option value="new" @selected($status === 'new')>Nouveaux</option>
              <option value="qualified" @selected($status === 'qualified')>Qualifies</option>
              <option value="closed" @selected($status === 'closed')>Fermes</option>
            </select>
            <button type="submit" class="btn btn-outline-primary">Filtrer</button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Contact</th>
                <th>Societe</th>
                <th>Service</th>
                <th>Volume</th>
                <th>Statut</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              @forelse($leads as $lead)
                <tr>
                  <td>
                    <strong>{{ $lead->full_name }}</strong>
                    <div class="small text-muted">{{ $lead->email }}</div>
                    @if($lead->phone)
                      <div class="small text-muted">{{ $lead->phone }}</div>
                    @endif
                  </td>
                  <td>{{ $lead->company_name ?? '--' }}</td>
                  <td>{{ $lead->service_interest ?? '--' }}</td>
                  <td>{{ $lead->monthly_volume ?? '--' }}</td>
                  <td><span class="badge bg-label-primary">{{ $lead->status }}</span></td>
                  <td class="small text-muted">{{ $lead->created_at?->format('Y-m-d H:i') }}</td>
                </tr>
                <tr>
                  <td colspan="6" class="border-top-0 pt-0">
                    <div class="text-muted">{{ $lead->message }}</div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-5">Aucun lead pour le moment.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($leads->hasPages())
          <div class="card-footer">
            {{ $leads->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection
