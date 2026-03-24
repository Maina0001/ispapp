<?php

namespace Modules\Network\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Network\Http\Requests\StoreNasRequest;
use Modules\Network\Http\Requests\UpdateNasRequest;
use Modules\Network\Http\Resources\NasResource;
use Modules\Network\Models\Nas;
use Modules\Network\Services\RadiusManager;

/**
 * Class NasController
 * Handles management of NAS (Network Access Servers) devices.
 */
class NasController extends Controller
{
    /**
     * @param RadiusManager $radiusManager
     */
    public function __construct(
        protected RadiusManager $radiusManager
    ) {}

    /**
     * List all NAS devices.
     * Scoped by tenant_id via BaseModel.
     */
    public function index(): AnonymousResourceCollection
    {
        $nasDevices = Nas::query()
            ->latest()
            ->paginate(request()->query('per_page', 15));

        return NasResource::collection($nasDevices);
    }

    /**
     * Store a new NAS device and sync with FreeRADIUS.
     */
    public function store(StoreNasRequest $request): JsonResponse
    {
        // Logic delegated to RadiusManager to ensure DB and config sync
        $nas = $this->radiusManager->registerNasDevice($request->validated());

        return (new NasResource($nas))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display NAS details and current connection count.
     */
    public function show(Nas $nas): NasResource
    {
        return new NasResource($nas->loadCount('activeSessions'));
    }

    /**
     * Update NAS configuration.
     */
    public function update(UpdateNasRequest $request, Nas $nas): NasResource
    {
        $updatedNas = $this->radiusManager->updateNasDevice($nas, $request->validated());

        return new NasResource($updatedNas);
    }

    /**
     * Remove NAS and dispatch cleanup jobs.
     */
    public function destroy(Nas $nas): JsonResponse
    {
        $this->radiusManager->removeNasDevice($nas);

        return response()->json([
            'message' => 'NAS device removed and de-registered from RADIUS.'
        ], 200);
    }
}