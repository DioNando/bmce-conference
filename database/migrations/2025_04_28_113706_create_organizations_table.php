<?php

use App\Enums\OrganizationType;
use App\Enums\Origin;
use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('origin', array_column(Origin::cases(), 'value'))->default(Origin::NATIONAL->value);
            $table->enum('profil', [UserRole::INVESTOR->value, UserRole::ISSUER->value]);
            $table->enum('organization_type', array_column(OrganizationType::cases(), 'value'))->nullable();
            $table->string('logo')->nullable();
            $table->string('fiche_bkgr')->nullable();
            $table->string('organization_type_other')->nullable();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
