<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PaymentBankResource;
use App\Models\PaymentBank;
use Illuminate\Http\JsonResponse;

class PaymentBankController extends Controller
{
    public function index(): JsonResponse
    {
        $banks = PaymentBank::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('bank_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => PaymentBankResource::collection($banks),
        ]);
    }
}
