<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePjokRecordRequest;
use App\Models\PjokRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PjokRecordController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', PjokRecord::class);

        $query = PjokRecord::query();

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }

        return $query->latest()->paginate(25);
    }

    public function store(StorePjokRecordRequest $request)
    {
        Gate::authorize('create', PjokRecord::class);

        $record = PjokRecord::create($request->validated());

        return response()->json($record, 201);
    }
}
