<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

Route::get('/categories', function () {
        return view('admin.categories.index');
    })->name('categories.index');


    // Index (Read All) - Mengarah ke file blade index
    Route::get('/products', function () {
        return view('admin.products.index');
    })->name('products.index');

    // Create (Form) - Mengarah ke file blade create
    Route::get('/products/create', function () {
        return view('admin.products.create');
    })->name('products.create');

    // Show (Detail) - Mengarah ke file blade show, butuh parameter ID
    Route::get('/products/{id}', function ($id) {
        return view('admin.products.show', ['id' => $id]);
    })->name('products.show');

    // Edit (Form Update) - Mengarah ke file blade edit, butuh parameter ID
    Route::get('/products/{id}/edit', function ($id) {
        return view('admin.products.edit', ['id' => $id]);
    })->name('products.edit');
});


Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Arahkan langsung ke view filenya
    Route::view('/pos', 'admin.transactions.pos')->name('transactions.pos');
    Route::view('/history', 'admin.transactions.history')->name('transactions.history');
    Route::view('/performance', 'admin.transactions.dashboard')->name('transactions.dashboard');
 // Route di web.php harus seperti ini:
Route::view('/financial-goal', 'admin.transactions.goal-setting')->name('financial.goal');

    // Khusus Receipt biasanya butuh ID di URL
    Route::get('/receipt/{id}', function($id) {
        return view('admin.transactions.receipt', ['id' => $id]);
    })->name('transactions.receipt');

    Route::get('/receipt/{id}', function($id) {
        return view('admin.transactions.receipt', ['id' => $id]);
    })->name('transactions.receipt');



    // PERBAIKAN: Masukkan route download ke SINI agar namanya menjadi admin.transactions.download
    Route::get('/transactions/{id}/download', function ($id) {
        $transaction = Transaction::findOrFail($id);

        $pdf = Pdf::loadView('admin.transactions.invoice-pdf', compact('transaction'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice-'.$transaction->reference_no.'.pdf');
    })->name('transactions.download');


Route::prefix('inventory')->name('inventory.')->group(function () {
        // Halaman Utama Monitoring & Restock (Volt Component)
        Route::get('/', function () {
            return view('admin.inventory.index');
        })->name('index');

        // Route untuk Detail Pergerakan Produk (Opsional jika ingin dipisah)
        Route::get('/tracking', function () {
            return view('admin.inventory.tracking');
        })->name('tracking');

        // Route untuk Laporan Margin per Periode
        Route::get('/analysis', function () {
            return view('admin.inventory.analysis');
        })->name('analysis');

        Route::get('/production', function () {
            return view('admin.production.index'); // Kita akan buat file view-nya sebentar lagi
        })->name('production');

    });




});









Route::prefix('admin/marketing')->name('admin.marketing.')->group(function () {

        // 1. Dashboard Marketing & Content Hub
        // Menampilkan semua konten yang sudah terposting (Database Konten)
        Route::get('/', function () {
            return view('admin.marketing.index');
        })->name('index');

        // 2. Content Studio (Livewire Volt yang kita buat tadi)
        // Tempat buat konten baru dengan template fleksibel
        Route::get('/studio', function () {
            return view('admin.marketing.studio');
        })->name('studio');

        // 3. Template Manager (PENTING!)
        // Tempat kamu nambahin field "Caption", "Link Canva", dll sesuka hati
        Route::get('/templates', function () {
            return view('admin.marketing.templates');
        })->name('templates');

    });










Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
