<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PaymentBankResource;
use App\Models\PaymentBank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentBankController extends Controller
{
    public function index(): JsonResponse
    {
        $banks = PaymentBank::orderBy('sort_order')->orderBy('bank_name')->get();

        return response()->json([
            'success' => true,
            'data' => PaymentBankResource::collection($banks),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_name' => 'required|string|max:150',
            'account_number' => 'required|string|max:50',
            'qr_image' => 'nullable',
            'qr_image_file' => 'nullable|image|max:5120',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('qr_image_file')) {
            $validated['qr_image'] = $request->file('qr_image_file')->store('payment-qr', 'public');
        } elseif (!empty($validated['qr_image']) && is_string($validated['qr_image'])) {
            $validated['qr_image'] = $validated['qr_image'];
        } else {
            unset($validated['qr_image']);
        }
        unset($validated['qr_image_file']);

        $bank = PaymentBank::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment bank created.',
            'data' => new PaymentBankResource($bank),
        ], 201);
    }

    public function show(PaymentBank $paymentBank): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new PaymentBankResource($paymentBank),
        ]);
    }

    public function update(Request $request, PaymentBank $paymentBank): JsonResponse
    {
        $validated = $request->validate([
            'bank_name' => 'sometimes|required|string|max:100',
            'account_name' => 'sometimes|required|string|max:150',
            'account_number' => 'sometimes|required|string|max:50',
            'qr_image' => 'nullable',
            'qr_image_file' => 'nullable|image|max:5120',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('qr_image_file')) {
            if ($paymentBank->qr_image) {
                Storage::disk('public')->delete($paymentBank->qr_image);
            }
            $validated['qr_image'] = $request->file('qr_image_file')->store('payment-qr', 'public');
        }
        unset($validated['qr_image_file']);

        $paymentBank->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment bank updated.',
            'data' => new PaymentBankResource($paymentBank->fresh()),
        ]);
    }

    public function destroy(PaymentBank $paymentBank): JsonResponse
    {
        if ($paymentBank->qr_image) {
            Storage::disk('public')->delete($paymentBank->qr_image);
        }

        $paymentBank->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment bank deleted.',
        ]);
    }
}
