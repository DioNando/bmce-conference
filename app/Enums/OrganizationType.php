<?php

namespace App\Enums;

/**
 * Enum representing different types of organisms in the system.
 *
 * This enum defines the different types of organisms that can be used in the application,
 * along with helper methods to list all available types and get human-readable labels.
 *
 * @method static OrganizationType CAISSE_RETRAITE() Caisse de retraite
 * @method static OrganizationType OPCVM() OPCVM
 * @method static OrganizationType ASSURANCE() Assurance
 * @method static OrganizationType GESTION_SOUS_MANDAT() Gestion Sous Mandat
 * @method static OrganizationType BANQUE() Banque
 * @method static OrganizationType FONDS_INVESTISSEMENT() Fonds d'investissement
 * @method static OrganizationType AUTRE() Autre
 * @method static array all() Returns an array of all available organism types
 * @method string label() Returns a human-readable French label for the type
 * @method string englishLabel() Returns a human-readable English label for the type
 */
enum OrganizationType: string
{
    case CAISSE_RETRAITE = 'caisse_retraite';
    case OPCVM = 'opcvm';
    case ASSURANCE = 'assurance';
    case GESTION_SOUS_MANDAT = 'gestion_sous_mandat';
    case BANQUE = 'banque';
    case FONDS_INVESTISSEMENT = 'fonds_investissement';
    case AUTRE = 'autre';

    public static function all(): array
    {
        return [
            self::CAISSE_RETRAITE,
            self::OPCVM,
            self::ASSURANCE,
            self::GESTION_SOUS_MANDAT,
            self::BANQUE,
            self::FONDS_INVESTISSEMENT,
            self::AUTRE,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::CAISSE_RETRAITE => 'Caisse de retraite',
            self::OPCVM => 'OPCVM',
            self::ASSURANCE => 'Assurance',
            self::GESTION_SOUS_MANDAT => 'Gestion Sous Mandat',
            self::BANQUE => 'Banque',
            self::FONDS_INVESTISSEMENT => 'Fonds d\'investissement',
            self::AUTRE => 'Autre',
        };
    }

    public function englishLabel(): string
    {
        return match ($this) {
            self::CAISSE_RETRAITE => 'Pension Fund',
            self::OPCVM => 'Mutual Fund',
            self::ASSURANCE => 'Insurance',
            self::GESTION_SOUS_MANDAT => 'Discretionary Management',
            self::BANQUE => 'Bank',
            self::FONDS_INVESTISSEMENT => 'Investment Fund',
            self::AUTRE => 'Other',
        };
    }
}
