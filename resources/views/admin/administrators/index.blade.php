<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
                <li>{{ __('Administrators') }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __('Administrator Management') }}
            </h3>
            <a href="{{ route('admin.administrators.create') }}" class="btn btn-primary">
                <x-heroicon-s-plus class="size-4" />
                <span class="hidden lg:block">{{ __('New Administrator') }}</span>
            </a>
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
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="card card-bordered bg-base-200 shadow-lg">
            <div class="card-body">
                <div class="card bg-base-100 shadow-md overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Last name') }}</th>
                                <th>{{ __('First name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th class="text-center">{{ __('Created At') }}</th>
                                <th class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($administrators as $administrator)
                                <tr>
                                    <td class="font-medium">{{ $administrator->name }}</td>
                                    <td class="font-medium">{{ $administrator->first_name }}</td>
                                    <td>{{ $administrator->email }}</td>
                                    <td class="text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-sm font-medium text-base-content">
                                                {{ $administrator->created_at->diffForHumans() }}
                                            </span>
                                            <span class="text-xs text-base-content/70 mt-1">
                                                {{ $administrator->created_at->format('d-m-Y H:i') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="flex gap-2 items-center justify-end">
                                            <a href="{{ route('admin.administrators.edit', $administrator) }}"
                                                class="btn btn-sm btn-success">{{ __('Edit') }}</a>
                                            @if (auth()->id() !== $administrator->id)
                                                <button type="button"
                                                    @click="$dispatch('open-modal', 'delete-administrator-{{ $administrator->id }}')"
                                                    class="btn btn-sm btn-square btn-error"><x-heroicon-s-trash
                                                        class="size-4" /></button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-base-content/70">
                                        {{ __('No administrators found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $administrators->onEachSide(1)->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </section>

    <!-- Deletion Confirmation Modals -->
    @foreach ($administrators as $administrator)
        @if (auth()->id() !== $administrator->id)
            <x-modal name="delete-administrator-{{ $administrator->id }}" :show="false" focusable>
                <div class="p-6">
                    <h3 class="text-lg font-medium text-error">
                        {{ __('Confirm Administrator Deletion') }}
                    </h3>

                    <p class="mt-3 text-sm text-base-content/70">
                        {{ __('Are you sure you want to delete') }} <strong>{{ $administrator->first_name }}
                            {{ $administrator->name }}</strong>?
                        {{ __('This action cannot be undone.') }}
                    </p>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" class="btn btn-ghost" x-on:click="$dispatch('close')">
                            {{ __('Cancel') }}
                        </button>

                        <form action="{{ route('admin.administrators.destroy', $administrator) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-error">
                                {{ __('Delete Administrator') }}
                            </button>
                        </form>
                    </div>
                </div>
            </x-modal>
        @endif
    @endforeach
</x-app-layout>
