<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $role = null;
    protected $status = null;
    protected $search = null;

    public function __construct($role = null, $status = null, $search = null)
    {
        $this->role = $role;
        $this->status = $status;
        $this->search = $search;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = User::query()->with(['roles', 'organization']);

        // Filter by role (profile)
        if ($this->role && $this->role !== 'all') {
            $query->whereHas('roles', function ($q) {
                $q->where('name', $this->role);
            });
        }

        // Filter by status
        if ($this->status !== null && $this->status !== 'all') {
            $query->where('status', $this->status);
        }

        // Search by name, first_name, email or phone
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('first_name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%")
                  ->orWhereHas('organization', function ($org) {
                      $org->where('name', 'like', "%{$this->search}%");
                  });
            });
        }

        return $query->get();
    }

    /**
     * @var User $user
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->first_name,
            $user->email,
            $user->phone ?? '',
            $user->position ?? '',
            $user->roles->first() ? $user->roles->first()->name : '',
            $user->status ? 'Active' : 'Inactive',
            $user->organization ? $user->organization->name : '',
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Last Name',
            'First Name',
            'Email',
            'Phone',
            'Position',
            'Profile',
            'Status',
            'Organization',
            'Created At',
        ];
    }
}
