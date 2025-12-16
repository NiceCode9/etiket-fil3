<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScanController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function scanQRCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $ticket = $this->ticketService->scanQRForWristband(
                $request->qr_code,
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'QR Code scanned successfully',
                'data' => [
                    'ticket' => $ticket->load(['customer', 'event', 'ticketType']),
                    'wristband_code' => $ticket->wristband_code,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function validateWristband(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wristband_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $ticket = $this->ticketService->validateWristband(
                $request->wristband_code,
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Wristband validated successfully',
                'data' => [
                    'ticket' => $ticket->load(['customer', 'event', 'ticketType']),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getTicketByQR($qrCode)
    {
        try {
            $ticket = \App\Models\Ticket::where('qr_code', $qrCode)
                ->with(['customer', 'event', 'ticketType', 'scanLogs'])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $ticket,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found',
            ], 404);
        }
    }

    public function getTicketByWristband($wristbandCode)
    {
        try {
            $ticket = \App\Models\Ticket::where('wristband_code', $wristbandCode)
                ->with(['customer', 'event', 'ticketType', 'scanLogs'])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $ticket,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Wristband not found',
            ], 404);
        }
    }
}
