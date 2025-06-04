@php
    use App\Enums\UserRole;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
                <li><a href="{{ route('admin.users.index') }}" class="text-primary">{{ __('Accounts') }}</a></li>
                <li>{{ __('Import') }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __('Import Users') }}
            </h3>
            <div class="flex gap-3">
                <a href="{{ route('admin.users.download-template') }}" class="btn btn-outline btn-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    {{ __('Download Template') }}
                </a>
            </div>
        </div>
    </x-slot>
    <section class="space-y-6">


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
                        <li>{{ __('Required columns') }}:
                            <span class="text-error font-medium">last_name, first_name, email</span>
                        </li>
                        <li>{{ __('Optional columns') }}: phone, position, profile, status, organization, password</li>
                        <li>{{ __('The "profile" column must contain one of the following roles') }}: issuer, investor
                        </li>
                        <li>{{ __('The "status" column can contain') }}: active/inactive {{ __('or') }}
                            true/false</li>
                        <li>{{ __('The "organization" column must contain the name of an existing organization') }}
                        </li>
                        <li>{{ __('If no password is provided, a default password will be generated') }}</li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h4 class="text-lg font-medium mb-2">{{ __('Example Format') }}:</h4>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra table-sm">
                            <thead>
                                <tr>
                                    <th>last_name</th>
                                    <th>first_name</th>
                                    <th>email</th>
                                    <th>phone</th>
                                    <th>position</th>
                                    <th>profile</th>
                                    <th>status</th>
                                    <th>organization</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Smith</td>
                                    <td>John</td>
                                    <td>john@example.com</td>
                                    <td>+212 123456789</td>
                                    <td>Manager</td>
                                    <td>investor</td>
                                    <td>active</td>
                                    <td>BlackRock</td>
                                </tr>
                                <tr>
                                    <td>Doe</td>
                                    <td>Jane</td>
                                    <td>jane@example.com</td>
                                    <td>+212 987654321</td>
                                    <td>CEO</td>
                                    <td>issuer</td>
                                    <td>active</td>
                                    <td>BMCE Bank</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="divider"></div>

                <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
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
                            {{ __('Import Users') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
