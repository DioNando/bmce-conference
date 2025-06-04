<?php

namespace Database\Seeders;

use App\Enums\OrganizationType;
use App\Enums\Origin;
use App\Enums\UserRole;
use App\Models\Country;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les pays par leur nom anglais
        $morocco = Country::where('name_en', 'Morocco')->first();
        $france = Country::where('name_en', 'France')->first();
        $usa = Country::where('name_en', 'United States')->first();
        $uk = Country::where('name_en', 'United Kingdom')->first();
        $germany = Country::where('name_en', 'Germany')->first();
        $spain = Country::where('name_en', 'Spain')->first();
        $switzerland = Country::where('name_en', 'Switzerland')->first();
        $canada = Country::where('name_en', 'Canada')->first();

        // Liste des organisations additionnelles (10)
        $organizations = [
            // 1. Banque marocaine
            [
                'name' => 'Crédit Agricole du Maroc',
                'profil' => UserRole::ISSUER->value,
                'origin' => Origin::NATIONAL->value,
                'organization_type' => OrganizationType::BANQUE->value,
                'country_id' => $morocco->id,
                'description' => 'Bank specializing in agricultural financing and rural development',
            ],

            // 2. Compagnie d'assurance marocaine
            [
                'name' => 'Wafa Assurance',
                'profil' => UserRole::ISSUER->value,
                'origin' => Origin::NATIONAL->value,
                'organization_type' => OrganizationType::ASSURANCE->value,
                'country_id' => $morocco->id,
                'description' => 'Leading insurance company in Morocco',
            ],

            // 3. Société industrielle marocaine
            [
                'name' => 'Centrale Danone Maroc',
                'profil' => UserRole::ISSUER->value,
                'origin' => Origin::FOREIGN->value,
                'organization_type' => OrganizationType::AUTRE->value,
                'organization_type_other' => 'Food Industry',
                'country_id' => $morocco->id,
                'description' => 'Dairy products manufacturer in Morocco',
            ],

            // 4. Fonds d'investissement britannique
            [
                'name' => 'Schroders Investment Management',
                'profil' => UserRole::INVESTOR->value,
                'origin' => Origin::FOREIGN->value,
                'organization_type' => OrganizationType::FONDS_INVESTISSEMENT->value,
                'country_id' => $uk->id,
                'description' => 'Global investment manager headquartered in London',
            ],

            // 5. Caisse de retraite canadienne
            [
                'name' => 'Canada Pension Plan Investment Board',
                'profil' => UserRole::INVESTOR->value,
                'origin' => Origin::FOREIGN->value,
                'organization_type' => OrganizationType::CAISSE_RETRAITE->value,
                'country_id' => $canada->id,
                'description' => 'Canada\'s largest pension fund manager',
            ],

            // 6. Banque d'investissement américaine
            [
                'name' => 'Goldman Sachs',
                'profil' => UserRole::INVESTOR->value,
                'origin' => Origin::FOREIGN->value,
                'organization_type' => OrganizationType::BANQUE->value,
                'country_id' => $usa->id,
                'description' => 'Global investment banking and securities firm',
            ],

            // 7. Gestion sous mandat allemande
            [
                'name' => 'Deutsche Asset Management',
                'profil' => UserRole::INVESTOR->value,
                'origin' => Origin::FOREIGN->value,
                'organization_type' => OrganizationType::GESTION_SOUS_MANDAT->value,
                'country_id' => $germany->id,
                'description' => 'Asset management division of Deutsche Bank',
            ],

            // 8. OPCVM français
            [
                'name' => 'Carmignac Gestion',
                'profil' => UserRole::INVESTOR->value,
                'origin' => Origin::FOREIGN->value,
                'organization_type' => OrganizationType::OPCVM->value,
                'country_id' => $france->id,
                'description' => 'Independent asset management company based in Paris',
            ],

            // 9. Entreprise énergétique marocaine
            [
                'name' => 'TAQA Morocco',
                'profil' => UserRole::ISSUER->value,
                'origin' => Origin::NATIONAL->value,
                'organization_type' => OrganizationType::AUTRE->value,
                'organization_type_other' => 'Energy',
                'country_id' => $morocco->id,
                'description' => 'Independent electricity producer in Morocco',
            ],

            // 10. Banque d'investissement suisse
            [
                'name' => 'UBS Asset Management',
                'profil' => UserRole::INVESTOR->value,
                'origin' => Origin::FOREIGN->value,
                'organization_type' => OrganizationType::BANQUE->value,
                'country_id' => $switzerland->id,
                'description' => 'Swiss global financial services company',
            ],
        ];

        // Création des organisations dans la base de données
        foreach ($organizations as $organizationData) {
            Organization::create($organizationData);
        }
    }
}
