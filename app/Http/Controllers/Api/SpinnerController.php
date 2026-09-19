<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ClaimSpinRequest;
use App\Http\Requests\Api\SpinRequest;
use App\Models\SpinnerPrize;
use App\Models\SpinnerSpin;
use App\Services\SpinnerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpinnerController extends Controller
{
    public function __construct(
        private SpinnerService $spinnerService
    ) {}

    /**
     * Return the wheel segments to render (labels, colors, order).
     *
     * Weights/probabilities are intentionally NOT exposed so the odds can't be
     * reverse-engineered from the client.
     */
    public function config(): JsonResponse
    {
        $prizes = SpinnerPrize::displayable()->get()->map(fn (SpinnerPrize $p) => [
            'id' => $p->id,
            'label' => $p->label,
            'type' => $p->type,
            'color' => $p->color,
            'sort_order' => $p->sort_order,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'prizes' => $prizes,
            ],
        ]);
    }

    /**
     * Report, for a given device, whether it can play today and what its
     * current (last, un-redeemed) reward is.
     */
    public function status(Request $request): JsonResponse
    {
        $request->validate([
            'device_id' => ['required', 'string', 'min:8', 'max:100'],
        ]);

        $deviceId = $request->query('device_id');
        $reward = $this->spinnerService->activeReward($deviceId);

        return response()->json([
            'success' => true,
            'data' => [
                'can_play' => $this->spinnerService->canPlay($deviceId),
                'reward' => $reward ? $this->rewardPayload($reward) : null,
            ],
        ]);
    }

    /**
     * Spin the wheel for a device.
     */
    public function spin(SpinRequest $request): JsonResponse
    {
        $deviceId = $request->input('device_id');

        if (!$this->spinnerService->canPlay($deviceId)) {
            $last = $this->spinnerService->lastSpinToday($deviceId);

            return response()->json([
                'success' => false,
                'message' => 'You have already played today. Come back tomorrow!',
                'data' => [
                    'can_play' => false,
                    // So the UI can still show what they last won today.
                    'reward' => ($last && $last->is_win)
                        ? $this->rewardPayload($last)
                        : null,
                ],
            ], 429);
        }

        $spin = $this->spinnerService->spin($deviceId, $request->ip());

        return response()->json([
            'success' => true,
            'message' => 'Spin completed',
            'data' => [
                // The segment the wheel must animate to.
                'prize_id' => $spin->spinner_prize_id,
                'label' => $spin->prize_label,
                'result_type' => $spin->result_type,
                'is_win' => $spin->is_win,
                'can_respin' => $spin->can_respin,
                // Token needed to submit the phone number for a winning spin.
                'token' => $spin->token,
                'reward' => $spin->is_win ? $this->rewardPayload($spin->fresh('coupon')) : null,
            ],
        ]);
    }

    /**
     * Attach the customer's phone number to a winning spin.
     */
    public function claim(ClaimSpinRequest $request): JsonResponse
    {
        $spin = $this->spinnerService->attachPhone(
            $request->input('token'),
            $request->input('phone_number')
        );

        if (!$spin) {
            return response()->json([
                'success' => false,
                'message' => 'Spin not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Phone number saved. Use your coupon at checkout.',
            'data' => [
                'reward' => $spin->is_win ? $this->rewardPayload($spin->fresh('coupon')) : null,
            ],
        ]);
    }

    /**
     * Shape the reward (coupon) data returned to the front-end.
     */
    private function rewardPayload(SpinnerSpin $spin): ?array
    {
        $coupon = $spin->coupon;

        if (!$coupon) {
            return null;
        }

        return [
            'coupon_code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
            'minimum_order_amount' => (float) $coupon->minimum_order_amount,
            'valid_until' => $coupon->valid_until?->toIso8601String(),
            'label' => $spin->prize_label,
        ];
    }
}
