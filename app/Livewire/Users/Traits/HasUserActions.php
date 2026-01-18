<?php

namespace App\Livewire\Users\Traits;

use App\Models\User;
use App\Services\ReferenceDataService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * 🛡️ ZIRHLI BELGELEME KARTI (V12.2)
 * -------------------------------------------------------------------------
 * TRAIT      : HasUserActions
 * SORUMLULUK : Kullanıcı CRUD işlemleri, şifre yönetimi ve avatar operasyonları.
 *
 * BAĞIMLILIKLAR:
 * - Livewire\WithFileUploads (Bileşen seviyesinde)
 * - Mary\Traits\Toast (Bileşen seviyesinde)
 *
 * METODLAR:
 * - save(): Kullanıcı oluşturur veya günceller.
 * - delete(): Kullanıcıyı siler.
 * - resetTwoFactor(): 2FA ayarlarını sıfırlar.
 * - sendPasswordReset(): Şifre sıfırlama maili gönderir.
 * - deleteAvatar(): Profil fotoğrafını siler.
 * - toggleStatus(): Aktif/Pasif durumunu değiştirir.
 * -------------------------------------------------------------------------
 */
trait HasUserActions
{
    public function getTailwindColor(?string $schemeId): string
    {
        if (! $schemeId) {
            return 'bg-gray-100 text-gray-800 border-gray-200 border';
        }

        $colorClass = app(ReferenceDataService::class)->getColorClasses($schemeId);

        return $colorClass ?: 'bg-gray-100 text-gray-800 border-gray-200 border';
    }

    public function toggleEditMode(): void
    {
        $this->isViewMode = ! $this->isViewMode;
    }

    public function cancel(): void
    {
        if ($this->user->exists) {
            $this->isViewMode = true;
            // Reset form to original values
            $this->mount($this->user);
            $this->reset('avatarFile');
        } else {
            $this->redirect(route('users.index'), navigate: true);
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.($this->userId ?: 'NULL'),
            'phone' => 'nullable|string|max:20',
            'title' => 'nullable|string|max:255',
            'password' => $this->userId ? 'nullable|min:8' : ($this->sendPasswordEmail ? 'nullable' : 'required|min:8'),
            'departmentId' => 'nullable|uuid',
            'avatarFile' => 'nullable|image|max:1024', // 1MB max
        ]);

        // Handle Avatar Upload
        if ($this->avatarFile) {
            // Delete old avatar if exists
            if ($this->avatar) {
                Storage::disk('public')->delete($this->avatar);
            }
            $this->avatar = $this->avatarFile->store('avatars', 'public');
        }

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'title' => $this->title,
            'role_id' => $this->roleId ?: null,
            'department_id' => $this->departmentId ?: null,
            'avatar' => $this->avatar,
        ];

        if ($this->password) {
            $userData['password'] = Hash::make($this->password);
        }

        if ($this->userId) {
            // Update existing user
            $this->user->update($userData);
            $this->success('Başarılı', 'Kullanıcı güncellendi.');
            $this->isViewMode = true;
            $this->reset('avatarFile');
        } else {
            // Create new user
            if (! $this->password && $this->sendPasswordEmail) {
                // Generate temporary password for user creation
                $userData['password'] = Hash::make(Str::random(32));
            }

            $user = User::create($userData);

            if ($this->sendPasswordEmail && ! $this->password) {
                // Send welcome email with setup link
                try {
                    $controller = new \App\Http\Controllers\UserSetupController;
                    $response = $controller->sendWelcomeEmail($user);
                    $responseData = json_decode($response->getContent(), true);

                    if ($responseData['success']) {
                        $this->success('Başarılı', 'Kullanıcı oluşturuldu ve hoş geldin maili gönderildi.');
                    } else {
                        $this->warning('Uyarı', 'Kullanıcı oluşturuldu ancak mail gönderilemedi: '.$responseData['message']);
                    }
                } catch (\Exception $e) {
                    $this->warning('Uyarı', 'Kullanıcı oluşturuldu ancak mail gönderilemedi: '.$e->getMessage());
                }
            } else {
                $this->success('Başarılı', 'Kullanıcı oluşturuldu.');
            }

            $this->redirect(route('users.edit', $user), navigate: true);
        }
    }

    public function delete(): void
    {
        if (! $this->user->exists) {
            return;
        }

        $this->user->delete();
        $this->success('Başarılı', 'Kullanıcı silindi.');
        $this->redirect(route('users.index'), navigate: true);
    }

    public function resetTwoFactor(): void
    {
        if (! $this->user->exists) {
            return;
        }

        $this->user->resetTwoFactor();
        $this->success('Başarılı', $this->user->name.' kullanıcısının 2FA ayarları sıfırlandı.');
    }

    public function sendPasswordReset(): void
    {
        if (! $this->user->exists) {
            return;
        }

        try {
            $controller = new \App\Http\Controllers\UserSetupController;
            $response = $controller->sendPasswordResetEmail($this->user);
            $responseData = json_decode($response->getContent(), true);

            if ($responseData['success']) {
                $this->success('Başarılı', 'Şifre sıfırlama maili gönderildi.');
            } else {
                $this->error('Hata', 'Mail gönderilemedi: '.$responseData['message']);
            }
        } catch (\Exception $e) {
            $this->error('Hata', 'Mail gönderilemedi: '.$e->getMessage());
        }
    }

    public function deleteAvatar(): void
    {
        if ($this->avatar) {
            Storage::disk('public')->delete($this->avatar);
            $this->avatar = '';

            if ($this->user->exists) {
                $this->user->update(['avatar' => null]);
            }
        }
        $this->reset('avatarFile');
        $this->success('Başarılı', 'Profil fotoğrafı kaldırıldı.');
    }

    public function toggleStatus(): void
    {
        if (! $this->user->exists) {
            return;
        }

        if ($this->user->status === 'active') {
            $this->user->deactivate();
            $this->success('Başarılı', $this->user->name.' kullanıcısı pasife alındı.');
        } else {
            $this->user->activate();
            $this->success('Başarılı', $this->user->name.' kullanıcısı aktif edildi.');
        }

        $this->user->refresh();
    }
}
