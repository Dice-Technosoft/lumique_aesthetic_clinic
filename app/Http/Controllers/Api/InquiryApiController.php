<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Inquiry;
use App\Services\InquiryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InquiryApiController extends Controller
{
    public function __construct(
        protected InquiryService $inquiryService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $query = Inquiry::with('assignedUser:id,name', 'service:id,title')->latest();
        
        // Filter by type (default to contact form inquiries only)
        if ($request->has('type')) {
            $query->where('type', $request->type);
        } else {
            $query->where('type', 'contact');
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        } else {
            $query->where('status', '!=', 'converted');
        }
        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $inquiries = $query->paginate($request->integer('per_page', 15));
        return response()->json([
            'success' => true,
            'data' => $inquiries->items(),
            'meta' => [
                'current_page' => $inquiries->currentPage(),
                'per_page' => $inquiries->perPage(),
                'total' => $inquiries->total(),
            ],
        ]);
    }

    public function updateStatus(Request $request, Inquiry $inquiry): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:new,contacted,in_progress,converted,closed,spam',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $updated = $this->inquiryService->updateStatus($inquiry, $request->status, $request->assigned_to);

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'module' => 'inquiries',
            'action' => 'status_updated',
            'record_id' => $inquiry->id,
            'new_values' => ['status' => $request->status],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inquiry status updated successfully',
            'data' => $updated,
        ]);
    }

    public function update(Request $request, Inquiry $inquiry): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'service_id' => 'nullable|exists:services,id',
            'service_name' => 'nullable|string|max:150',
            'preferred_date' => 'nullable|date',
            'preferred_time' => 'nullable|string|max:50',
            'status' => 'required|string|in:new,contacted,in_progress,converted,closed,spam',
            'priority' => 'nullable|string|in:low,medium,high',
            'message' => 'nullable|string|max:3000',
        ]);

        if (!empty($validated['service_id'])) {
            $service = \App\Models\Service::find($validated['service_id']);
            $validated['service_name'] = $service?->title ?? $validated['service_name'] ?? null;
        }

        $inquiry->update($validated);

        if ($inquiry->lead) {
            $inquiry->lead->update([
                'name' => $inquiry->name,
                'email' => $inquiry->email,
                'phone' => $inquiry->phone,
                'service_id' => $inquiry->service_id,
                'service_name' => $inquiry->service_name,
                'status' => $inquiry->status,
                'priority' => $inquiry->priority,
                'preferred_date' => $inquiry->preferred_date,
                'preferred_time' => $inquiry->preferred_time,
                'notes' => $inquiry->message,
            ]);
        }

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'module' => 'inquiries',
            'action' => 'inquiry_updated',
            'record_id' => $inquiry->id,
            'new_values' => $validated,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inquiry updated successfully',
            'data' => $inquiry->fresh(['service', 'assignedUser']),
        ]);
    }

    public function destroy(Request $request, Inquiry $inquiry): JsonResponse
    {
        $id = $inquiry->id;
        $name = $inquiry->name;

        if ($inquiry->lead) {
            $inquiry->lead->delete();
        }

        $inquiry->delete();

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'module' => 'inquiries',
            'action' => 'inquiry_deleted',
            'record_id' => $id,
            'notes' => "Deleted inquiry #{$id} ({$name})",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inquiry deleted successfully',
        ]);
    }

    public function convertToAppointment(Request $request, Inquiry $inquiry): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'service_id' => 'nullable|exists:services,id',
            'service_name' => 'nullable|string|max:150',
            'preferred_date' => 'required|date',
            'preferred_time' => 'nullable|string|max:50',
            'priority' => 'nullable|string|in:low,medium,high',
            'estimated_value' => 'nullable|numeric',
            'notes' => 'nullable|string|max:3000',
        ]);

        $service = null;
        if (!empty($validated['service_id'])) {
            $service = \App\Models\Service::find($validated['service_id']);
            $validated['service_name'] = $service?->title ?? $validated['service_name'] ?? null;
        } elseif (!empty($validated['service_name'])) {
            $service = \App\Models\Service::where('title', $validated['service_name'])->first();
            if ($service) {
                $validated['service_id'] = $service->id;
            }
        }

        // Mark existing inquiry as converted
        $inquiry->status = 'converted';
        $inquiry->save();

        // Create or update Lead / Appointment record
        $lead = $inquiry->lead;
        if (!$lead) {
            $lead = new \App\Models\Lead();
            $lead->inquiry_id = $inquiry->id;
        }
        $lead->name = $validated['name'];
        $lead->email = $validated['email'];
        $lead->phone = $validated['phone'];
        $lead->service_id = $validated['service_id'] ?? null;
        $lead->service_name = $validated['service_name'] ?: ($service?->title ?: 'General Consultation');
        $lead->preferred_date = $validated['preferred_date'];
        $lead->preferred_time = $validated['preferred_time'] ?? null;
        $lead->status = 'consultation_scheduled';
        $lead->priority = $validated['priority'] ?? 'medium';
        $lead->estimated_value = $validated['estimated_value'] ?? null;
        $lead->notes = $validated['notes'] ?? $inquiry->message;
        $lead->save();

        // Create Appointment Inquiry instance for Email Templates
        $appointmentInquiry = new Inquiry([
            'name' => $lead->name,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'service_name' => $lead->service_name,
            'preferred_date' => $lead->preferred_date,
            'preferred_time' => $lead->preferred_time,
            'type' => 'appointment',
            'message' => $lead->notes,
        ]);
        $appointmentInquiry->id = $inquiry->id;

        // Dispatch Dual Emails
        \App\Jobs\SendAppointmentThankYouJob::dispatch($appointmentInquiry);
        \App\Jobs\SendAppointmentNotificationJob::dispatch($appointmentInquiry);

        \App\Models\LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => $request->user()?->id,
            'activity_type' => 'converted_from_inquiry',
            'description' => "Inquiry #{$inquiry->id} converted to confirmed Appointment scheduled for {$lead->preferred_date->format('M d, Y')}.",
        ]);

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'module' => 'inquiries',
            'action' => 'converted_to_appointment',
            'record_id' => $inquiry->id,
            'notes' => "Converted inquiry #{$inquiry->id} ({$inquiry->name}) into appointment #{$lead->id}",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inquiry successfully converted to Appointment! Confirmation emails dispatched to patient and admin.',
            'data' => [
                'lead_id' => $lead->id,
                'inquiry_id' => $inquiry->id,
            ],
        ]);
    }

    public function submitInquiry(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:3000',
            'service_id' => 'nullable|exists:services,id',
            'service_name' => 'nullable|string|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['type'] = 'contact';
        $inquiry = $this->inquiryService->createInquiry($data, $request->ip(), $request->userAgent());

        return response()->json([
            'success' => true,
            'message' => 'Thank you for reaching out! We have received your inquiry and will contact you shortly.',
            'data' => [
                'id' => $inquiry->id,
                'name' => $inquiry->name,
                'email' => $inquiry->email,
            ],
        ], 201);
    }

    public function bookAppointment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'service_id' => 'nullable|exists:services,id',
            'service_name' => 'nullable|string|max:150',
            'preferred_date' => 'nullable|date',
            'preferred_time' => 'nullable|string|max:50',
            'message' => 'nullable|string|max:3000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['type'] = 'appointment';
        $inquiry = $this->inquiryService->createInquiry($data, $request->ip(), $request->userAgent());

        return response()->json([
            'success' => true,
            'message' => 'Your appointment request has been submitted successfully. Our concierge will contact you shortly to confirm.',
            'data' => [
                'id' => $inquiry->id,
                'name' => $inquiry->name,
                'email' => $inquiry->email,
            ],
        ], 201);
    }
}
