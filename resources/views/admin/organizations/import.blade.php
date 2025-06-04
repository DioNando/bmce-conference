@php
    use App\Enums\Origin;
    use App\Enums\OrganizationType;
@endphp
<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
            <li><a href="{{ route('admin.organizations.index') }}" class="text-primary">{{ __('Organizations') }}</a></li>
            <li>{{ __('Import') }}</li>
        </ul>
    </div>
    <section class="space-y-6">
        <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __('Import Organizations') }}
            </h3>
            <div class="flex gap-3 mt-4">
                <a href="{{ route('admin.organizations.download-template') }}" class="btn btn-outline btn-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    {{ __('Download Template') }}
                </a>
            </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div role="alert" class="alert alert-error">
                <span>{!! session('error') !!}</span>
            </div>
        @endif

        <div class="card card-bordered bg-base-100 shadow-lg">
            <div class="card-body">
                <div class="mb-6">
                    <h4 class="text-lg font-medium mb-2">{{ __('Import Instructions') }}:</h4>
                    <ul class="list pl-5 space-y-2">
                        <li>{{ __('The file must be in Excel (.xlsx, .xls) or CSV format.') }}</li>
                        <li>{{ __('The first row must contain column headers.') }}</li>
                        <li>{{ __('Required columns') }}: <span class="text-error font-medium">name</span></li>
                        <li>{{ __('Optional columns') }}: profile, origin, organization_type, country, description</li>
                        <li>{{ __('The "profile" column must contain one of the following') }}: issuer, investor</li>
                        <li>{{ __('The "origin" column must contain') }}: national, foreign/international</li>
                        <li>{{ __('The "organization_type" column should match one of the predefined types or will be set as "Other"') }}</li>
                        <li>{{ __('The "country" column should match an existing country name (English or French)') }}</li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h4 class="text-lg font-medium mb-2">{{ __('Example Format') }}:</h4>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra table-sm">
                            <thead>
                                <tr>
                                    <th>name</th>
                                    <th>profile</th>
                                    <th>origin</th>
                                    <th>organization_type</th>
                                    <th>country</th>
                                    <th>description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>BlackRock</td>
                                    <td>investor</td>
                                    <td>foreign</td>
                                    <td>Asset Management</td>
                                    <td>United States</td>
                                    <td>A global investment company</td>
                                </tr>
                                <tr>
                                    <td>BMCE Bank</td>
                                    <td>issuer</td>
                                    <td>national</td>
                                    <td>Bank</td>
                                    <td>Morocco</td>
                                    <td>Leading bank in Morocco</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="divider"></div>

                <form action="{{ route('admin.organizations.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <fieldset class="fieldset mb-4">
                        <legend class="fieldset-legend">{{ __('Excel/CSV File') }}</legend>
                        <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required
                               class="file-input file-input-bordered w-full">
                        @error('file')
                            <p class="label text-error text-sm">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Import Organizations') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
