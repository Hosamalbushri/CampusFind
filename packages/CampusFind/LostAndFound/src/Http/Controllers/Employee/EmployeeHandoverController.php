<?php

namespace CampusFind\LostAndFound\Http\Controllers\Employee;

use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use CampusFind\LostAndFound\Http\Requests\Employee\CompleteHandoverRequest;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Services\Application\EmployeeHandoverApplicationService;

class EmployeeHandoverController extends Controller
{
    public function __construct(
        protected EmployeeHandoverApplicationService $applicationService
    ) {}

    public function complete(CompleteHandoverRequest $request, int $id): JsonResponse
    {
        $actor = auth('user')->user();
        $item = FoundItem::findOrFail($id);

        try {
            $handover = $this->applicationService->completeHandover(
                $actor,
                $item,
                $request->validated(),
            );
        } catch (DomainException|InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'data' => [
                'id' => $handover->getKey(),
                'found_item_id' => $handover->found_item_id,
                'item_status' => $handover->foundItem()->value('status'),
                'handed_over_at' => $handover->handed_over_at->toIso8601String(),
            ],
        ]);
    }
}
