<?php

use App\Enums\UserRole;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DiagramController;
use App\Http\Controllers\Admin\IssuerScheduleController as AdminIssuerScheduleController;
use App\Http\Controllers\Admin\MeetingController as AdminMeetingController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\AdministratorController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TimeSlotController;
use App\Http\Controllers\Investor\DashboardController as InvestorDashboardController;
use App\Http\Controllers\Issuer\DashboardController as IssuerDashboardController;
use App\Http\Controllers\Issuer\ScheduleController as IssuerScheduleController;
use App\Http\Controllers\Issuer\MeetingController as IssuerMeetingController;
use App\Http\Controllers\Issuer\QuestionController as IssuerQuestionController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Route pour changer de langue
Route::get('language/{locale}', [LanguageController::class, 'switchLang'])->name('language.switch');

Route::get('/', function () {
    return redirect()->route('login');
});

// Redirection des préfixes vers les dashboards respectifs
Route::middleware('auth')->group(function () {
    // Redirection /admin vers /admin/dashboard
    Route::get('/admin', function () {
        return redirect()->route('admin.dashboard');
    })->middleware('role:admin');

    // Redirection /investor vers /investor/dashboard
    Route::get('/investor', function () {
        return redirect()->route('investor.dashboard');
    })->middleware('role:investor');

    // Redirection /issuer vers /issuer/dashboard
    Route::get('/issuer', function () {
        return redirect()->route('issuer.dashboard');
    })->middleware('role:issuer');

    // Redirection du dashboard en fonction du rôle utilisateur
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole(UserRole::ADMIN->value)) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole(UserRole::ISSUER->value)) {
            return redirect()->route('issuer.dashboard');
        } elseif ($user->hasRole(UserRole::INVESTOR->value)) {
            return redirect()->route('investor.dashboard');
        }
    })->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes pour le Dashboard admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Organizations Management
        Route::resource('organizations', OrganizationController::class);
        Route::get('/organizations-export', [OrganizationController::class, 'export'])->name('organizations.export');
        Route::get('/organizations-import', [OrganizationController::class, 'showImportForm'])->name('organizations.import.form');
        Route::post('/organizations-import', [OrganizationController::class, 'import'])->name('organizations.import');
        Route::get('/organizations-download-template', [OrganizationController::class, 'downloadTemplate'])->name('organizations.download-template');

        // User QR Code Scanner
        Route::get('/users/scanner', [UserController::class, 'showScanner'])->name('users.scanner');
        Route::post('/users/verify-qr', [UserController::class, 'verifyQrCode'])->name('users.verify-qr');
        Route::get('/users/{user}/details', [UserController::class, 'getUserDetails'])->name('users.details');

        // Users Management
        Route::patch('/users/toggle-multiple-status', [UserController::class, 'toggleMultipleStatus'])->name('users.toggle-multiple-status');
        Route::post('/users/{user}/activation-email', [UserController::class, 'sendActivationEmail'])->name('users.send-activation-email');
        Route::post('/users/send-multiple-activation-emails', [UserController::class, 'sendMultipleActivationEmails'])->name('users.send-multiple-activation-emails');
        Route::resource('users', UserController::class);

        // Import/Export Users
        Route::get('/users-import', [UserController::class, 'showImportForm'])->name('users.import.form');
        Route::post('/users-import', [UserController::class, 'import'])->name('users.import');
        Route::get('/users-download-template', [UserController::class, 'downloadTemplate'])->name('users.download-template');
        Route::get('/users-export', [UserController::class, 'export'])->name('users.export');

        // Administrator Management - routes complètes pour CRUD
        Route::get('/administrators', [AdministratorController::class, 'index'])->name('administrators');
        Route::get('/administrators/create', [AdministratorController::class, 'create'])->name('administrators.create');
        Route::post('/administrators', [AdministratorController::class, 'store'])->name('administrators.store');
        Route::get('/administrators/{administrator}/edit', [AdministratorController::class, 'edit'])->name('administrators.edit');
        Route::put('/administrators/{administrator}', [AdministratorController::class, 'update'])->name('administrators.update');
        Route::delete('/administrators/{administrator}', [AdministratorController::class, 'destroy'])->name('administrators.destroy');
        Route::resource('rooms', RoomController::class);

        // Meeting Management - routes complètes pour CRUD
        Route::resource('meetings', AdminMeetingController::class);
        Route::get('/meetings-export', [AdminMeetingController::class, 'export'])->name('meetings.export');
        Route::get('/meetings/{meeting}/investors-export-pdf', [AdminMeetingController::class, 'exportInvestorsPdf'])->name('meetings.investors.export-pdf');
        Route::get('/meetings/{meeting}/qrcode-export-pdf/{investor}', [AdminMeetingController::class, 'exportQrCodePdf'])->name('meetings.qrcode.export-pdf');
        Route::get('/meetings/{meeting}/investors', [AdminMeetingController::class, 'investors'])->name('meetings.investors');
        Route::patch('/meetings/{meeting}/investors/{investor}/status', [AdminMeetingController::class, 'updateInvestorStatus'])->name('meetings.update-investor-status');
        Route::patch('/meetings/{meeting}/update-multiple-investors', [AdminMeetingController::class, 'updateMultipleInvestorsStatus'])->name('meetings.update-multiple-investors');
        Route::post('/meetings/{meeting}/investors/{investor}/send-invitation', [AdminMeetingController::class, 'sendInvitationEmail'])->name('meetings.send-invitation');
        Route::post('/meetings/{meeting}/send-multiple-invitations', [AdminMeetingController::class, 'sendMultipleInvitationEmails'])->name('meetings.send-multiple-invitations');

        // Attendance QR code scanning
        Route::get('/meetings/{meeting}/attendance/scanner', [App\Http\Controllers\Admin\AttendanceController::class, 'showScanner'])->name('attendance.scanner');
        Route::post('/attendance/verify', [App\Http\Controllers\Admin\AttendanceController::class, 'verifyQrCode'])->name('attendance.verify');
        Route::get('/meetings/{meeting}/investors/{investor}/qrcode', [App\Http\Controllers\Admin\AttendanceController::class, 'showQrCode'])->name('attendance.qrcode');
        Route::get('/meetings/{meeting}/attendance/tables', [App\Http\Controllers\Admin\AttendanceController::class, 'getInvestorTables'])->name('attendance.tables');

        // Issuer Schedule Management
        Route::get('/users/{user}/schedule', [AdminIssuerScheduleController::class, 'show'])->name('users.schedule');
        Route::patch('/users/{user}/timeslots/{timeSlot}', [AdminIssuerScheduleController::class, 'update'])->name('users.timeslots.update');
        Route::patch('/users/{user}/schedule/{date}', [AdminIssuerScheduleController::class, 'updateByDate'])->name('users.schedule.update-by-date');
        Route::patch('/users/{user}/schedule/{date}/batch-update', [AdminIssuerScheduleController::class, 'batchUpdate'])->name('users.schedule.batch-update');

        // TimeSlots generation
        Route::post('/users/{user}/generate-time-slots', [UserController::class, 'generateTimeSlots'])->name('users.generate-time-slots');

        // Questions - Admin can delete any question
        Route::delete('questions/{question}', [App\Http\Controllers\QuestionController::class, 'adminDestroy'])->name('questions.destroy');

        // Routes pour les diagrammes
        Route::get('/diagrams', [DiagramController::class, 'index'])->name('diagrams.index');
        Route::get('/diagrams/classes', [DiagramController::class, 'classes'])->name('diagrams.classes');
        Route::get('/diagrams/sequences', [DiagramController::class, 'sequences'])->name('diagrams.sequences');
        Route::get('/diagrams/packages', [DiagramController::class, 'packages'])->name('diagrams.packages');
        Route::get('/diagrams/use-cases', [DiagramController::class, 'useCases'])->name('diagrams.use-cases');
        Route::get('/diagrams/global', [DiagramController::class, 'global'])->name('diagrams.global');
        Route::get('/diagrams/permissions', [DiagramController::class, 'permissions'])->name('diagrams.permissions');
        Route::get('/diagrams/authentication', [DiagramController::class, 'authentication'])->name('diagrams.authentication');
        Route::get('/diagrams/meetings', [DiagramController::class, 'meetings'])->name('diagrams.meetings');
        Route::get('/diagrams/exports', [DiagramController::class, 'exports'])->name('diagrams.exports');
    });

    // Routes pour le Dashboard investisseur
    Route::middleware('role:investor')->prefix('investor')->name('investor.')->group(function () {
        Route::get('/dashboard', [InvestorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/calendar', [InvestorDashboardController::class, 'calendar'])->name('calendar');
        Route::get('/statistics', [InvestorDashboardController::class, 'statistics'])->name('statistics');

        // Nouvelles routes pour les meetings et les issuers
        Route::get('/meetings', [App\Http\Controllers\Investor\MeetingController::class, 'index'])->name('meetings.index');
        Route::get('/meetings/{meeting}', [App\Http\Controllers\Investor\MeetingController::class, 'show'])->name('meetings.show');
        Route::post('/meetings/request', [App\Http\Controllers\Investor\MeetingController::class, 'requestMeeting'])->name('meetings.request');
        Route::get('/meeting/request/{issuer}', [App\Http\Controllers\Investor\MeetingController::class, 'showRequestForm'])->name('meeting.request');

        Route::get('/issuers', [App\Http\Controllers\Investor\IssuerController::class, 'index'])->name('issuers.index');
        Route::get('/issuers/{issuer}', [App\Http\Controllers\Investor\IssuerController::class, 'show'])->name('issuers.show');

        // Questions Management
        Route::post('/questions', [App\Http\Controllers\Investor\QuestionController::class, 'store'])->name('questions.store');

        // QR Code Management
        Route::get('/qr-code', [App\Http\Controllers\Investor\QrCodeController::class, 'show'])->name('qr-code.show');
        Route::get('/qr-code/download', [App\Http\Controllers\Investor\QrCodeController::class, 'download'])->name('qr-code.download');
    });

    // Routes pour le Dashboard émetteur
    Route::middleware('role:issuer')->prefix('issuer')->name('issuer.')->group(function () {
        Route::get('/dashboard', [IssuerDashboardController::class, 'index'])->name('dashboard');

        // Schedule Management
        Route::get('/schedule', [IssuerScheduleController::class, 'index'])->name('schedule');
        Route::patch('/timeslots/{timeSlot}', [IssuerScheduleController::class, 'update'])->name('timeslots.update');
        Route::patch('/schedule/{date}', [IssuerScheduleController::class, 'updateByDate'])->name('schedule.update-by-date');
        Route::patch('/schedule/{date}/batch-update', [IssuerScheduleController::class, 'batchUpdate'])->name('schedule.batch-update');

        // Route pour générer les TimeSlots côté issuer
        Route::post('/generate-time-slots', [IssuerScheduleController::class, 'generateTimeSlots'])->name('generate-time-slots');

        // Meetings Management
        Route::resource('meetings', IssuerMeetingController::class)->only(['index', 'show']);
        Route::put('/meetings/{meeting}/status', [IssuerMeetingController::class, 'updateStatus'])->name('meetings.update-status');
        Route::put('/meetings/{meeting}/investor/{investor}', [IssuerMeetingController::class, 'updateInvestorStatus'])->name('meetings.update-investor-status');

        // Questions Management
        Route::post('/questions/{question}/answer', [IssuerQuestionController::class, 'answer'])->name('questions.answer');

        // QR Code Management
        Route::get('/qr-code', [App\Http\Controllers\Issuer\QrCodeController::class, 'show'])->name('qr-code.show');
        Route::get('/qr-code/download', [App\Http\Controllers\Issuer\QrCodeController::class, 'download'])->name('qr-code.download');
    });
});

// Gestion des erreurs personnalisée
Route::fallback(function () {
    return response()->view("errors.404", [], 404);
});

// Routes pour tester les pages d'erreur (uniquement en environnement local ou de développement)
if (app()->environment('local', 'development')) {
    Route::prefix('error-test')->group(function () {
        Route::get('/{code}', function ($code) {
            if (!view()->exists("errors.{$code}")) {
                abort(404, "Error view {$code} does not exist");
            }

            $data = ['isAuthenticated' => auth()->check()];
            return response()->view("errors.{$code}", $data, (int)$code);
        })->where('code', '401|403|404|405|419|422|429|500|503');
    });
}

require __DIR__ . '/auth.php';
