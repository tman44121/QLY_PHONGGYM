<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Repositories\CheckInRepository;
use App\Repositories\MembershipRepository;
use App\Services\CheckInService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffCheckInController extends Controller
{
    public function index(Request $request, CheckInRepository $checkInRepository, MembershipRepository $membershipRepository): View
    {
        $visits = $checkInRepository->staffHistory();
        $selectedMember = $request->validate([
            'member' => ['nullable', 'string', 'max:100'],
        ])['member'] ?? '';
        $memberPreview = null;

        if ($selectedMember !== '') {
            $customer = $checkInRepository->findCustomerByIdentifier($selectedMember);
            $memberPreview = [
                'customer' => $customer,
                'membership' => $customer
                    ? $membershipRepository->activeFor($customer, now(config('app.timezone'))->toDateString())
                    : null,
                'hasOpenVisit' => $customer ? $checkInRepository->hasOpenVisit($customer) : false,
            ];
        }

        return view('staff.checkins.index', [
            'visits' => $visits,
            'selectedMember' => $selectedMember,
            'memberPreview' => $memberPreview,
        ]);
    }

    public function store(Request $request, CheckInService $service): RedirectResponse
    {
        $validated = $request->validate([
            'member' => ['required', 'string', 'max:100'],
        ]);

        $service->checkIn($validated['member']);

        return redirect()->route('staff.checkins.index')->with('success', 'Đã ghi nhận hội viên vào tập.');
    }

    public function checkOut(Request $request, CheckIn $visit, CheckInService $service): RedirectResponse
    {
        $service->checkOut($visit, $request->user());

        return redirect()->route('staff.checkins.index')->with('success', 'Đã ghi nhận giờ ra.');
    }

    public function manualClose(Request $request, CheckIn $visit, CheckInService $service): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $service->manualClose($visit, $request->user(), $validated['reason']);

        return redirect()->route('staff.checkins.index')->with('success', 'Đã đóng lượt tập kèm lý do.');
    }
}
