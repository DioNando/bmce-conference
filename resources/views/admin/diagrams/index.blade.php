<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
                <li>{{ __('Diagrammes') }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __('Diagrammes du Projet') }}
            </h3>
        </div>
    </x-slot>

    <section class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="card-body">
                    <h2 class="card-title">{{ __('Diagrammes de Classes') }}</h2>
                    <p>{{ __('Visualisez la structure des classes du projet') }}</p>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.diagrams.classes') }}" class="btn btn-primary rounded-full">
                            {{ __('Voir') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="card-body">
                    <h2 class="card-title">{{ __('Diagrammes de Séquences') }}</h2>
                    <p>{{ __('Visualisez les interactions entre objets') }}</p>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.diagrams.sequences') }}" class="btn btn-primary rounded-full">
                            {{ __('Voir') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="card-body">
                    <h2 class="card-title">{{ __('Diagramme Global') }}</h2>
                    <p>{{ __('Vue d\'ensemble complète de toutes les entités et leurs relations') }}</p>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.diagrams.global') }}" class="btn btn-primary rounded-full">
                            {{ __('Voir') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="card-body">
                    <h2 class="card-title">{{ __('Système de Permissions') }}</h2>
                    <p>{{ __('Architecture détaillée du système de rôles et permissions') }}</p>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.diagrams.permissions') }}" class="btn btn-primary rounded-full">
                            {{ __('Voir') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="card-body">
                    <h2 class="card-title">{{ __('Authentification') }}</h2>
                    <p>{{ __('Processus complet de connexion et validation des utilisateurs') }}</p>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.diagrams.authentication') }}" class="btn btn-primary rounded-full">
                            {{ __('Voir') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="card-body">
                    <h2 class="card-title">{{ __('Création de Rendez-vous') }}</h2>
                    <p>{{ __('Flux détaillé de création et gestion des rendez-vous') }}</p>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.diagrams.meetings') }}" class="btn btn-primary rounded-full">
                            {{ __('Voir') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="card-body">
                    <h2 class="card-title">{{ __('Export de Données') }}</h2>
                    <p>{{ __('Processus d\'export vers Excel avec gestion des gros volumes') }}</p>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.diagrams.exports') }}" class="btn btn-primary rounded-full">
                            {{ __('Voir') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="card-body">
                    <h2 class="card-title">{{ __('Diagrammes de Packages') }}</h2>
                    <p>{{ __("Visualisez l'organisation des packages du projet") }}</p>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.diagrams.packages') }}" class="btn btn-primary rounded-full">
                            {{ __('Voir') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="card-body">
                    <h2 class="card-title">{{ __("Diagrammes de Cas d'Utilisation") }}</h2>
                    <p>{{ __('Visualisez les fonctionnalités du système du point de vue utilisateur') }}</p>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.diagrams.use-cases') }}" class="btn btn-primary rounded-full">
                            {{ __('Voir') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
