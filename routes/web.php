 <?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Filament\Notifications\Notification;

Route::get('/test-notif', function () {
    $user = User::find(4);

    if (!$user) return 'User tidak ditemukan';

    Notification::make()
        ->title('Halo bro!')
        ->body('Ini notifikasi khusus buat kamu 😎')
        ->success()
        ->sendToDatabase($user);

    return 'Notif sukses!';
});
