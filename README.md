# BMCE Capital Investors Conference 2025 Platform

**Current Date:** 2025-04-30
**Project Author:** DioNando

## Description
    
This web platform is designed for the BMCE Capital Investors Conference taking place on June 12-13, 2025. The solution enables efficient scheduling and management of meetings between institutional investors and listed companies (issuers). The platform features a paperless experience with online planning and profile management.

### Key Features

- **Authentication System:** Separate interfaces for investors and issuers
- **Meeting Scheduling:** Interactive calendar with 45-minute meeting slots
- **Q&A Functionality:** Investors can submit questions to issuers before meetings
- **Admin Back-Office:** Interface for managing users and meetings
- **Fully Responsive Design:** Optimized for desktop, tablet, and mobile devices
- **English-Only Interface:** International accessibility

## Technical Stack

- **Backend:** Laravel 10+
- **Frontend:** Tailwind CSS for styling
- **Support:** Alpine.js for lightweight JavaScript functionality
- **Database:** MySQL/PostgreSQL
- **Authentication:** Laravel Breeze
- **Authorization:** Spatie Laravel Permission

## Project Structure

```
bmce-invest-2025/
├── app/
│   ├── Enums/
│   │   ├── InvestorStatus.php
│   │   ├── MeetingStatus.php
│   │   ├── OrganizationType.php
│   │   ├── Origin.php
│   │   ├── Status.php
│   │   └── UserRole.php
│   ├── Exports/
│   │   ├── MeetingsExport.php
│   │   ├── OrganizationsExport.php
│   │   └── UsersExport.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── MeetingController.php
│   │   │   │   └── OrganizationController.php
│   │   │   ├── Investor/
│   │   │   │   └── MeetingController.php
│   │   │   ├── Issuer/
│   │   │   │   └── MeetingController.php
│   │   │   └── MeetingController.php
│   │   ├── Middlewares/
│   │   │   └── RoleMiddleware.php
│   │   └── Requests/
│   ├── Imports/
│   │   └── UsersImport.php
│   ├── Mail/
│   │   └── ActivationEmail.php
│   ├── Models/
│   │   ├── Country.php
│   │   ├── Meeting.php
│   │   ├── MeetingInvestor.php
│   │   ├── Organization.php
│   │   ├── Question.php
│   │   ├── Room.php
│   │   ├── TimeSlot.php
│   │   └── User.php
│   ├── Observers/
│   │   └── UserObserver.php
│   └── Providers/
│       └── AppServiceProvider.php
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 2025_04_28_113706_create_countries_table.php
│   │   ├── 2025_04_28_113706_create_organizations_table.php
│   │   ├── 2025_04_28_113707_create_users_table.php
│   │   ├── 2025_04_28_113708_create_rooms_table.php
│   │   ├── 2025_04_28_113711_create_time_slots_table.php
│   │   ├── 2025_04_28_113712_create_meetings_table.php
│   │   ├── 2025_04_28_113713_create_meeting_investors_table.php
│   │   ├── 2025_04_28_120039_create_questions_table.php
│   │   └── 2025_04_28_142241_create_permission_tables.php
│   ├── seeders/
│   │   ├── CountrySeeder.php
│   │   ├── MeetingSeeder.php
│   │   ├── RoleSeeder.php
│   │   └── UserSeeder.php
│   └── diagrams/
│       └── class_diagram.md
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   │   ├── meetings/
│   │   │   └── organizations/
│   │   ├── investor/
│   │   │   └── meetings/
│   │   └── issuer/
│   │       └── meetings/
│   └── ...
├── routes/
│   ├── auth.php
│   └── web.php
└── ...
```

## Database Models and Relationships

### Entities

1. **User**
   - Attributes: `id`, `name`, `first_name`, `email`, `password`, `phone`, `position`, `organization_id`, `status`
   - Représente les investisseurs, émetteurs et administrateurs avec distinction basée sur les rôles
   - Participe aux réunions en tant qu'investisseur ou émetteur
   - Relations: `organization`, `issuerMeetings`, `investorMeetings`, `createdMeetings`, `updatedMeetings`, `questions`, `timeSlots`

2. **Organization**
   - Attributes: `id`, `name`, `origin`, `profil`, `organization_type`, `organization_type_other`, `fiche_bkgr`, `logo`, `country_id`, `description`
   - Représente les institutions d'investissement et les sociétés émettrices
   - Relations: `users`, `country`

3. **Country**
   - Attributes: `id`, `name_en`, `code`
   - Représente les pays d'origine des organisations
   - Relations: `organizations`

4. **TimeSlot**
   - Attributes: `id`, `user_id`, `date`, `start_time`, `end_time`, `availability`
   - Représente les créneaux horaires disponibles pour les réunions
   - L'indicateur `availability` indique si le créneau est disponible pour la réservation
   - Relations: `meetings`, `user`

5. **Room**
   - Attributes: `id`, `name`, `capacity`, `location`
   - Emplacements physiques où se déroulent les réunions
   - Relations: `meetings`

6. **Meeting**
   - Attributes: `id`, `room_id`, `time_slot_id`, `issuer_id`, `created_by_id`, `updated_by_id`, `status`, `notes`, `is_one_on_one`
   - Entité centrale qui relie les créneaux horaires, les salles, les émetteurs et les investisseurs
   - Le statut peut être SCHEDULED, PENDING, CONFIRMED, DECLINED, CANCELLED, COMPLETED
   - Relations: `room`, `timeSlot`, `issuer`, `investors`, `meetingInvestors`, `questions`, `createdBy`, `updatedBy`

7. **MeetingInvestor**
   - Attributes: `id`, `meeting_id`, `investor_id`, `status`
   - Table pivot reliant les réunions et les investisseurs
   - Permet à plusieurs investisseurs de participer à une réunion avec un statut individuel
   - Relations: `meeting`, `investor`

8. **Question**
   - Attributes: `id`, `meeting_id`, `investor_id`, `question`, `is_answered`, `response`, `answered_at`
   - Questions soumises par les investisseurs avant les réunions
   - Relations: `meeting`, `investor`

### Entity Relationships

```
User "1" --- "1" Organization : appartient à
Organization "many" --- "1" Country : basée dans
User "1" --- "many" TimeSlot : possède
TimeSlot "1" --- "many" Meeting : fournit du temps pour
Room "1" --- "many" Meeting : héberge
User "1" --- "many" Meeting : participe en tant qu'émetteur
User "many" --- "many" Meeting : participent en tant qu'investisseurs
Meeting "1" --- "many" MeetingInvestor : a
MeetingInvestor "many" --- "1" User : concerne
Meeting "1" --- "many" Question : a
Question "many" --- "1" User : posée par
User "1" --- "many" Meeting : crée/met à jour
User "many" --- "many" Role : a
```

## Meeting Scheduling Workflow

Le processus de planification des réunions suit un flux de travail structuré :

1. **Création des créneaux horaires**
   - Lors de la création d'un utilisateur émetteur (issuer), le système génère automatiquement des créneaux horaires pour chaque jour de conférence
   - Chaque créneau horaire comprend une date, une heure de début, une heure de fin et est associé à l'émetteur spécifique
   - Par défaut, tous les créneaux sont marqués comme indisponibles (availability = false)

2. **Gestion des disponibilités par l'émetteur**
   - Les émetteurs sélectionnent les créneaux horaires durant lesquels ils sont disponibles pour des réunions
   - Cela donne aux émetteurs un contrôle total sur leur emploi du temps pendant la conférence
   - Seuls les créneaux marqués comme disponibles (availability = true) seront visibles pour les investisseurs

3. **Demandes de réunion**
   - Les investisseurs consultent les émetteurs disponibles et leurs créneaux horaires
   - Ils sélectionnent un émetteur, un créneau disponible et peuvent ajouter une question préliminaire
   - Une réunion est créée avec le statut initial "PENDING" (en attente)
   - Pour les réunions créées par les investisseurs, elles sont automatiquement marquées comme "one-on-one" (individuelles)

4. **Approbation des réunions**
   - Les émetteurs reçoivent des notifications concernant les demandes de réunion en attente
   - Ils peuvent approuver (CONFIRMED) ou refuser (DECLINED) chaque investisseur individuellement
   - Les administrateurs peuvent également créer et gérer des réunions, y compris des réunions de groupe

5. **Réunions multi-investisseurs**
   - Les administrateurs peuvent créer des réunions de groupe (is_one_on_one = false)
   - Plusieurs investisseurs peuvent être ajoutés à une même réunion avec un émetteur
   - Chaque investisseur a son propre statut de confirmation via la table MeetingInvestor

6. **Questions pré-réunion**
   - Les investisseurs peuvent soumettre des questions avant la réunion
   - Les questions sont liées directement à une réunion spécifique
   - Les émetteurs peuvent préparer des réponses à l'avance et marquer les questions comme répondues

7. **Gestion du statut des réunions**
   - Les réunions passent par différents états: SCHEDULED, PENDING, CONFIRMED, DECLINED, CANCELLED, COMPLETED
   - Les utilisateurs appropriés peuvent mettre à jour les statuts en fonction de leurs rôles
   - Le système garde une trace de qui a créé et mis à jour chaque réunion

## Advantages of This Architecture

This approach offers several key advantages:

1. **Clear Separation of Concerns**
   - TimeSlot: handles both scheduling time and issuer availability
   - Room: manages physical meeting spaces
   - Meeting: handles the business logic of meetings
   - MeetingInvestor: manages investor participation and status

2. **Flexible Meeting Formats**
   - Support for both one-on-one and group meetings
   - Multiple investors can join a single meeting with an issuer
   - Each investor has individual status tracking

3. **Granular Control**
   - Each issuer manages their own availability via time slots
   - Issuers can approve/decline specific investors for each meeting
   - Admins can track meeting status and participation
   - Questions are tied directly to meetings for better context

4. **Simplified Availability Management**
   - Each time slot belongs to a specific issuer
   - Simple boolean flag for time slot availability
   - No complex availability calculations or conflicts
   - Easy to implement and understand

5. **Audit Trail**
   - Records who created and last updated each meeting
   - Complete history of meeting statuses and changes
   - Transparent tracking of the entire scheduling process

6. **Scalability**
   - Structure supports future enhancements like recurring meetings
   - Easy to add waitlists, priorities, or additional meeting attributes
   - Clean domain model that matches business concepts

## Authentication and Authorization

- **User Roles:** Investor, Issuer, Admin
- **Authentication Flow:** 
  1. Users receive login credentials via email
  2. Login with email/password
  3. Redirected to role-specific dashboard
- **Authorization:** Spatie Laravel Permission package manages role-based access

## User Interfaces

### Public Landing Page
- Event information and details
- Login access for registered users
- Responsive design with corporate branding

### Investor Dashboard
- Profile completion
- Issuer selection for meetings
- Meeting scheduling interface
- Personal schedule overview
- Question submission form

### Issuer Dashboard
- Meeting schedule overview
- Investor information for each meeting
- Meeting request approval interface
- Review of questions submitted by investors
- Availability management for time slots

### Admin Back-Office
- User management
- Manual meeting scheduling
- Schedule overview
- Event configuration

## Setup Instructions

1. Clone the repository
```bash
git clone https://github.com/yourusername/bmce-invest-2025.git
cd bmce-invest-2025
```

2. Install dependencies
```bash
composer install
npm install
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database in .env file
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bmce_db
DB_USERNAME=user
DB_PASSWORD=password
```

5. Start Docker services (Optional)
```bash
docker-compose up -d
```

This will start the following services:
- **MySQL:** Database server accessible on port 3306
- **PostgreSQL:** Alternative database server accessible on port 5432
- **phpMyAdmin:** Database management interface accessible at http://localhost:8080
- **MailDev:** Email testing service accessible at http://localhost:1080

6. Run migrations and seeders
```bash
php artisan migrate
php artisan db:seed
```

7. Compile assets
```bash
npm run dev
```

8. Start the development server
```bash
php artisan serve
```

9. Stopping Docker services (when done)
```bash
docker-compose down
```

To remove volumes and start clean:
```bash
docker-compose down -v
```

## Setup Instructions with Laravel Sail (WSL + Docker)

### Prerequisites
- Windows with WSL2 installed
- Docker Desktop configured to work with WSL2
- A working WSL2 distro (Ubuntu recommended)

### Installation Steps

1. Clone the repository in your WSL environment
```bash
git clone https://github.com/yourusername/bmce-invest-2025.git
cd bmce-invest-2025
```

2. Configure environment variables
```bash
cp .env.example .env
```

3. Install Composer dependencies
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

4. Start Laravel Sail (Docker environment)
```bash
./vendor/bin/sail up -d
```

5. Generate application key
```bash
./vendor/bin/sail artisan key:generate
```

6. Run database migrations and seeders
```bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

7. Install and build front-end assets
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

8. Access the application
- Main website: [http://localhost](http://localhost)
- MailHog (Email Testing): [http://localhost:8025](http://localhost:8025)

### Common Sail Commands

```bash
# Start the Sail environment
./vendor/bin/sail up -d

# Stop the Sail environment
./vendor/bin/sail down

# Run artisan commands
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
./vendor/bin/sail artisan make:model NewModel -m

# Run tests
./vendor/bin/sail test

# Run composer commands
./vendor/bin/sail composer require package-name

# Run npm commands
./vendor/bin/sail npm run dev
./vendor/bin/sail npm run build

# Open a shell session within the container
./vendor/bin/sail shell

# Run MySQL CLI
./vendor/bin/sail mysql

# Create a Sail alias (add to ~/.bashrc or ~/.zshrc)
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
# Then you can simply use:
sail up -d
sail artisan migrate
```

### Troubleshooting

- **Port conflicts**: If you have services already running on ports 80, 3306, etc., modify the port mappings in the `docker-compose.yml` file.
- **WSL file performance**: For improved performance, make sure your project files are stored in the WSL file system, not on a Windows-mounted drive.
- **Permission issues**: If you encounter permission problems, you may need to run `chmod -R 777 storage bootstrap/cache` within the WSL environment.

### Stopping and Clean Up

```bash
# Stop containers
./vendor/bin/sail down

# Stop containers and remove volumes (clean slate)
./vendor/bin/sail down -v
```
