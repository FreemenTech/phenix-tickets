<?php

use App\Http\Controllers\BecomeOrganizerController;
use App\Http\Controllers\MyOrdersController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Organizer\DashboardController;
use App\Http\Controllers\Organizer\EventController;
use App\Http\Controllers\Organizer\RegistrationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\TicketController;
use App\Http\Middleware\EnsureOrderOwner;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $events = Event::published()
        ->where('starts_at', '>=', now())
        ->with('ticketTypes')
        ->orderBy('starts_at')
        ->get();

    return view('welcome', ['events' => $events, 'featured' => $events->first()]);
});

// Pages publiques
Route::get('/evenements/{event:slug}', [PublicEventController::class, 'show'])->name('events.show');
Route::get('/billet/{code}', [TicketController::class, 'show'])->name('tickets.show');
Route::get('/paiement/retour', [PaymentController::class, 'callback'])->name('payment.callback');

// « Se connecter pour commander » : on revient sur l'événement après la connexion
Route::get('/evenements/{event:slug}/connexion', function (Event $event) {
    session(['url.intended' => route('events.show', $event->slug)]);

    return redirect()->route('login');
})->name('events.login');

Route::get('/evenements/{event:slug}/inscription', function (Event $event) {
    session(['url.intended' => route('events.show', $event->slug)]);

    return redirect()->route('register');
})->name('events.register');

// Aiguillage après connexion ou inscription
Route::get('/dashboard', [SpaceController::class, 'home'])->middleware(['auth', 'verified'])->name('dashboard');

// Inscription des organisateurs (lien à leur envoyer)
Route::middleware('guest')->group(function () {
    Route::get('/organisateur/inscription', [RegistrationController::class, 'create'])->name('organizer.register');
    Route::post('/organisateur/inscription', [RegistrationController::class, 'store'])->name('organizer.register.store');
});

// Espace connecté
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/choisir-espace', [SpaceController::class, 'choose'])->name('space.choose');
    Route::get('/devenir-organisateur', [BecomeOrganizerController::class, 'create'])->name('organizer.become');
    Route::post('/devenir-organisateur', [BecomeOrganizerController::class, 'store'])->name('organizer.become.store');

    // Commander demande un compte
    Route::get('/mes-billets', [MyOrdersController::class, 'index'])->name('orders.index');
    Route::post('/evenements/{event:slug}/commander', [OrderController::class, 'store'])->name('orders.store');

    Route::middleware(EnsureOrderOwner::class)->group(function () {
        Route::get('/commande/{order:reference}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/commande/{order:reference}/payer', [PaymentController::class, 'pay'])->name('orders.pay');
        Route::get('/commande/{order:reference}/billets.pdf', [TicketController::class, 'orderPdf'])->name('orders.pdf');
    });
});

// Espace organisateur
Route::middleware(['auth', 'role:organizer'])->prefix('organisateur')->name('organizer.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/evenements/creer', [EventController::class, 'create'])->name('events.create');
    Route::post('/evenements', [EventController::class, 'store'])->name('events.store');
});

// Scanner des billets (admins et organisateurs)
Route::middleware(['auth', 'role:admin,organizer'])->group(function () {
    Route::get('/scanner', [ScannerController::class, 'index'])->name('scanner.index');
    Route::post('/scanner/valider', [ScannerController::class, 'validateTicket'])->name('scanner.validate');
});


require __DIR__.'/auth.php';

// Modifier / supprimer un événement (organisateur)
Route::middleware(['auth', 'role:organizer'])->prefix('organisateur')->name('organizer.')->group(function () {
    Route::get('/evenements/{event}/modifier', [\App\Http\Controllers\Organizer\EventController::class, 'edit'])->name('events.edit');
    Route::put('/evenements/{event}', [\App\Http\Controllers\Organizer\EventController::class, 'update'])->name('events.update');
    Route::delete('/evenements/{event}', [\App\Http\Controllers\Organizer\EventController::class, 'destroy'])->name('events.destroy');
});

// Confirmation de secours des paiements (appelée par FedaPay, sans session ni CSRF)
Route::post('/webhooks/fedapay', [\App\Http\Controllers\PaymentController::class, 'webhook'])
    ->withoutMiddleware('web')
    ->name('webhooks.fedapay');

// Espace organisateur : événements (statistiques) et reversements
Route::middleware(['auth', 'role:organizer'])->prefix('organisateur')->name('organizer.')->group(function () {
    Route::get('/evenements', [\App\Http\Controllers\Organizer\EventStatsController::class, 'index'])->name('events.index');
    Route::get('/evenements/{event}/statistiques', [\App\Http\Controllers\Organizer\EventStatsController::class, 'show'])->whereNumber('event')->name('events.show');
    Route::get('/reversements', [\App\Http\Controllers\Organizer\PayoutController::class, 'index'])->name('payouts');
    Route::post('/reversements/profil', [\App\Http\Controllers\Organizer\PayoutController::class, 'updateProfile'])->name('payouts.profile');
});

// Espace admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('home');
    Route::get('/organisateurs', [\App\Http\Controllers\Admin\OrganizerController::class, 'index'])->name('organizers');
    Route::post('/organisateurs/{organizer}/approuver', [\App\Http\Controllers\Admin\OrganizerController::class, 'approve'])->name('organizers.approve');
    Route::post('/organisateurs/{organizer}/suspendre', [\App\Http\Controllers\Admin\OrganizerController::class, 'suspend'])->name('organizers.suspend');
    Route::get('/ventes', [\App\Http\Controllers\Admin\SalesController::class, 'index'])->name('sales');
    Route::get('/reversements', [\App\Http\Controllers\Admin\PayoutController::class, 'index'])->name('payouts');
    Route::post('/reversements', [\App\Http\Controllers\Admin\PayoutController::class, 'store'])->name('payouts.store');
});

// Pages d'information
Route::view('/aide', 'pages.help')->name('help');
Route::get('/contact', [\App\Http\Controllers\ContactController::class, 'create'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])->middleware('throttle:6,10')->name('contact.store');

// Messages de contact (admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/messages', [\App\Http\Controllers\Admin\MessageController::class, 'index'])->name('messages');
    Route::post('/messages/{message}/lu', [\App\Http\Controllers\Admin\MessageController::class, 'markRead'])->name('messages.read');
    Route::delete('/messages/{message}', [\App\Http\Controllers\Admin\MessageController::class, 'destroy'])->name('messages.destroy');
});

// Supprimer une commande non payée (acheteur uniquement)
Route::middleware(['auth', \App\Http\Middleware\EnsureOrderOwner::class])
    ->delete('/commande/{order:reference}', [\App\Http\Controllers\MyOrdersController::class, 'destroy'])
    ->name('orders.destroy');

// Pages légales
Route::view('/conditions-utilisation', 'pages.terms')->name('legal.terms');
Route::view('/confidentialite', 'pages.privacy')->name('legal.privacy');
Route::view('/mentions-legales', 'pages.legal-notice')->name('legal.notice');

// Annulation d'un événement (organisateur)
Route::middleware(['auth', 'role:organizer'])->prefix('organisateur')->name('organizer.')->group(function () {
    Route::post('/evenements/{event}/annuler', [\App\Http\Controllers\Organizer\EventController::class, 'cancel'])
        ->whereNumber('event')
        ->name('events.cancel');
});

// Événements, annulations et remboursements (admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/evenements', [\App\Http\Controllers\Admin\EventController::class, 'index'])->name('events');
    Route::post('/evenements/{event}/annuler', [\App\Http\Controllers\Admin\EventController::class, 'cancel'])->name('events.cancel');
    Route::post('/commandes/{order}/rembourser', [\App\Http\Controllers\Admin\SalesController::class, 'refund'])->name('orders.refund');
});
