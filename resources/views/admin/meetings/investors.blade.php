 @php
     $INVESTOR_STATUS = \App\Enums\InvestorStatus::class;
     $MEETING_STATUS = \App\Enums\MeetingStatus::class;
 @endphp

 <x-app-layout>
     <section class="space-y-6" x-data="{ selectedInvestors: [] }">
         <div class="breadcrumbs text-sm mb-4">
             <ul>
                 <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
                 <li><a href="{{ route('admin.meetings.index') }}" class="text-primary">{{ __('Meetings') }}</a></li>
                 <li><a href="{{ route('admin.meetings.show', $meeting) }}"
                         class="text-primary">{{ __('Meeting Details') }}</a></li>
                 <li>{{ __('Investors') }}</li>
             </ul>
         </div>
         <div>
             <h1 class="text-2xl font-bold text-primary">
                 {{ __('Meeting #:id Investors', ['id' => $meeting->id]) }}
             </h1>
         </div>

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

         <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
             <!-- Main Content: Investors List -->
             <div class="lg:col-span-3 space-y-6">
                 <!-- Bulk action section -->
                 <div x-show="selectedInvestors.length > 0" x-cloak class="mb-4 p-4 bg-base-200 rounded-box shadow-md">
                     <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                         <div class="flex items-center gap-2">
                             <span class="badge badge-lg badge-primary" x-text="selectedInvestors.length"></span>
                             <span class="font-medium">{{ __('investors selected') }}</span>
                         </div>
                         <div class="flex gap-2">
                             <form action="{{ route('admin.meetings.update-multiple-investors', $meeting) }}"
                                 method="POST">
                                 @method('PATCH')
                                 @csrf
                                 <template x-for="investorId in selectedInvestors" :key="investorId">
                                     <input type="hidden" name="investor_ids[]" :value="investorId">
                                 </template>
                                 <input type="hidden" name="status"
                                     value="{{ \App\Enums\InvestorStatus::CONFIRMED->value }}">
                                 <button type="submit" class="btn btn-sm btn-success">
                                     <x-heroicon-s-check class="size-4" />
                                     <span> {{ __('Confirm') }}</span>
                                 </button>
                             </form>

                             <form action="{{ route('admin.meetings.update-multiple-investors', $meeting) }}"
                                 method="POST">
                                 @method('PATCH')
                                 @csrf
                                 <template x-for="investorId in selectedInvestors" :key="investorId">
                                     <input type="hidden" name="investor_ids[]" :value="investorId">
                                 </template>
                                 <input type="hidden" name="status"
                                     value="{{ \App\Enums\InvestorStatus::PENDING->value }}">
                                 <button type="submit" class="btn btn-sm btn-warning">
                                     <x-heroicon-s-clock class="size-4" />
                                     <span> {{ __('Set as Pending') }}</span>
                                 </button>
                             </form>

                             <form action="{{ route('admin.meetings.update-multiple-investors', $meeting) }}"
                                 method="POST">
                                 @method('PATCH')
                                 @csrf
                                 <template x-for="investorId in selectedInvestors" :key="investorId">
                                     <input type="hidden" name="investor_ids[]" :value="investorId">
                                 </template>
                                 <input type="hidden" name="status"
                                     value="{{ \App\Enums\InvestorStatus::REFUSED->value }}">
                                 <button type="submit" class="btn btn-sm btn-error">
                                     <x-heroicon-s-x-mark class="size-4" />
                                     <span> {{ __('Refuse') }}</span>
                                 </button>
                             </form>

                             <form action="{{ route('admin.meetings.send-multiple-invitations', $meeting) }}"
                                 method="POST">
                                 @csrf
                                 <template x-for="investorId in selectedInvestors" :key="investorId">
                                     <input type="hidden" name="investor_ids[]" :value="investorId">
                                 </template>
                                 <button type="submit" class="btn btn-sm btn-accent">
                                     <x-heroicon-s-paper-airplane class="size-4" />
                                     <span> {{ __('Envoyer invitations') }}</span>
                                 </button>
                             </form>
                         </div>
                     </div>
                 </div>

                 <div class="card card-bordered bg-base-200 shadow-lg">
                     <div class="card-body">
                         <div class="flex gap-2 justify-between items-center mb-4">
                             <h2 class="card-title text-primary">{{ __('Investors List') }}</h2>
                             <span class="badge badge-primary">{{ $meeting->investors->count() }}
                                 {{ __('participant(s)') }}</span>
                         </div>

                         <div x-data="investorsTable()" class="card bg-base-100 shadow-md overflow-x-auto">
                             <table class="table">
                                 <thead>
                                     <tr>
                                         <th class="w-10 pl-6 pt-2">
                                             <label class="cursor-pointer">
                                                 <input type="checkbox" id="select-all-investors"
                                                     class="checkbox checkbox-sm checkbox-primary"
                                                     x-on:change="toggleAll($event)">
                                             </label>
                                         </th>
                                         <th>{{ __('Investor Name') }}</th>
                                         {{-- <th>{{ __('Organization') }}</th>
                                        <th>{{ __('Email') }}</th> --}}
                                         <th class="text-center">{{ __('QR Code') }}</th>
                                         <th class="text-center">{{ __('Status') }}</th>
                                         <th class="text-center">{{ __('Invitation') }}</th>
                                         <th class="text-end">{{ __('Actions') }}</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     @forelse ($meeting->investors as $investor)
                                         @php
                                             $meetingInvestor = $meeting->meetingInvestors
                                                 ->where('investor_id', $investor->id)
                                                 ->first();
                                             $status = $meetingInvestor ? $meetingInvestor->status->value : 'unknown';
                                         @endphp
                                         <tr class="investor-row"
                                             :class="{ 'bg-primary-content': selectedInvestors.includes({{ $investor->id }}) }">
                                             <td class="w-10 pl-6 pt-2">
                                                 <label class="cursor-pointer">
                                                     <input type="checkbox" value="{{ $investor->id }}"
                                                         class="investor-checkbox checkbox checkbox-sm checkbox-primary"
                                                         x-on:change="toggleInvestor({{ $investor->id }})"
                                                         :checked="selectedInvestors.includes({{ $investor->id }})">
                                                 </label>
                                             </td>
                                             <td class="font-medium">
                                                 <a href="{{ route('admin.users.show', $investor) }}"
                                                     class="link link-primary">
                                                     {{ $investor->name . ' ' . $investor->first_name }}
                                                 </a>
                                                 <div class="text-xs text-base-content/70 flex flex-col gap-1 mt-1">
                                                     <span>
                                                         {{ $investor->email }}
                                                     </span>
                                                     <span>
                                                         {{ $investor->organization->name ?? __('No Organization') }}
                                                     </span>
                                                 </div>
                                             </td>
                                             {{-- <td>
                                                {{ $investor->organization->name ?? __('No Organization') }}
                                            </td>
                                            <td>
                                                {{ $investor->email }}
                                            </td> --}}
                                             <td class="text-center flex flex-col justify-center items-center">
                                                 {{-- <div class="flex items-center mb-1 text-primary">
                                                    <x-heroicon-s-qr-code class="size-3 mr-2" />
                                                    <span class="text-xs">QR
                                                        Code</span>
                                                </div> --}}
                                                 <a href="{{ route('admin.attendance.qrcode', ['meeting' => $meeting->id, 'investor' => $meetingInvestor->id]) }}"
                                                     class="hover:scale-105 transition-transform">
                                                     <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($investor->qr_code) }}"
                                                         alt="QR Code pour {{ $investor->first_name }} {{ $investor->last_name }}"
                                                         class="w-20 h-20 flex-shrink-0 p-1 border-2 border-accent rounded-md" />
                                                 </a>
                                                 <div class="flex flex-col items-center gap-1 mt-1">
                                                     <span class="text-xs truncate max-w-20"
                                                         id="qrCode{{ $meetingInvestor->id }}">
                                                         {{ Str::limit($investor->qr_code, 12) }}</span>
                                                     <button type="button"
                                                         class="btn btn-xs btn-accent rounded-full copy-btn"
                                                         data-qr-code="{{ $investor->qr_code }}"
                                                         data-id="{{ $meetingInvestor->id }}">
                                                         <x-heroicon-s-qr-code class="size-3" />
                                                         {{ __('Copy') }}
                                                     </button>
                                                 </div>
                                             </td>
                                             <td class="text-center">
                                                 @switch($status)
                                                     @case($INVESTOR_STATUS::PENDING->value)
                                                         <span
                                                             class="badge badge-{{ $INVESTOR_STATUS::PENDING->color() }}">{{ __($INVESTOR_STATUS::PENDING->label()) }}</span>
                                                     @break

                                                     @case($INVESTOR_STATUS::CONFIRMED->value)
                                                         <span
                                                             class="badge badge-{{ $INVESTOR_STATUS::CONFIRMED->color() }}">{{ __($INVESTOR_STATUS::CONFIRMED->label()) }}</span>
                                                     @break

                                                     @case($INVESTOR_STATUS::REFUSED->value)
                                                         <span
                                                             class="badge badge-{{ $INVESTOR_STATUS::REFUSED->color() }}">{{ __($INVESTOR_STATUS::REFUSED->label()) }}</span>
                                                     @break

                                                     @case($INVESTOR_STATUS::ATTENDED->value)
                                                         <span
                                                             class="badge badge-{{ $INVESTOR_STATUS::ATTENDED->color() }}">{{ __($INVESTOR_STATUS::ATTENDED->label()) }}</span>
                                                     @break

                                                     @case($INVESTOR_STATUS::ABSENT->value)
                                                         <span
                                                             class="badge badge-{{ $INVESTOR_STATUS::ABSENT->color() }}">{{ __($INVESTOR_STATUS::ABSENT->label()) }}</span>
                                                     @break

                                                     @default
                                                         <span class="badge badge-neutral">{{ __('Unknown') }}</span>
                                                 @endswitch
                                                 @if ($status !== $INVESTOR_STATUS::ATTENDED->value)
                                                     <form
                                                         action="{{ route('admin.meetings.update-investor-status', ['meeting' => $meeting->id, 'investor' => $investor->id]) }}"
                                                         method="POST" class="status-form mt-2">
                                                         @csrf
                                                         @method('PATCH')
                                                         <select name="status"
                                                             class="select select-bordered select-sm status-select"
                                                             onchange="this.form.submit()">
                                                             <option value="{{ $INVESTOR_STATUS::CONFIRMED->value }}"
                                                                 {{ $status === $INVESTOR_STATUS::CONFIRMED->value ? 'selected' : '' }}>
                                                                 {{ __($INVESTOR_STATUS::CONFIRMED->label()) }}
                                                             </option>
                                                             <option value="{{ $INVESTOR_STATUS::PENDING->value }}"
                                                                 {{ $status === $INVESTOR_STATUS::PENDING->value ? 'selected' : '' }}>
                                                                 {{ __($INVESTOR_STATUS::PENDING->label()) }}</option>
                                                             <option value="{{ $INVESTOR_STATUS::REFUSED->value }}"
                                                                 {{ $status === $INVESTOR_STATUS::REFUSED->value ? 'selected' : '' }}>
                                                                 {{ __($INVESTOR_STATUS::REFUSED->label()) }}</option>
                                                             <option value="{{ $INVESTOR_STATUS::ABSENT->value }}"
                                                                 {{ $status === $INVESTOR_STATUS::ABSENT->value ? 'selected' : '' }}>
                                                                 {{ __($INVESTOR_STATUS::ABSENT->label()) }}</option>
                                                             {{-- <option value="{{ $INVESTOR_STATUS::ATTENDED->value }}"
                                                                 {{ $status === $INVESTOR_STATUS::ATTENDED->value ? 'selected' : '' }}>
                                                                 {{ __($INVESTOR_STATUS::ATTENDED->label()) }}</option> --}}
                                                         </select>
                                                     </form>
                                                 @endif
                                             </td>
                                             <td class="text-center">
                                                 @if ($meetingInvestor && $meetingInvestor->invitation_sent)
                                                     <div class="flex flex-col items-center">
                                                         <span
                                                             class="badge badge-success mb-1">{{ __('Envoyée') }}</span>
                                                         <span
                                                             class="text-xs text-base-content/70">{{ $meetingInvestor->invitation_sent_at->diffForHumans() }}</span>
                                                     </div>
                                                 @else
                                                     <span
                                                         class="badge badge-neutral text-nowrap">{{ __('Non envoyée') }}</span>
                                                 @endif
                                             </td>
                                             <td class="text-end">
                                                 <div class="flex items-center justify-end gap-2">
                                                     {{-- <form
                                                        action="{{ route('admin.meetings.update-investor-status', ['meeting' => $meeting->id, 'investor' => $investor->id]) }}"
                                                        method="POST" class="status-form">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="status"
                                                            class="select select-bordered select-sm status-select"
                                                            onchange="this.form.submit()">
                                                            <option
                                                                value="{{ \App\Enums\InvestorStatus::CONFIRMED->value }}"
                                                                {{ $status === \App\Enums\InvestorStatus::CONFIRMED->value ? 'selected' : '' }}>
                                                                {{ __('Confirmed') }}</option>
                                                            <option
                                                                value="{{ \App\Enums\InvestorStatus::PENDING->value }}"
                                                                {{ $status === \App\Enums\InvestorStatus::PENDING->value ? 'selected' : '' }}>
                                                                {{ __('Pending') }}</option>
                                                            <option
                                                                value="{{ \App\Enums\InvestorStatus::REFUSED->value }}"
                                                                {{ $status === \App\Enums\InvestorStatus::REFUSED->value ? 'selected' : '' }}>
                                                                {{ __('Refused') }}</option>
                                                        </select>
                                                    </form> --}}
                                                     <form
                                                         action="{{ route('admin.meetings.send-invitation', ['meeting' => $meeting->id, 'investor' => $investor->id]) }}"
                                                         method="POST">
                                                         @csrf
                                                         <button type="submit"
                                                             class="btn btn-sm {{ $meetingInvestor && $meetingInvestor->invitation_sent ? 'btn-base' : 'btn-accent' }} text-nowrap">
                                                             {{ $meetingInvestor && $meetingInvestor->invitation_sent ? __('Renvoyer invitation') : __('Envoyer invitation') }}
                                                         </button>
                                                     </form>
                                                 </div>
                                             </td>
                                         </tr>
                                         @empty
                                             <tr>
                                                 <td colspan="7" class="text-center py-4 text-base-content/70">
                                                     {{ __('No investors have been added to this meeting.') }}
                                                 </td>
                                             </tr>
                                         @endforelse
                                     </tbody>
                                 </table>
                             </div>
                         </div>
                     </div>
                 </div>

                 <!-- Sidebar with Meeting Info -->
                 <div class="lg:col-span-1 space-y-6">
                     <!-- Actions Panel -->
                     <div class="card card-bordered bg-base-100 shadow-lg">
                         <div class="card-body">
                             <h2 class="card-title text-primary">{{ __('Actions') }}</h2>
                             <div class="space-y-3 mt-4">
                                 <a href="{{ route('admin.meetings.investors.export-pdf', $meeting) }}"
                                     class="btn btn-primary w-full flex items-center justify-center">
                                     {{ __('Download Meeting') }}
                                 </a>
                                 <a href="{{ route('admin.attendance.scanner', $meeting) }}"
                                     class="btn btn-warning w-full">
                                     {{ __('Scan QR Code') }}
                                 </a>
                                 <a href="{{ route('admin.meetings.show', $meeting) }}" class="btn w-full">
                                     {{ __('Back to Meeting') }}
                                 </a>
                                 <a href="{{ route('admin.meetings.edit', $meeting) }}" class="btn btn-outline w-full">
                                     {{ __('Edit Meeting') }}
                                 </a>
                             </div>
                         </div>
                     </div>

                     <!-- Meeting Information -->
                     <div class="card card-bordered bg-base-100 shadow-lg">
                         <div class="card-body">
                             <h2 class="card-title text-primary">{{ __('Meeting Information') }}</h2>

                             <div class="space-y-4 mt-2">
                                 <div>
                                     <div class="text-sm font-medium text-base-content/70">{{ __('Date & Time') }}</div>
                                     <div class="mt-1 text-sm font-medium">
                                         {{ $meeting->timeSlot->date->format('l, F j, Y') }}
                                     </div>
                                     <div class="text-sm text-base-content/70">
                                         {{ $meeting->timeSlot->start_time->format('H:i') }} -
                                         {{ $meeting->timeSlot->end_time->format('H:i') }}
                                     </div>
                                 </div>

                                 <div>
                                     <div class="text-sm font-medium text-base-content/70">{{ __('Status') }}</div>
                                     <div class="mt-1">
                                         @switch($meeting->status->value)
                                             @case($MEETING_STATUS::SCHEDULED->value)
                                                 <span class="badge badge-info">{{ __($meeting->status->label()) }}</span>
                                             @break

                                             @case($MEETING_STATUS::COMPLETED->value)
                                                 <span class="badge badge-success">{{ __($meeting->status->label()) }}</span>
                                             @break

                                             @case($MEETING_STATUS::CANCELLED->value)
                                                 <span class="badge badge-error">{{ __($meeting->status->label()) }}</span>
                                             @break

                                             @case($MEETING_STATUS::PENDING->value)
                                                 <span class="badge badge-warning">{{ __($meeting->status->label()) }}</span>
                                             @break

                                             @case($MEETING_STATUS::CONFIRMED->value)
                                                 <span class="badge badge-success">{{ __($meeting->status->label()) }}</span>
                                             @break

                                             @case($MEETING_STATUS::DECLINED->value)
                                                 <span class="badge badge-error">{{ __($meeting->status->label()) }}</span>
                                             @break

                                             @default
                                                 <span class="badge badge-neutral">{{ __($meeting->status->label()) }}</span>
                                         @endswitch
                                     </div>
                                 </div>

                                 <div>
                                     <div class="text-sm font-medium text-base-content/70">{{ __('Issuer') }}</div>
                                     <div class="mt-1 text-sm font-medium">
                                         {{ $meeting->issuer->name . ' ' . $meeting->issuer->first_name }}
                                     </div>
                                     <div class="text-xs text-base-content/70">
                                         {{ $meeting->issuer->organization->name ?? __('No Organization') }}
                                     </div>
                                 </div>

                                 <div>
                                     <div class="text-sm font-medium text-base-content/70">{{ __('Room') }}</div>
                                     <div class="mt-1 text-sm">
                                         @if ($meeting->room)
                                             {{ $meeting->room->name }}
                                             <span class="text-xs text-base-content/70">(Capacity:
                                                 {{ $meeting->room->capacity }})</span>
                                         @else
                                             <span class="text-base-content/50">{{ __('No Room Assigned') }}</span>
                                         @endif
                                     </div>
                                 </div>

                                 <div>
                                     <div class="text-sm font-medium text-base-content/70">{{ __('Meeting Type') }}</div>
                                     <div class="mt-1 text-sm">
                                         @if ($meeting->is_one_on_one)
                                             <span class="badge badge-primary">{{ __('One-on-One (individual)') }}</span>
                                         @else
                                             <span class="badge badge-secondary">{{ __('Group') }}</span>
                                         @endif
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </section>

         <script>
             // Animation lors de la soumission du formulaire
             document.addEventListener('DOMContentLoaded', function() {
                 const statusSelects = document.querySelectorAll('.status-select');
                 statusSelects.forEach(select => {
                     select.addEventListener('change', function() {
                         this.classList.add('animate-pulse');
                         setTimeout(() => {
                             this.classList.remove('animate-pulse');
                         }, 1500);
                     });
                 });
             });

             // Alpine.js function for managing investor checkboxes
             function investorsTable() {
                 return {
                     toggleAll(event) {
                         const checkboxes = document.querySelectorAll('.investor-checkbox');
                         const isChecked = event.target.checked;

                         // Clear or fill the list of selected investors
                         this.selectedInvestors = [];

                         if (isChecked) {
                             // Select all investors
                             checkboxes.forEach(checkbox => {
                                 checkbox.checked = true;
                                 this.selectedInvestors.push(parseInt(checkbox.value));
                             });
                         } else {
                             // Deselect all investors
                             checkboxes.forEach(checkbox => {
                                 checkbox.checked = false;
                             });
                         }
                     },
                     toggleInvestor(investorId) {
                         const index = this.selectedInvestors.indexOf(investorId);

                         if (index === -1) {
                             // Add to selected list
                             this.selectedInvestors.push(investorId);
                         } else {
                             // Remove from selected list
                             this.selectedInvestors.splice(index, 1);
                         }

                         // Update "Select All" checkbox
                         const checkboxes = document.querySelectorAll('.investor-checkbox');
                         const selectAllCheckbox = document.getElementById('select-all-investors');
                         selectAllCheckbox.checked = this.selectedInvestors.length === checkboxes.length;
                     }
                 }
             }

             // QR Code Copy Functionality
             document.addEventListener('DOMContentLoaded', function() {
                 const copyButtons = document.querySelectorAll('.copy-btn');

                 copyButtons.forEach(button => {
                     button.addEventListener('click', function() {
                         const qrCode = this.getAttribute('data-qr-code');
                         const buttonId = this.getAttribute('data-id');
                         navigator.clipboard.writeText(qrCode)
                             .then(() => {
                                 // Change button text temporarily to show success
                                 const originalHTML = this.innerHTML;
                                 this.innerHTML =
                                     `<x-heroicon-o-check class="size-3" /> {{ __('Copied!') }}`;
                                 this.classList.remove('btn-accent');
                                 this.classList.add('btn-success');

                                 setTimeout(() => {
                                     this.innerHTML = originalHTML;
                                     this.classList.remove('btn-success');
                                     this.classList.add('btn-accent');
                                 }, 2000);
                             })
                             .catch(err => {
                                 console.error('Failed to copy text: ', err);
                                 alert('{{ __('Failed to copy QR code') }}');
                             });
                     });
                 });
             });
         </script>
     </x-app-layout>
