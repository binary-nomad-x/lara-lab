@extends('layouts.nexus')

@section('title', 'General Ledger')

@section('content')
<div class="card mb-6">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title">Bookkeeping & Journal Entries</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEntryModal">
            <i class="ti ti-plus ti-xs me-1"></i> New Journal Entry
        </button>
    </div>
    <div class="card-datatable table-responsive">
        <table class="datatables-ledger table border-top">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Account</th>
                    <th>Description</th>
                    <th>Debit</th>
                    <th>Credit</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                <tr>
                    <td>{{ $entry->created_at->format('M d, Y') }}</td>
                    <td>{{ $entry->account->name }}</td>
                    <td>{{ $entry->description }}</td>
                    <td class="text-success">{{ $entry->debit > 0 ? '$' . number_format($entry->debit, 2) : '-' }}</td>
                    <td class="text-danger">{{ $entry->credit > 0 ? '$' . number_format($entry->credit, 2) : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">No entries found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3">
            {{ $entries->links() }}
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addEntryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Journal Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('finance.ledger.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label" for="account_id">Account</label>
                        <select id="account_id" name="account_id" class="form-select" required>
                            @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->type }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label" for="debit">Debit Amount</label>
                            <input type="number" step="0.01" id="debit" name="debit" class="form-control" placeholder="0.00">
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="credit">Credit Amount</label>
                            <input type="number" step="0.01" id="credit" name="credit" class="form-control" placeholder="0.00">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="2" placeholder="Describe the transaction..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
