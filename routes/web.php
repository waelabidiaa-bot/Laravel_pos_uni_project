<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureRole;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\{
    DashboardController as AdminDashboardController,
    CashierController,
    ProductController,
    CategoryController,
    StockController,
    SaleReportController,
    StatisticsController
};
use App\Http\Controllers\Cashier\{
    DashboardController as CashierDashboardController,
    CashSessionController,
    SaleController,
    SaleItemController,
    PaymentController
};
use App\Http\Controllers\comm\{
    ParametreController
};

/* ---------- Guest ---------- */
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});


/* ---------- Public ---------- */
Route::get('/parametre', [ParametreController::class, 'index'])
    ->name('parametre.index');

Route::put('/parametre', [ParametreController::class, 'update'])
    ->name('parametre.update');

Route::put('/theme', [ParametreController::class, 'updateTheme'])
    ->name('theme.update');


/* ---------- Authenticated ---------- */
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Send each role to its own dashboard
    Route::get('/', fn () => redirect()->route(
        auth()->user()->role === 'ADMIN' ? 'admin.dashboard' : 'cashier.dashboard'
    ))->name('home');

    /* ----- Admin ----- */
    Route::middleware(EnsureRole::class . ':ADMIN')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('cashiers', CashierController::class)->except('show');
        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('products', ProductController::class)->except('show');

        Route::get('stock', [StockController::class, 'index'])->name('stock.index');
        Route::patch('stock/{product}', [StockController::class, 'update'])->name('stock.update');

        Route::get('sales', [SaleReportController::class, 'index'])->name('sales.index');
        Route::get('sales/{sale}', [SaleReportController::class, 'show'])->name('sales.show');
        Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics.index'); 
    });

    /* ----- Cashier ----- */
    Route::middleware(EnsureRole::class . ':CASHIER')->prefix('cashier')->name('cashier.')->group(function () {
        Route::get('/', [CashierDashboardController::class, 'index'])->name('dashboard');

        // Cash session
        Route::post('sessions', [CashSessionController::class, 'store'])->name('sessions.store');
        Route::patch('sessions/{cashSession}/close', [CashSessionController::class, 'close'])->name('sessions.close');

        // Sale (POS screen)
        Route::post('sales', [SaleController::class, 'store'])->name('sales.store');
        Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
        Route::patch('sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
        Route::get('sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');

        // Sale items and payments
        Route::post('sales/{sale}/items', [SaleItemController::class, 'store'])->name('sales.items.store');
        Route::delete('sales/{sale}/items/{product}', [SaleItemController::class, 'destroy'])->name('sales.items.destroy');
        Route::post('sales/{sale}/payments', [PaymentController::class, 'store'])->name('sales.payments.store');
    });
});
