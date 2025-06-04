@php
    use App\Enums\Origin;
    use App\Enums\OrganizationType;
@endphp
<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
            <li><a href="{{ route('admin.organizations.index') }}" class="text-primary">{{ __('Organizations') }}</a></li>
            <li>{{ __('Edit') }}</li>
        </ul>
    </div>
    <div class="mb-6">
        <h2 class="text-xl font-semibold">{{ __('Edit Organization') }}</h2>
    </div>
    <div class="card card-bordered bg-base-100 shadow-lg">
        <div class="card-body">

            <form method="POST" action="{{ route('admin.organizations.update', $organization) }}" enctype="multipart/form-data" x-data="{ organizationType: '{{ old('organization_type', $organization->organization_type?->value) }}' }">
                @csrf
                @method('PUT')

                <!-- Name -->
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">{{ __('Name') }}</legend>
                    <input id="name" type="text" name="name" value="{{ old('name', $organization->name) }}"
                        class="input input-bordered w-full" required autofocus />
                    @error('name')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Origin -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('Origin') }}</legend>
                    <select id="origin" name="origin" class="select select-bordered w-full" required>
                        <option value="">{{ __('-- Select --') }}</option>
                        @foreach(Origin::all() as $origin)
                            <option value="{{ $origin->value }}" {{ old('origin', $organization->origin?->value) == $origin->value ? 'selected' : '' }}>
                                {{ ucfirst($origin->value) }}
                            </option>
                        @endforeach
                    </select>
                    @error('origin')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Profil -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('Profile') }}</legend>
                    <select id="profil" name="profil" class="select select-bordered w-full" required>
                        <option value="">{{ __('-- Select --') }}</option>
                        <option value="issuer" {{ old('profil', $organization->profil?->value) == 'issuer' ? 'selected' : '' }}>{{ __('Issuer') }}</option>
                        <option value="investor" {{ old('profil', $organization->profil?->value) == 'investor' ? 'selected' : '' }}>{{ __('Investor') }}</option>
                    </select>
                    @error('profil')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Organization Type -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('Organization Type') }}</legend>
                    <select id="organization_type" name="organization_type" x-model="organizationType"
                        class="select select-bordered w-full">
                        <option value="">{{ __('-- Select --') }}</option>
                        @foreach(OrganizationType::all() as $type)
                            <option value="{{ $type->value }}" {{ old('organization_type', $organization->organization_type?->value) == $type->value ? 'selected' : '' }}>
                                {{ $type->englishLabel() }}
                            </option>
                        @endforeach
                    </select>
                    @error('organization_type')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Organization Type Other -->
                <fieldset class="fieldset mt-4" x-show="organizationType === 'autre'" x-cloak>
                    <legend class="fieldset-legend">{{ __('Specify Organization Type') }}</legend>
                    <input id="organization_type_other" type="text" name="organization_type_other"
                        value="{{ old('organization_type_other', $organization->organization_type_other) }}"
                        class="input input-bordered w-full" />
                    @error('organization_type_other')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Country -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('Country') }}</legend>
                    <select id="country_id" name="country_id" class="select select-bordered w-full" required>
                        <option value="">{{ __('-- Select --') }}</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id', $organization->country_id) == $country->id ? 'selected' : '' }}>
                                {{ $country->name_en }}
                            </option>
                        @endforeach
                    </select>
                    @error('country_id')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Logo -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('Logo') }}</legend>
                    @if($organization->logo)
                        <div class="mt-2 mb-2 h-16 w-16 rounded-full overflow-hidden">
                            <img src="{{ asset('storage/' . $organization->logo) }}" alt="{{ $organization->name }}" class="h-full w-full object-cover">
                        </div>
                    @endif
                    <input id="logo" type="file" name="logo" class="file-input file-input-bordered w-full" />
                    <p class="label text-base-content/70 text-sm">{{ __('Leave empty to keep the current logo') }}</p>
                    @error('logo')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Fiche BKGR -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('Background File') }}</legend>
                    @if($organization->fiche_bkgr)
                        <div class="mt-2 mb-2">
                            <a href="{{ asset('storage/' . $organization->fiche_bkgr) }}" target="_blank" class="link link-primary">
                                {{ __('View current file') }}
                            </a>
                        </div>
                    @endif
                    <input id="fiche_bkgr" type="file" name="fiche_bkgr" class="file-input file-input-bordered w-full" />
                    <p class="label text-base-content/70 text-sm">{{ __('Leave empty to keep the current file') }}</p>
                    @error('fiche_bkgr')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Description -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('Description') }}</legend>
                    <textarea id="description" name="description" rows="4"
                        class="textarea textarea-bordered w-full">{{ old('description', $organization->description) }}</textarea>
                    @error('description')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('admin.organizations.index') }}"
                        class="btn btn-ghost">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Update Organization') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
