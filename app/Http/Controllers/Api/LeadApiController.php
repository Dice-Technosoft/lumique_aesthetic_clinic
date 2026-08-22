<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\LeadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadApiController extends Controller
{
    public function __construct(
        protected LeadService $leadService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $query = Lead::with('assignedUser:id,name', 'leadSource:id,name', 'notesList.author:id,name', 'followups')->latest();
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('service_name', 'like', "%{$s}%");
            });
        }
        $leads = $query->paginate($request->integer('per_page', 15));
        return response()->json([
            'success' => true,
            'data' => $leads->items(),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'service_name' => 'nullable|string',
            'service_id' => 'nullable|exists:services,id',
            'lead_source_id' => 'nullable|exists:lead_sources,id',
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'estimated_value' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $lead = $this->leadService->createLead($data, $request->user());
        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully',
            'data' => $lead,
        ], 201);
    }

    public function update(Request $request, Lead $lead): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'service_name' => 'nullable|string',
            'status' => 'nullable|string',
            'estimated_value' => 'nullable|numeric',
        ]);

        $lead->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully in database',
            'data' => $lead,
        ]);
    }

    public function destroy(Lead $lead): JsonResponse
    {
        $lead->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead deleted successfully from database',
        ]);
    }

    public function addNote(Request $request, Lead $lead): JsonResponse
    {
        $request->validate(['note' => 'required|string|max:3000']);
        $note = $this->leadService->addNote($lead, $request->note, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Note added successfully',
            'data' => $note,
        ]);
    }

    public function scheduleFollowUp(Request $request, Lead $lead): JsonResponse
    {
        $data = $request->validate([
            'follow_up_date' => 'required|date',
            'follow_up_time' => 'nullable|string',
            'note' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $followUp = $this->leadService->scheduleFollowUp($lead, $data, $request->user());
        return response()->json([
            'success' => true,
            'message' => 'Follow-up scheduled successfully',
            'data' => $followUp,
        ]);
    }
}
