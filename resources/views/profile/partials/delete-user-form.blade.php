<section>
    <header class="mb-6">
        <h2 class="text-xl font-medium text-error">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-base-content/70">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button
        type="button"
        class="btn btn-error"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h3 class="font-medium text-lg text-error mb-4">
                {{ __('Are you sure you want to delete your account?') }}
            </h3>

            <p class="text-base-content/70 text-sm">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <fieldset class="fieldset mt-6">
                <legend class="fieldset-legend sr-only">{{ __('Password') }}</legend>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="input input-bordered w-full"
                    placeholder="{{ __('Password') }}"
                />
                @error('password', 'userDeletion')
                    <p class="label text-error text-sm">{{ $message }}</p>
                @enderror
            </fieldset>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="btn btn-ghost" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" class="btn btn-error">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
