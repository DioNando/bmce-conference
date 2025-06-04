<?php

namespace Database\Seeders;

use App\Enums\MeetingStatus;
use App\Enums\InvestorStatus;
use App\Enums\UserRole;
use App\Models\Meeting;
use App\Models\MeetingInvestor;
use App\Models\Organization;
use App\Models\Question;
use App\Models\Room;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MeetingSeeder extends Seeder
{
    /**
     * Liste de questions prédéfinies pertinentes pour les rencontres investisseurs-émetteurs
     */
    private $questionsList = [
        "What are your growth plans for the next 3 years?",
        "How do you plan to finance your international development?",
        "What are your operational margins by market segment?",
        "What is your strategy regarding new entrants in your sector?",
        "What is your action plan to reduce your carbon footprint?",
        "How are you approaching the digital transformation of your company?",
        "What are your main operational risks and how do you manage them?",
        "What acquisitions are you considering in the next 12 months?",
        "What is your medium-term dividend policy?",
        "How is your governance adapting to new ESG requirements?",
        "What are your R&D investments and their expected returns?",
        "What is the impact of inflation on your cost structure?",
        "How are you managing supply chain tensions?",
        "What is your currency risk hedging strategy?",
        "How do you evaluate the current valuation of your company?",
        "What are your main competitive advantages in the market?",
        "How do you anticipate regulatory changes in your sector?",
        "What synergies do you expect from your recent acquisitions?",
        "What is your 5-year technology roadmap?",
        "How do you attract and retain key talent?",
    ];

    /**
     * Liste de réponses prédéfinies pour les questions
     */
    private $responsesList = [
        "We are targeting a 15% annual growth rate over the next three years, focusing on emerging markets in Africa and Southeast Asia.",
        "Our international development will be financed through a mix of debt and equity, with a planned bond issuance next quarter.",
        "Our highest margins are in our premium segment at 42%, while our mass market products operate at 23%.",
        "We're differentiating through proprietary technology and economies of scale that new entrants can't match.",
        "We've committed to reducing our carbon emissions by 30% by 2028 and are investing in renewable energy across all facilities.",
        "We're implementing an enterprise-wide digital transformation strategy with a focus on AI-driven analytics and customer experience.",
        "Our risk management framework identifies supply chain disruptions as our top risk, mitigated through supplier diversification.",
        "We're currently in due diligence with two mid-size competitors in the European market.",
        "We target a 40-45% dividend payout ratio over the medium term while maintaining flexibility for strategic investments.",
        "We've established an ESG committee at the board level and are implementing TCFD reporting standards this year.",
        "R&D investments represent 8% of revenue with an expected ROI of 3x over a 5-year period.",
        "Inflation has increased our cost base by approximately 7%, which we've partially offset through operational efficiencies.",
        "We've established secondary suppliers for critical components and increased inventory levels of essential materials.",
        "We hedge 75% of our foreign currency exposure using a combination of forwards and options with 6-12 month horizons.",
        "Based on peer multiples and discounted cash flow analysis, we believe our current valuation is approaching fair value.",
        "Our patent portfolio, brand equity, and distribution network provide significant barriers to entry.",
        "We maintain a regulatory affairs team that works proactively with legislators to anticipate changes in our industry.",
        "Cost synergies from our recent acquisition are expected to reach $15M annually by year two.",
        "Our technology roadmap focuses on cloud migration, AI integration, and cybersecurity enhancement over the next five years.",
        "We've reduced turnover by 25% through our employee stock ownership program and professional development initiatives."
    ];

    // Compteur pour les statistiques
    private $totalQuestions = 0;
    private $totalMeetings = 0;
    private $totalInvestors = 0;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer toutes les ressources nécessaires
        $rooms = Room::all();

        // Récupérer les organisations correctement en filtrant par le champ 'profil'
        $issuerOrgs = Organization::where('profil', UserRole::ISSUER->value)->get();
        $investorOrgs = Organization::where('profil', UserRole::INVESTOR->value)->get();

        // Récupérer les utilisateurs liés aux organisations
        $issuers = User::whereIn('organization_id', $issuerOrgs->pluck('id'))->get();
        $investors = User::whereIn('organization_id', $investorOrgs->pluck('id'))->get();

        // Vérifier que nous avons des données
        if ($issuers->isEmpty() || $investors->isEmpty() || $rooms->isEmpty()) {
            echo "Avertissement: Données manquantes pour créer des réunions.\n";
            echo "Issuers: " . $issuers->count() . ", Investors: " . $investors->count() . ", Rooms: " . $rooms->count() . "\n";
            return;
        }

        echo "Création des réunions pour " . $issuers->count() . " émetteurs et " . $investors->count() . " investisseurs.\n";

        // Pour chaque émetteur, créer des réunions
        foreach ($issuers as $issuer) {
            // Récupérer les créneaux horaires disponibles pour cet émetteur spécifique
            $issuerTimeSlots = TimeSlot::where('user_id', $issuer->id)
                                      ->where('availability', true)
                                      ->get();

            if ($issuerTimeSlots->isEmpty()) {
                echo "Aucun créneau horaire disponible pour l'émetteur ID: " . $issuer->id . "\n";
                continue;
            }

            // On crée entre 2 et 6 réunions par émetteur (ou moins si moins de créneaux disponibles)
            $meetingsCount = min(rand(2, 6), $issuerTimeSlots->count());

            for ($i = 0; $i < $meetingsCount; $i++) {
                // Si on n'a plus de time slots disponibles, on arrête
                if ($issuerTimeSlots->isEmpty()) {
                    break;
                }

                // Sélectionner un time slot et une salle aléatoire
                $timeSlot = $issuerTimeSlots->random();
                $room = $rooms->random();

                // Déterminer si c'est une réunion one-on-one (20% de chance)
                $isOneOnOne = rand(1, 100) <= 20;

                // Statut aléatoire de la réunion avec 60% confirmed, 30% pending, 10% cancelled
                $statusRandom = rand(1, 100);
                $meetingStatus = match(true) {
                    $statusRandom <= 60 => MeetingStatus::CONFIRMED,
                    $statusRandom <= 90 => MeetingStatus::PENDING,
                    default => MeetingStatus::CANCELLED,
                };

                // Créer la réunion
                $meeting = Meeting::create([
                    'room_id' => $room->id,
                    'time_slot_id' => $timeSlot->id,
                    'issuer_id' => $issuer->id,
                    'created_by_id' => $issuer->id, // L'émetteur crée la réunion
                    'updated_by_id' => $issuer->id,
                    'status' => $meetingStatus,
                    'notes' => "Meeting with " . $issuer->organization->name,
                    'is_one_on_one' => $isOneOnOne,
                ]);

                // Ajouter des investisseurs à la réunion
                $investorCount = $isOneOnOne ? 1 : rand(2, min(4, $investors->count()));
                $meetingInvestors = $investors->random($investorCount);

                foreach ($meetingInvestors as $investor) {
                    // 60% confirmés, 30% en attente, 10% refusés
                    $statusRandom = rand(1, 100);
                    $investorStatus = match(true) {
                        $statusRandom <= 60 => InvestorStatus::CONFIRMED,
                        $statusRandom <= 90 => InvestorStatus::PENDING,
                        default => InvestorStatus::REFUSED,
                    };

                    MeetingInvestor::create([
                        'meeting_id' => $meeting->id,
                        'investor_id' => $investor->id,
                        'status' => $investorStatus,
                    ]);

                    $this->totalInvestors++;

                    // Ajouter une question pour chaque investisseur confirmé ou en attente (avec 80% de chance)
                    if ($investorStatus !== InvestorStatus::REFUSED && rand(1, 100) <= 80) {
                        // 80% de chances que la question soit répondue si l'investisseur est confirmé
                        $isAnswered = $investorStatus === InvestorStatus::CONFIRMED && rand(1, 100) <= 80;

                        // Sélectionner une question aléatoire de la liste
                        $randomQuestion = $this->questionsList[array_rand($this->questionsList)];

                        $questionData = [
                            'meeting_id' => $meeting->id,
                            'investor_id' => $investor->id,
                            'question' => $randomQuestion,
                            'is_answered' => $isAnswered,
                        ];

                        if ($isAnswered) {
                            $questionData['response'] = $this->responsesList[array_rand($this->responsesList)];
                            $questionData['answered_at'] = Carbon::now();
                        }

                        Question::create($questionData);

                        $this->totalQuestions++;
                    }
                }

                $this->totalMeetings++;

                // Retirer le time slot utilisé de la liste des disponibles
                $issuerTimeSlots = $issuerTimeSlots->reject(function ($t) use ($timeSlot) {
                    return $t->id === $timeSlot->id;
                });
            }
        }

        echo "Total réunions créées: " . $this->totalMeetings . "\n";
        echo "Total investisseurs aux réunions: " . $this->totalInvestors . "\n";
        echo "Total questions créées: " . $this->totalQuestions . "\n";
    }
}
