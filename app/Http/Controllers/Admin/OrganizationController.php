<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Country;
use App\Enums\UserRole;
use App\Enums\OrganizationType;
use App\Enums\Origin;
use Illuminate\Http\Request;
use App\Exports\OrganizationsExport;
use App\Imports\OrganizationsImport;
use Maatwebsite\Excel\Facades\Excel;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the organizations
     */
    public function index(Request $request)
    {
        $query = Organization::query();

        // Filter by profile
        $profile = $request->input('profile');
        if ($profile && $profile !== 'all') {
            $query->where('profil', $profile);
        }

        // Filter by origin
        $origin = $request->input('origin');
        if ($origin && $origin !== 'all') {
            $query->where('origin', $origin);
        }

        // Filter by organization type
        $type = $request->input('type');
        if ($type && $type !== 'all') {
            $query->where('organization_type', $type);
        }

        // Filter by country
        $country = $request->input('country');
        if ($country && $country !== 'all') {
            $query->where('country_id', $country);
        }

        // Search by name or description
        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Gestion du tri
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');

        // Appliquer le tri
        if ($sortBy === 'country_id') {
            // Tri spécial pour le pays (jointure)
            $query->leftJoin('countries', 'organizations.country_id', '=', 'countries.id')
                  ->orderBy('countries.name_en', $sortOrder)
                  ->select('organizations.*'); // Éviter les conflits de colonnes
        } else {
            // Tri standard sur les colonnes de l'organisation
            $query->orderBy($sortBy, $sortOrder);
        }

        $organizations = $query->with('country')->paginate(10)->withQueryString();
        $countries = Country::orderBy('name_en')->get();

        return view('admin.organizations.index', compact('organizations', 'countries', 'sortBy', 'sortOrder'));
    }

    /**
     * Show the form for creating a new organization.
     */
    public function create()
    {
        $countries = Country::orderBy('name_en')->get();
        return view('admin.organizations.create', compact('countries'));
    }

    /**
     * Store a newly created organization in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profil' => 'required|string',
            'origin' => 'required|string',
            'organization_type' => 'required|string',
            'organization_type_other' => 'nullable|string|max:255',
            'fiche_bkgr' => 'nullable|file|mimes:pdf|max:10240',
            'logo' => 'nullable|image|max:2048',
            'country_id' => 'required|exists:countries,id',
            'description' => 'nullable|string',
        ]);

        // Handle file uploads if present
        if ($request->hasFile('fiche_bkgr')) {
            $ficheBkgr = $request->file('fiche_bkgr');
            $ficheBkgrPath = $ficheBkgr->store('fiches', 'public');
            $validated['fiche_bkgr'] = $ficheBkgrPath;
        }

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('logos', 'public');
            $validated['logo'] = $logoPath;
        }

        Organization::create($validated);

        return redirect()->route('admin.organizations.index')
                        ->with('success', 'Organization created successfully.');
    }

    /**
     * Display the specified organization.
     */
    public function show(Organization $organization)
    {
        return view('admin.organizations.show', compact('organization'));
    }

    /**
     * Show the form for editing the specified organization.
     */
    public function edit(Organization $organization)
    {
        $countries = Country::orderBy('name_en')->get();
        return view('admin.organizations.edit', compact('organization', 'countries'));
    }

    /**
     * Update the specified organization in storage.
     */
    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profil' => 'required|string',
            'origin' => 'required|string',
            'organization_type' => 'required|string',
            'organization_type_other' => 'nullable|string|max:255',
            'fiche_bkgr' => 'nullable|file|mimes:pdf|max:10240',
            'logo' => 'nullable|image|max:2048',
            'country_id' => 'required|exists:countries,id',
            'description' => 'nullable|string',
        ]);

        // Handle file uploads if present
        if ($request->hasFile('fiche_bkgr')) {
            $ficheBkgr = $request->file('fiche_bkgr');
            $ficheBkgrPath = $ficheBkgr->store('fiches', 'public');
            $validated['fiche_bkgr'] = $ficheBkgrPath;
        }

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('logos', 'public');
            $validated['logo'] = $logoPath;
        }

        $organization->update($validated);

        return redirect()->route('admin.organizations.index')
                        ->with('success', 'Organization updated successfully.');
    }

    /**
     * Remove the specified organization from storage.
     */
    public function destroy(Organization $organization)
    {
        // Check if organization has users
        if ($organization->users()->count() > 0) {
            return redirect()->route('admin.organizations.index')
                            ->with('error', 'Cannot delete organization with associated users.');
        }

        $organization->delete();

        return redirect()->route('admin.organizations.index')
                        ->with('success', 'Organization deleted successfully.');
    }

    /**
     * Export organizations to Excel/CSV.
     */
    public function export(Request $request)
    {
        // Récupérer les filtres actuels
        $profile = $request->input('profile', 'all');
        $origin = $request->input('origin');
        $type = $request->input('type');
        $country = $request->input('country');
        $search = $request->input('search');

        // Générer un nom de fichier dynamique
        $fileName = 'organizations_' . now()->format('Y-m-d_His') . '.xlsx';

        // Exporter les données
        return Excel::download(new OrganizationsExport($profile, $origin, $type, $country, $search), $fileName);
    }

    /**
     * Show import form.
     */
    public function showImportForm()
    {
        return view('admin.organizations.import');
    }

    /**
     * Download organization import template.
     */
    public function downloadTemplate()
    {
        $file_path = public_path('../temp_excel_files/organizations_template.xlsx');
        return response()->download($file_path, 'organizations_template.xlsx');
    }

    /**
     * Import organizations from Excel/CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls|max:10240', // 10MB max
        ]);

        try {
            Excel::import(new OrganizationsImport, $request->file('file'));

            return redirect()->route('admin.organizations.index')
                ->with('success', 'Organizations imported successfully.');
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
}
