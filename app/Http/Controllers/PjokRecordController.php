<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePjokRecordRequest;
use App\Models\PjokRecord;
use App\Support\PjokMasterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function sync(Request $request)
    {
        Gate::authorize('create', PjokRecord::class);

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:60'],
            'records' => ['required', 'array'],
            'records.*' => ['array'],
        ]);

        abort_unless(array_key_exists($validated['type'], PjokMasterData::defaults()), 422, 'Tipe data master tidak dikenal.');

        DB::transaction(function () use ($validated): void {
            PjokRecord::query()->where('type', $validated['type'])->delete();

            foreach (array_values($validated['records']) as $index => $payload) {
                $code = PjokMasterData::recordCode($validated['type'], $payload, $index);

                PjokRecord::query()->create([
                    'type' => $validated['type'],
                    'code' => $code,
                    'name' => $payload['name'] ?? $payload['className'] ?? $payload['materi'] ?? $code,
                    'payload' => $payload,
                ]);
            }
        });

        return response()->json(['ok' => true]);
    }
}

