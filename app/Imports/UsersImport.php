<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Organization;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    // Stockage des organisations pour éviter des requêtes répétitives
    protected $cachedOrganizations = [];

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Rechercher l'organisation par son nom
        $organization_id = null;

        if (!empty($row['organization'])) {
            $orgName = trim($row['organization']);

            // Utiliser le cache si l'organisation a déjà été recherchée
            if (!isset($this->cachedOrganizations[$orgName])) {
                $organization = Organization::where('name', $orgName)->first();
                $this->cachedOrganizations[$orgName] = $organization ? $organization->id : null;
            }

            $organization_id = $this->cachedOrganizations[$orgName];
            // Si l'organisation n'existe pas, on continue avec null
        }

        // Créer l'utilisateur
        $user = User::create([
            'name' => $row['last_name'],
            'first_name' => $row['first_name'],
            'email' => $row['email'],
            'password' => Hash::make($row['password'] ?? 'password123'),
            'phone' => $row['phone'] ?? null,
            'position' => $row['position'] ?? null,
            'organization_id' => $organization_id,
            'status' => $this->parseStatus($row['status'] ?? 'inactive'),
        ]);

        // Assigner le rôle
        if (!empty($row['profile'])) {
            $role = Role::where('name', strtolower($row['profile']))->first();
            if ($role) {
                $user->assignRole($role);
            }
        }

        return $user;
    }

    /**
     * Parse le statut à partir du texte
     */
    protected function parseStatus($status)
    {
        if (in_array(strtolower($status), ['active', 'actif', '1', 'true', 'yes', 'oui'])) {
            return true;
        }
        return false;
    }

    /**
     * Règles de validation pour l'importation
     */
    public function rules(): array
    {
        return [
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')
            ],
            'profile' => 'nullable|string|in:issuer,investor',
            'status' => 'nullable|string',
            'organization' => 'nullable|string',
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function customValidationMessages()
    {
        return [
            'email.unique' => 'L\'adresse email :input est déjà utilisée par un autre utilisateur.',
            'last_name.required' => 'Le nom de famille est requis.',
            'first_name.required' => 'Le prénom est requis.',
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'profile.in' => 'Le profil doit être soit "issuer" soit "investor".',
        ];
    }
}
