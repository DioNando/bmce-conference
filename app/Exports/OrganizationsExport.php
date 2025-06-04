<?php

namespace App\Exports;

use App\Models\Organization;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrganizationsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $profile = null;
    protected $origin = null;
    protected $type = null;
    protected $country = null;
    protected $search = null;

    public function __construct($profile = null, $origin = null, $type = null, $country = null, $search = null)
    {
        $this->profile = $profile;
        $this->origin = $origin;
        $this->type = $type;
        $this->country = $country;
        $this->search = $search;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Organization::query()->with(['country']);

        // Filter by profile
        if ($this->profile && $this->profile !== 'all') {
            $query->where('profil', $this->profile);
        }

        // Filter by origin
        if ($this->origin && $this->origin !== 'all') {
            $query->where('origin', $this->origin);
        }

        // Filter by organization type
        if ($this->type && $this->type !== 'all') {
            $query->where('organization_type', $this->type);
        }

        // Filter by country
        if ($this->country && $this->country !== 'all') {
            $query->where('country_id', $this->country);
        }

        // Search by name or description
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        return $query->get();
    }

    /**
     * @var Organization $organization
     */
    public function map($organization): array
    {
        return [
            $organization->id,
            $organization->name,
            $organization->profil ? $organization->profil->label() : '',
            $organization->origin ? $organization->origin->label() : '',
            $organization->organization_type ? $organization->organization_type->englishLabel() : '',
            $organization->organization_type_other ?? '',
            $organization->country ? $organization->country->name_en : '',
            $organization->fiche_bkgr ? 'Yes' : 'No',
            $organization->logo ? 'Yes' : 'No',
            $organization->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Profile',
            'Origin',
            'Organization Type',
            'Other Organization Type',
            'Country',
            'Has BKGR Fiche',
            'Has Logo',
            'Created At',
        ];
    }
}
