<?php

namespace Modules\Network\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Network\Http\Requests\StoreIpPoolRequest;
use Modules\Network\Http\Resources\IpPoolResource;
use Modules\Network\Models\IpPool;
use Modules\Network\Services\ProvisioningService;
use Illuminate\Http\JsonResponse;

/**
 * Class IpPoolController
 */
class IpPoolController extends Controller
{
    public function __construct(
        protected ProvisioningService $provisioningService
    ) {}

    /**
     * List all IP pools and their utilization.
     */
    public function index(): AnonymousResourceCollection
    {
        $pools = IpPool::query()
            ->withCount('assignedIps')
            ->paginate(request()->query('per_page', 15));

        return IpPoolResource::collection($pools);
    }

    /**
     * Define a new IP Pool.
     */
    public function store(StoreIpPoolRequest $request): JsonResponse
    {
        $pool = IpPool::create($request->validated());

        return (new IpPoolResource($pool))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show utilization statistics for a pool.
     */
    public function show(IpPool $ipPool): IpPoolResource
    {
        return new IpPoolResource($ipPool->load('assignedIps'));
    }
}