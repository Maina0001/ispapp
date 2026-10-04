<?php

namespace Modules\Network\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RadiusAccountController extends Controller
{
    /**
     * Display a listing of active sessions/RADIUS accounts.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Active RADIUS sessions retrieved successfully.',
            'data' => []
        ]);
    }

    /**
     * Store a new account (usually automated via onboarding/vouchers).
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'RADIUS account registered.'
        ], 201);
    }

    /**
     * Display a specific session state.
     */
    public function show($id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => ['session_id' => $id]
        ]);
    }

    /**
     * Update a RADIUS account record.
     */
    public function update(Request $request, $id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'RADIUS account updated.'
        ]);
    }

    /**
     * Remove/Terminate a session.
     */
    public function destroy($id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'RADIUS account removed.'
        ]);
    }

    /**
     * Force disconnect a user session from the NAS (MikroTik) using PoD.
     */
    public function disconnect($username): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => "Disconnect signal (Packet of Disconnect) sent to user: {$username}."
        ]);
    }
}