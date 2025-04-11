<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFundRequest;
use App\Http\Resources\FundResource;
use App\Models\Fund;
use App\Services\FundReturnService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FundController extends Controller
{
    public function __construct(private FundReturnService $service) {

    }

    public function index(): AnonymousResourceCollection
    {
        $funds = Fund::with(['returns' => function ($query) {
            $query->where('reverted', false)->latest('date');
        }])->paginate(10);

        return FundResource::collection($funds);
    }

    public function store(StoreFundRequest $request): FundResource
    {
        $fund = Fund::create($request->validated());
        return new FundResource($fund);
    }

    public function show(Fund $fund): FundResource
    {
        $fund->load(['returns' => function ($query) {
            $query->where('reverted', false)->orderBy('date');
        }]);

        return new FundResource($fund);
    }

    public function valueAtDate(Request $request, Fund $fund): JsonResponse
    {
        $request->validate([
            'date' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:today',
                function ($attribute, $value, $fail) use ($fund) {
                    if (Carbon::parse($value) < $fund->created_at->startOfDay()) {
                        $fail("Date cannot be before fund creation date ({$fund->created_at->format('Y-m-d')})");
                    }
                }
            ]
        ]);

        $date = Carbon::parse($request->input('date'));
        $value = $this->service->getFundValueAtDateOptimized($fund, $date);

        return response()->json([
            'date' => $date->toDateString(),
            'value' => $value,
            'currency' => $fund->currency,
        ]);
    }

    public function projection(Request $request, Fund $fund): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'return_percentage' => 'required|numeric',
            'is_compounding' => 'boolean',
            'frequency' => 'required|in:monthly,quarterly,yearly',
        ]);

        $projections = $this->service->calculateProjectedValue(
            $fund,
            Carbon::parse($request->input('start_date')),
            Carbon::parse($request->input('end_date')),
            $request->input('return_percentage'),
            $request->boolean('is_compounding'),
            $request->input('frequency')
        );

        return response()->json([
            'fund_id' => $fund->id,
            'projections' => $projections,
            'currency' => $fund->currency,
        ]);
    }

    public function returnHistory(Fund $fund): JsonResponse
    {
        $returns = $fund->returns()
            ->with('fund')
            ->orderBy('date')
            ->get();

        return response()->json([
            'fund_id' => $fund->id,
            'returns' => $returns,
        ]);
    }
}
