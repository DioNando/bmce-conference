<?php

namespace Database\Seeders;

use App\Enums\OrganizationType;
use App\Enums\Origin;
use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\User;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
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
        $spain = Country::where('name_en', 'Spain')->first();
        $china = Country::where('name_en', 'China')->first();
        $australia = Country::where('name_en', 'Australia')->first();

        // Create administrators
        $administrators = [
            [
                'first_name' => 'Karim',
                'name' => 'Benzaoui',
                'email' => 'karim@bmceinvest.ma',
                'password' => Hash::make('password'),
                'phone' => '+212 600000010',
                'position' => 'Platform Administrator',
                'status' => true,
            ],
            [
                'first_name' => 'Laila',
                'name' => 'Bensouda',
                'email' => 'laila@bmceinvest.ma',
                'password' => Hash::make('password'),
                'phone' => '+212 600000011',
                'position' => 'Senior Administrator',
                'status' => true,
            ],
            [
                'first_name' => 'Rachid',
                'name' => 'Ouazzani',
                'email' => 'rachid@bmceinvest.ma',
                'password' => Hash::make('password'),
                'phone' => '+212 600000012',
                'position' => 'Technical Administrator',
                'status' => true,
            ],
        ];

        foreach ($administrators as $adminData) {
            $user = User::create($adminData);
            $user->assignRole(UserRole::ADMIN->value);
        }

        // Create issuers
        $issuers = [
            [
                'user' => [
                    'first_name' => 'John',
                    'name' => 'Smith',
                    'email' => 'john@bmcecapital.com',
                    'password' => Hash::make('password'),
                    'phone' => '+212 600000001',
                    'position' => 'CFO',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'BMCE Bank',
                    'profil' => UserRole::ISSUER->value,
                    'origin' => Origin::NATIONAL->value,
                    'organization_type' => OrganizationType::BANQUE->value,
                    'country_id' => $morocco->id,
                    'description' => 'Leading banking institution in Morocco',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'Xiaoming',
                    'name' => 'Zhang',
                    'email' => 'xiaoming@chinabank.cn',
                    'password' => Hash::make('password'),
                    'phone' => '+86 1358889999',
                    'position' => 'International Finance Director',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'China Industrial Bank',
                    'profil' => UserRole::ISSUER->value,
                    'origin' => Origin::FOREIGN->value,
                    'organization_type' => OrganizationType::BANQUE->value,
                    'country_id' => $china->id,
                    'description' => 'Major financial institution in China with global operations',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'Carlos',
                    'name' => 'Rodriguez',
                    'email' => 'carlos@santander.es',
                    'password' => Hash::make('password'),
                    'phone' => '+34 612345678',
                    'position' => 'Head of International Markets',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'Banco Santander',
                    'profil' => UserRole::ISSUER->value,
                    'origin' => Origin::FOREIGN->value,
                    'organization_type' => OrganizationType::BANQUE->value,
                    'country_id' => $spain->id,
                    'description' => 'Spanish multinational banking group',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'Elena',
                    'name' => 'Martinez',
                    'email' => 'elena@iberdrola.es',
                    'password' => Hash::make('password'),
                    'phone' => '+34 698765432',
                    'position' => 'Finance Executive',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'Iberdrola',
                    'profil' => UserRole::ISSUER->value,
                    'origin' => Origin::FOREIGN->value,
                    'organization_type' => OrganizationType::AUTRE->value,
                    'organization_type_other' => 'Energy',
                    'country_id' => $spain->id,
                    'description' => 'Spanish multinational electric utility company',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'Sara',
                    'name' => 'Alaoui',
                    'email' => 'sara@attijariwafabank.com',
                    'password' => Hash::make('password'),
                    'phone' => '+212 600000002',
                    'position' => 'Head of Investor Relations',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'Attijariwafa Bank',
                    'profil' => UserRole::ISSUER->value,
                    'origin' => Origin::NATIONAL->value,
                    'organization_type' => OrganizationType::BANQUE->value,
                    'country_id' => $morocco->id,
                    'description' => 'One of the largest commercial banks in Morocco',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'Mohammed',
                    'name' => 'Tazi',
                    'email' => 'mohammed@maroctelecom.ma',
                    'password' => Hash::make('password'),
                    'phone' => '+212 600000003',
                    'position' => 'Financial Director',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'Maroc Telecom',
                    'profil' => UserRole::ISSUER->value,
                    'origin' => Origin::NATIONAL->value,
                    'organization_type' => OrganizationType::AUTRE->value,
                    'organization_type_other' => 'Telecom',
                    'country_id' => $morocco->id,
                    'description' => 'Telecommunications company',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'Ahmed',
                    'name' => 'Bennani',
                    'email' => 'ahmed@cosumar.ma',
                    'password' => Hash::make('password'),
                    'phone' => '+212 600000021',
                    'position' => 'Financial Director',
                    'status' => false,
                ],
                'organization' => [
                    'name' => 'Cosumar',
                    'profil' => UserRole::ISSUER->value,
                    'origin' => Origin::NATIONAL->value,
                    'organization_type' => OrganizationType::AUTRE->value,
                    'organization_type_other' => 'Agribusiness',
                    'country_id' => $morocco->id,
                    'description' => 'Sugar producer and exporter',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'Kamal',
                    'name' => 'Hassan',
                    'email' => 'kamal@lafarge.ma',
                    'password' => Hash::make('password'),
                    'phone' => '+212 600000022',
                    'position' => 'Treasury Director',
                    'status' => false,
                ],
                'organization' => [
                    'name' => 'Lafarge Maroc',
                    'profil' => UserRole::ISSUER->value,
                    'origin' => Origin::FOREIGN->value,
                    'organization_type' => OrganizationType::AUTRE->value,
                    'organization_type_other' => 'Construction Materials',
                    'country_id' => $morocco->id,
                    'description' => 'Building materials company',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'Samira',
                    'name' => 'El Mansouri',
                    'email' => 'samira@ocpgroup.ma',
                    'password' => Hash::make('password'),
                    'phone' => '+212 600000023',
                    'position' => 'Head of Finance',
                    'status' => false,
                ],
                'organization' => [
                    'name' => 'OCP Group',
                    'profil' => UserRole::ISSUER->value,
                    'origin' => Origin::NATIONAL->value,
                    'organization_type' => OrganizationType::AUTRE->value,
                    'organization_type_other' => 'Phosphates & Fertilizers',
                    'country_id' => $morocco->id,
                    'description' => 'Phosphate mining and fertilizer producer',
                ]
            ],
        ];

        foreach ($issuers as $issuerData) {
            // Créer d'abord l'organisation
            $organization = Organization::create($issuerData['organization']);

            // Créer l'utilisateur avec une référence à l'organisation
            $user = User::create(array_merge(
                $issuerData['user'],
                ['organization_id' => $organization->id]
            ));

            $user->assignRole(UserRole::ISSUER->value);
        }

        // Create investors
        $investors = [
            [
                'user' => [
                    'first_name' => 'Fatima',
                    'name' => 'Zahra',
                    'email' => 'fatima@cimr.ma',
                    'password' => Hash::make('password'),
                    'phone' => '+212 600000004',
                    'position' => 'Investment Manager',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'CIMR',
                    'profil' => UserRole::INVESTOR->value,
                    'origin' => Origin::NATIONAL->value,
                    'organization_type' => OrganizationType::CAISSE_RETRAITE->value,
                    'country_id' => $morocco->id,
                    'description' => 'Pension fund',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'Pierre',
                    'name' => 'Dubois',
                    'email' => 'pierre@amundi.fr',
                    'password' => Hash::make('password'),
                    'phone' => '+33 123456789',
                    'position' => 'Portfolio Manager',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'Asset Management',
                    'profil' => UserRole::INVESTOR->value,
                    'origin' => Origin::NATIONAL->value,
                    'organization_type' => OrganizationType::OPCVM->value,
                    'country_id' => $france->id,
                    'description' => 'European asset management company',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'Wei',
                    'name' => 'Zhang',
                    'email' => 'wei.zhang@cic.cn',
                    'password' => Hash::make('password'),
                    'phone' => '+86 1381234567',
                    'position' => 'Chief Investment Officer',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'China Investment Corporation',
                    'profil' => UserRole::INVESTOR->value,
                    'origin' => Origin::FOREIGN->value,
                    'organization_type' => OrganizationType::FONDS_INVESTISSEMENT->value,
                    'country_id' => $china->id,
                    'description' => 'Chinese sovereign wealth fund',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'Li',
                    'name' => 'Chen',
                    'email' => 'li.chen@ccb.cn',
                    'password' => Hash::make('password'),
                    'phone' => '+86 1391234567',
                    'position' => 'International Investment Director',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'China Construction Bank',
                    'profil' => UserRole::INVESTOR->value,
                    'origin' => Origin::FOREIGN->value,
                    'organization_type' => OrganizationType::BANQUE->value,
                    'country_id' => $china->id,
                    'description' => 'One of the "Big Four" banks in China',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'James',
                    'name' => 'Cooper',
                    'email' => 'james@australiansuper.com.au',
                    'password' => Hash::make('password'),
                    'phone' => '+61 412345678',
                    'position' => 'International Markets Director',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'AustralianSuper',
                    'profil' => UserRole::INVESTOR->value,
                    'origin' => Origin::FOREIGN->value,
                    'organization_type' => OrganizationType::CAISSE_RETRAITE->value,
                    'country_id' => $australia->id,
                    'description' => 'Australia\'s largest superannuation and pension fund',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'Jing',
                    'name' => 'Wang',
                    'email' => 'jing.wang@chinaamc.com',
                    'password' => Hash::make('password'),
                    'phone' => '+86 1351234567',
                    'position' => 'Global Markets Analyst',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'China Asset Management Co',
                    'profil' => UserRole::INVESTOR->value,
                    'origin' => Origin::FOREIGN->value,
                    'organization_type' => OrganizationType::OPCVM->value,
                    'country_id' => $china->id,
                    'description' => 'Leading asset management company in China',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'Michael',
                    'name' => 'Johnson',
                    'email' => 'michael@blackrock.com',
                    'password' => Hash::make('password'),
                    'phone' => '+1 2125551234',
                    'position' => 'Senior Investment Officer',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'BlackRock',
                    'profil' => UserRole::INVESTOR->value,
                    'origin' => Origin::NATIONAL->value,
                    'organization_type' => OrganizationType::FONDS_INVESTISSEMENT->value,
                    'country_id' => $usa->id,
                    'description' => 'Global investment management corporation',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'Antonio',
                    'name' => 'Fernandez',
                    'email' => 'antonio@bbva.es',
                    'password' => Hash::make('password'),
                    'phone' => '+34 912345678',
                    'position' => 'Fixed Income Portfolio Manager',
                    'status' => true,
                ],
                'organization' => [
                    'name' => 'BBVA Asset Management',
                    'profil' => UserRole::INVESTOR->value,
                    'origin' => Origin::FOREIGN->value,
                    'organization_type' => OrganizationType::OPCVM->value,
                    'country_id' => $spain->id,
                    'description' => 'Spanish investment management company',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'Omar',
                    'name' => 'Benjelloun',
                    'email' => 'omar@cmr.gov.ma',
                    'password' => Hash::make('password'),
                    'phone' => '+212 600000024',
                    'position' => 'Fixed Income Manager',
                    'status' => false,
                ],
                'organization' => [
                    'name' => 'CMR',
                    'profil' => UserRole::INVESTOR->value,
                    'origin' => Origin::NATIONAL->value,
                    'organization_type' => OrganizationType::CAISSE_RETRAITE->value,
                    'country_id' => $morocco->id,
                    'description' => 'Moroccan pension fund',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'Sophie',
                    'name' => 'Martin',
                    'email' => 'sophie@bnpparibas.fr',
                    'password' => Hash::make('password'),
                    'phone' => '+33 678901234',
                    'position' => 'Senior Investment Analyst',
                    'status' => false,
                ],
                'organization' => [
                    'name' => 'BNP Paribas Asset Management',
                    'profil' => UserRole::INVESTOR->value,
                    'origin' => Origin::FOREIGN->value,
                    'organization_type' => OrganizationType::OPCVM->value,
                    'country_id' => $france->id,
                    'description' => 'French asset management company',
                ]
            ],
            [
                'user' => [
                    'first_name' => 'David',
                    'name' => 'Wilson',
                    'email' => 'david@fidelity.com',
                    'password' => Hash::make('password'),
                    'phone' => '+1 6175551234',
                    'position' => 'Investment Director',
                    'status' => false,
                ],
                'organization' => [
                    'name' => 'Fidelity Investments',
                    'profil' => UserRole::INVESTOR->value,
                    'origin' => Origin::FOREIGN->value,
                    'organization_type' => OrganizationType::FONDS_INVESTISSEMENT->value,
                    'country_id' => $usa->id,
                    'description' => 'American multinational financial services corporation',
                ]
            ],
        ];

        foreach ($investors as $investorData) {
            // Créer d'abord l'organisation
            $organization = Organization::create($investorData['organization']);

            // Créer l'utilisateur avec une référence à l'organisation
            $user = User::create(array_merge(
                $investorData['user'],
                ['organization_id' => $organization->id]
            ));

            $user->assignRole(UserRole::INVESTOR->value);
        }
    }
}
