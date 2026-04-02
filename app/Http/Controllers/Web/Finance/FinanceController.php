<?php

namespace App\Http\Controllers\Web\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Ledger;
use Illuminate\Http\Request;

class FinanceController extends Controller {

    public function index(Request $request) {
        return view('finance.ledger.index', [
            'journal_entries' => JournalEntry::with(['account'])->latest()->paginate($request->input('pagination_size', 10)),
            'accounts' => Account::get()
        ]);
    }

    public function store(Request $request) {

        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'debit' => 'required_without:credit|numeric|min:0',
            'credit' => 'required_without:debit|numeric|min:0',
            'description' => 'required|string',
        ]);

        JournalEntry::create([
            'tenant_id' => auth()->user()->tenant_id,
            'account_id' => $request->account_id,
            'debit' => $request->debit ?? 0,
            'credit' => $request->credit ?? 0,
            'description' => $request->description,
            'ledger_id' => Ledger::first()->id ?? null, // Simplification
        ]);

        return back()->with('success', 'Journal entry recorded successfully.');
    }
}
