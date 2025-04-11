<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFundReturnRequest;
use App\Http\Resources\FundReturnResource;
use App\Models\Fund;
use App\Models\FundReturn;
use App\Services\FundReturnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FundReturnController extends Controller
{
    public function __construct(private FundReturnService $service)
    {
        $this->service = $service;
    }

    public function index(Fund $fund): AnonymousResourceCollection
    {
        $returns = $fund->returns()
            ->latest('date')
            ->get();

        return FundReturnResource::collection($returns);
    }

    public function store(StoreFundReturnRequest $request, Fund $fund): FundReturnResource
    {
        $return = $this->service->addReturn($fund, $request->validated());
        return new FundReturnResource($return);
    }

    public function revert(FundReturn $fundReturn): JsonResponse
    {
        $this->service->revertReturn($fundReturn);
        return response()->json(['message' => 'Return reverted successfully']);
    }
}
