<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ActivationEmail;
use App\Models\Organization;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role (profile)
        $role = $request->input('role');
        if ($role && $role !== 'all') {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        } else {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('name', [UserRole::ISSUER->value, UserRole::INVESTOR->value]);
            });
        }

        // Filter by status
        $status = $request->input('status');
        if ($status !== null && $status !== 'all') {
            $query->where('status', $status);
        }

        // Search by name, first_name, email or phone
        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('organization', function ($org) use ($search) {
                      $org->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Gestion du tri
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');

        // Appliquer le tri
        if ($sortBy === 'organization_id') {
            // Tri spécial pour l'organisation (jointure)
            $query->leftJoin('organizations', 'users.organization_id', '=', 'organizations.id')
                  ->orderBy('organizations.name', $sortOrder)
                  ->select('users.*'); // Éviter les conflits de colonnes
        } else {
            // Tri standard sur les colonnes de l'utilisateur
            $query->orderBy($sortBy, $sortOrder);
        }

        // Récupérer le nombre d'éléments par page depuis la requête ou utiliser la valeur par défaut
        $perPage = $request->input('perPage', 10);

        $users = $query->with(['roles', 'organization', 'organization.country'])->paginate($perPage)->withQueryString();

        // Calculate statistics for the dashboard cards
        $statistics = [
            'total_users' => User::whereHas('roles', function ($q) {
                $q->whereIn('name', [UserRole::ISSUER->value, UserRole::INVESTOR->value]);
            })->count(),

            'total_investors' => User::whereHas('roles', function ($q) {
                $q->where('name', UserRole::INVESTOR->value);
            })->count(),

            'total_issuers' => User::whereHas('roles', function ($q) {
                $q->where('name', UserRole::ISSUER->value);
            })->count(),

            'active_users' => User::whereHas('roles', function ($q) {
                $q->whereIn('name', [UserRole::ISSUER->value, UserRole::INVESTOR->value]);
            })->where('status', true)->count(),

            'inactive_users' => User::whereHas('roles', function ($q) {
                $q->whereIn('name', [UserRole::ISSUER->value, UserRole::INVESTOR->value]);
            })->where('status', false)->count(),
        ];

        return view('admin.users.index', compact('users', 'sortBy', 'sortOrder', 'statistics'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Récupérer uniquement les organisations qui ne sont pas encore associées à un utilisateur
        $organizations = Organization::whereDoesntHave('users')->get();
        $roles = Role::whereIn('name', [UserRole::ISSUER->value, UserRole::INVESTOR->value])->get();

        return view('admin.users.create', compact('organizations', 'roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'organization_id' => 'required|exists:organizations,id',
            'role' => 'required|exists:roles,name',
            'status' => 'boolean',
            'send_activation_email' => 'boolean'
        ]);

        $status = isset($validated['status']) ? true : false;

        $user = User::create([
            'first_name' => $validated['first_name'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'position' => $validated['position'] ?? null,
            'organization_id' => $validated['organization_id'],
            'status' => $status,
        ]);

        $user->assignRole($validated['role']);

        // Send activation email if checkbox is checked
        if (isset($validated['send_activation_email']) && $validated['send_activation_email']) {
            // Generate a new random password
            $password = Str::random(10);
            $user->password = Hash::make($password);
            $user->save();

            // Send email with credentials
            Mail::to($user->email)->send(new ActivationEmail($user, $password));
        }

        return redirect()->route('admin.users.index')
                        ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Récupérer les organisations qui n'ont pas d'utilisateurs et ajouter l'organisation actuelle de l'utilisateur
        $organizations = Organization::where(function($query) use ($user) {
            $query->whereDoesntHave('users')
                  ->orWhere('id', $user->organization_id);
        })->get();

        $roles = Role::whereIn('name', [UserRole::ISSUER->value, UserRole::INVESTOR->value])->get();

        return view('admin.users.edit', compact('user', 'organizations', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'organization_id' => 'required|exists:organizations,id',
            'role' => 'required|exists:roles,name',
        ];

        // Validation conditionnelle pour le mot de passe
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $validated = $request->validate($rules);

        $status = $request->has('status') ? true : false;

        // Mettre à jour les données de l'utilisateur
        $user->first_name = $validated['first_name'];
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->position = $validated['position'] ?? null;
        $user->organization_id = $validated['organization_id'];
        $user->status = $status;

        // Mettre à jour le mot de passe si fourni
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Mise à jour du rôle en préservant le contexte
        // Vérifier si le rôle est différent avant de le modifier
        $currentRole = $user->roles->first() ? $user->roles->first()->name : null;

        if ($currentRole !== $validated['role']) {
            // Mettre à jour le rôle uniquement s'il a changé
            $user->syncRoles([$validated['role']]);
        }

        return redirect()->route('admin.users.index')
                        ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')
                            ->with('error', 'Cannot delete an admin user from this interface.');
        }

        // Check if user has created meetings
        if ($user->createdMeetings()->exists()) {
            return redirect()->route('admin.users.index')
                            ->with('error', 'Cannot delete this user because they have created meetings. Please reassign or delete the meetings first.');
        }

        try {
            $user->delete();
            return redirect()->route('admin.users.index')
                            ->with('success', 'User deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Check for foreign key constraint error
            if ($e->getCode() == 23000) {
                return redirect()->route('admin.users.index')
                                ->with('error', 'Cannot delete this user because they are referenced by other records in the system.');
            }
            throw $e;
        }
    }

    /**
     * Send activation email to the user.
     */
    public function sendActivationEmail(User $user)
    {
        // Generate a random password
        $password = Str::random(10);
        $user->password = Hash::make($password);
        $user->save();

        // Send email with credentials
        Mail::to($user->email)->send(new ActivationEmail($user, $password));

        return redirect()->back()->with('success', 'Activation email sent successfully.');
    }

    /**
     * Send activation emails to multiple users.
     */
    public function sendMultipleActivationEmails(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $count = 0;
        foreach ($validated['user_ids'] as $userId) {
            $user = User::findOrFail($userId);

            // Generate a random password
            $password = Str::random(10);
            $user->password = Hash::make($password);
            $user->save();

            // Send email with credentials
            Mail::to($user->email)->send(new ActivationEmail($user, $password));

            $count++;
        }

        return redirect()->back()->with('success', $count . ' activation emails sent successfully.');
    }

    /**
     * Toggle status for multiple users at once.
     */
    public function toggleMultipleStatus(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'status' => 'required|boolean'
        ]);

        $status = (bool) $validated['status'];
        $count = 0;

        foreach ($validated['user_ids'] as $userId) {
            $user = User::findOrFail($userId);
            $user->status = $status;
            $user->save();
            $count++;
        }

        $statusText = $status ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', $count . ' users ' . $statusText . ' successfully.');
    }

    /**
     * Export users to Excel/CSV.
     */
    public function export(Request $request)
    {
        // Récupérer les filtres actuels
        $role = $request->input('role', 'all');
        $status = $request->input('status');
        $search = $request->input('search');

        // Générer un nom de fichier dynamique
        $fileName = 'users_' . now()->format('Y-m-d_His') . '.xlsx';

        // Exporter les données
        return Excel::download(new UsersExport($role, $status, $search), $fileName);
    }

    /**
     * Show import form.
     */
    public function showImportForm()
    {
        return view('admin.users.import');
    }

    /**
     * Download user import template.
     */
    public function downloadTemplate()
    {
        $file_path = public_path('../temp_excel_files/users_template.xlsx');
        return response()->download($file_path, 'users_template.xlsx');
    }

    /**
     * Import users from Excel/CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls|max:10240', // 10MB max
        ]);

        try {
            Excel::import(new UsersImport, $request->file('file'));

            return redirect()->route('admin.users.index')
                ->with('success', 'Users imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }

            return redirect()->back()
                ->with('error', 'Import failed. ' . implode('<br>', $errors))
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Import failed. ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Generate time slots for an issuer user
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateTimeSlots(User $user)
    {
        if (!$user->hasRole(UserRole::ISSUER->value)) {
            return redirect()->back()->with('error', 'Only issuer users can have time slots.');
        }

        try {
            // Check if the user already has time slots with meetings
            if ($user->timeSlots()->whereHas('meetings')->exists()) {
                return redirect()->back()->with('error', 'Cannot regenerate time slots because this user has time slots with scheduled meetings.');
            }

            // Use the command we created earlier with --force option to regenerate slots
            $output = Artisan::call('issuers:generate-timeslots', [
                '--user-id' => $user->id,
                '--force' => true
            ]);

            // Get command output to display appropriate message
            $commandOutput = Artisan::output();

            if (strpos($commandOutput, 'already has time slots') !== false) {
                return redirect()->back()->with('info', 'User already has time slots. No changes made.');
            }

            return redirect()->back()->with('success', 'Time slots have been generated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to generate time slots: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate time slots: ' . $e->getMessage());
        }
    }

    /**
     * Show the QR code scanner page for users.
     */
    public function showScanner()
    {
        return view('admin.users.scanner');
    }

    /**
     * Verify a QR code to find a user.
     */
    public function verifyQrCode(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $user = User::where('qr_code', $request->qr_code)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid QR code or user not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => __('User found successfully.'),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'first_name' => $user->first_name,
                'email' => $user->email,
                'organization' => $user->organization ? $user->organization->name : null,
            ],
        ]);
    }

    /**
     * Get detailed user information and meetings.
     */
    public function getUserDetails(User $user)
    {
        // Load relationships
        $user->load(['organization', 'roles']);

        // Get user role information
        $userRole = $user->hasRole(UserRole::ISSUER->value) ? 'issuer' : 'investor';

        // Format user data
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'position' => $user->position,
            'organization' => $user->organization ? $user->organization->name : null,
            'role' => $userRole,
            'role_label' => $userRole === 'issuer' ? UserRole::ISSUER->label() : UserRole::INVESTOR->label(),
        ];

        // Get meetings based on user role
        $meetings = [];

        if ($userRole === 'issuer') {
            // Get meetings where this user is the issuer
            $issuerMeetings = $user->issuerMeetings()->with(['timeSlot', 'room'])->get();

            foreach ($issuerMeetings as $meeting) {
                $meetings[] = [
                    'id' => $meeting->id,
                    'date' => $meeting->timeSlot->date->format('d/m/Y'),
                    'time' => $meeting->timeSlot->start_time->format('H:i') . ' - ' . $meeting->timeSlot->end_time->format('H:i'),
                    'role' => 'issuer',
                    'role_label' => UserRole::ISSUER->label(),
                    'status_color' => $meeting->status->color(),
                    'status_label' => $meeting->status->label(),
                ];
            }
        } else {
            // Get meetings where this user is an investor
            $investorMeetings = $user->investorMeetings()->with(['timeSlot', 'room'])->get();

            foreach ($investorMeetings as $meeting) {
                // Get the investor's status from the pivot table
                $pivotStatus = $meeting->pivot->status ?? 'pending';
                $statusEnum = \App\Enums\InvestorStatus::from($pivotStatus);

                $meetings[] = [
                    'id' => $meeting->id,
                    'date' => $meeting->timeSlot->date->format('d/m/Y'),
                    'time' => $meeting->timeSlot->start_time->format('H:i') . ' - ' . $meeting->timeSlot->end_time->format('H:i'),
                    'role' => 'investor',
                    'role_label' => UserRole::INVESTOR->label(),
                    'status_color' => $statusEnum->color(),
                    'status_label' => $statusEnum->label(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'user' => $userData,
            'meetings' => $meetings,
        ]);
    }
}
