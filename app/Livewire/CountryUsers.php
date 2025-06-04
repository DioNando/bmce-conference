<?php

namespace App\Livewire;

use App\Models\Country;
use App\Models\Organization;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class CountryUsers extends Component
{
    use WithPagination;

    public $countryCode = null;
    public $countryName = null;
    public $userCount = 0;
    public $totalUsers = 0;
    public $percentage = 0;
    public $investorCount = 0;
    public $issuerCount = 0;
    public $organizationCount = 0;
    public $topOrganizations = [];

    public $isLoading = false;

    protected $listeners = [
        'countrySelected' => 'updateSelectedCountry',
        'showLoading' => 'showLoading',
    ];

    public function showLoading()
    {
        $this->isLoading = true;
    }

    public function updateSelectedCountry($code)
    {
        $this->isLoading = true;
        $this->countryCode = $code;
        $country = Country::where('code', $code)->first();

        if ($country) {
            $this->countryName = app()->getLocale() === 'fr' ? $country->name_fr : $country->name_en;

            // Récupérer les organisations du pays
            $organizations = Organization::where('country_id', $country->id)->get();
            $organizationIds = $organizations->pluck('id')->toArray();
            $this->organizationCount = count($organizationIds);

            // Récupérer les utilisateurs des organisations de ce pays
            $users = User::whereIn('organization_id', $organizationIds)->with(['roles']);
            $this->userCount = $users->count();
            $this->totalUsers = User::count();
            $this->percentage = $this->totalUsers > 0 ? round(($this->userCount / $this->totalUsers) * 100) : 0;

            // Compter les investisseurs et émetteurs
            $usersWithRoles = $users->get();
            $this->investorCount = $usersWithRoles->filter(function($user) {
                return $user->hasRole('investor');
            })->count();

            $this->issuerCount = $usersWithRoles->filter(function($user) {
                return $user->hasRole('issuer');
            })->count();

            // Trouver les 3 organisations avec le plus d'utilisateurs
            $this->topOrganizations = Organization::where('country_id', $country->id)
                ->withCount('users')
                ->orderBy('users_count', 'desc')
                ->take(3)
                ->get()
                ->map(function($org) {
                    return [
                        'name' => $org->name,
                        'count' => $org->users_count,
                        'type' => $org->type ? $org->type->label() : null
                    ];
                });
        } else {
            $this->countryName = $code;
            $this->userCount = 0;
            $this->percentage = 0;
            $this->investorCount = 0;
            $this->issuerCount = 0;
            $this->organizationCount = 0;
            $this->topOrganizations = [];
        }

        $this->resetPage();
        $this->isLoading = false;
    }

    public function render()
    {
        $users = collect();

        if ($this->countryCode) {
            $country = Country::where('code', $this->countryCode)->first();

            if ($country) {
                $organizationIds = Organization::where('country_id', $country->id)->pluck('id')->toArray();
                $users = User::whereIn('organization_id', $organizationIds)
                    ->with(['organization', 'roles'])
                    ->get();
            }
        }

        return view('livewire.country-users', [
            'users' => $users
        ]);
    }
}
