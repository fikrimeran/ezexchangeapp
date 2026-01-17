<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExchangeRequest;
use App\Models\User;
use App\Models\Item;

class ExchangeRequestController extends Controller
{
    /**
     * Show all exchange history.
     */
public function index()
{
    $exchangerequests = \App\Models\ExchangeRequest::with(['fromUser', 'toUser', 'fromItem', 'toItem'])
        ->latest()
        ->paginate(10); // ✅ now supports links()

    return view('auth.exchangerequests.index', compact('exchangerequests'));
}


    /**
     * Show details of a single exchange history record.
     */
    public function show(ExchangeRequest $exchangerequest)
    {
        return view('auth.exchangerequests.show', compact('exchangerequest'));
    }

    /**
     * Edit a specific exchange history record.
     */
public function edit($id)
{
    $exchange = \App\Models\ExchangeRequest::findOrFail($id);

    // ✅ Fetch all users & items for the dropdowns
    $users = \App\Models\User::all();
    $items = \App\Models\Item::all();

    return view('auth.exchangerequests.edit', compact('exchange', 'users', 'items'));
}


    /**
     * Update the exchange history record.
     */
    public function update(Request $request, ExchangeRequest $exchangerequest)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $exchangerequest->update([
            'status' => $request->status,
        ]);

        return redirect()->route('auth.exchangerequests.index')
            ->with('success', 'Exchange history updated successfully.');
    }

    /**
     * Delete a record from exchange history.
     */
    public function destroy(ExchangeRequest $exchangerequest)
    {
        $exchangerequest->delete();

        return redirect()->route('auth.exchangerequests.index')
            ->with('success', 'Exchange history deleted successfully.');
    }
}
