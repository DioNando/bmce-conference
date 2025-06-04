<?php

namespace App\Http\Controllers\Investor;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IssuerController extends Controller
{
    /**
     * Display a listing of all issuers as cards.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = User::whereHas('roles', function($query) {
                        $query->where('name', UserRole::ISSUER->value);
                    })
                    ->with('organization', 'organization.country')
                    ->where('status', true);

        // Filter by organization type
        $orgType = $request->input('org_type');
        if ($orgType && $orgType !== 'all') {
            $query->whereHas('organization', function($q) use ($orgType) {
                $q->where('organization_type', $orgType);
            });
        }

        // Filter by country
        $country = $request->input('country');
        if ($country && $country !== 'all') {
            $query->whereHas('organization', function($q) use ($country) {
                $q->where('country_id', $country);
            });
        }

        // Filter by origin
        $origin = $request->input('origin');
        if ($origin && $origin !== 'all') {
            $query->whereHas('organization', function($q) use ($origin) {
                $q->where('origin', $origin);
            });
        }

        // Search for issuers
        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('organization', function ($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $issuers = $query->orderBy('name')->get();

        // Get countries and organization types for filter dropdowns
        $countries = Country::orderBy('name_en')->get();

        return view('investor.issuers.index', compact('issuers', 'countries'));
    }

    /**
     * Display the specified issuer details.
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->hasRole(UserRole::INVESTOR->value)) {
            return redirect()->route('home')
                            ->with('error', 'You do not have permission to access this page.');
        }

        $issuer = User::whereHas('roles', function($query) {
                        $query->where('name', UserRole::ISSUER->value);
                    })
                    ->with('organization', 'organization.country', 'timeSlots')
                    ->where('status', true)
                    ->findOrFail($id);

        // Get available time slots for this issuer
        $availableTimeSlots = $issuer->timeSlots()
                                     ->where('availability', true)
                                     ->orderBy('start_time')
                                     ->get();

        return view('investor.issuers.show', compact('issuer', 'availableTimeSlots'));
    }
}
