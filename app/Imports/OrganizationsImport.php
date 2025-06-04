<?php

namespace App\Imports;

use App\Models\Organization;
use App\Models\Country;
use App\Enums\Origin;
use App\Enums\OrganizationType;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class OrganizationsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Find or create country
        $country = null;
        if (!empty($row['country'])) {
            $country = Country::where('name_en', $row['country'])
                      ->orWhere('name_fr', $row['country'])
                      ->first();
        }

        // Parse profile
        $profile = strtolower($row['profile'] ?? '');
        if (!in_array($profile, ['issuer', 'investor'])) {
            $profile = null;
        }

        // Parse origin
        $origin = strtolower($row['origin'] ?? '');
        $originEnum = null;
        if ($origin === 'national') {
            $originEnum = Origin::NATIONAL->value;
        } elseif ($origin === 'foreign' || $origin === 'international') {
            $originEnum = Origin::FOREIGN->value;
        }

        // Parse organization type
        $organizationType = strtolower($row['organization_type'] ?? '');
        $orgTypeEnum = null;
        $orgTypeOther = null;

        // Match organization type with enum values
        foreach (OrganizationType::cases() as $type) {
            if (strtolower($type->englishLabel()) === $organizationType) {
                $orgTypeEnum = $type->value;
                break;
            }
        }

        // If not matched with any enum, set as "autre"
        if (!$orgTypeEnum && !empty($organizationType)) {
            $orgTypeEnum = OrganizationType::AUTRE->value;
            $orgTypeOther = $row['organization_type'];
        }

        // Create the organization
        return new Organization([
            'name' => $row['name'],
            'profil' => $profile,
            'origin' => $originEnum,
            'organization_type' => $orgTypeEnum,
            'organization_type_other' => $orgTypeOther,
            'country_id' => $country ? $country->id : null,
            'description' => $row['description'] ?? null,
        ]);
    }

    /**
     * Validation rules for the import
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('organizations', 'name')
            ],
            'profile' => 'nullable|string',
            'origin' => 'nullable|string',
            'organization_type' => 'nullable|string',
            'country' => 'nullable|string',
            'description' => 'nullable|string',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'name.required' => 'The organization name is required.',
            'name.unique' => 'An organization with this name already exists.',
        ];
    }
}
