<?php

namespace App\Models;

use App\Enums\FavoriteType;
use App\Enums\Theme;
use App\Enums\UserStatus;
use App\Models\Concerns\HasAvatar;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasAvatar, HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'theme',
        'notification_preferences',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_preferences' => 'array',
            'status' => UserStatus::class,
            'theme' => Theme::class,
        ];
    }

    // ========================================
    // Domain Methods - Email Verification
    // ========================================

    public function verifyEmail(): void
    {
        if ($this->email_verified_at === null) {
            $this->email_verified_at = now();
            $this->status = UserStatus::ACTIVE;
            $this->save();
        }
    }

    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    // ========================================
    // Domain Methods - Status Management
    // ========================================

    public function activate(): void
    {
        if ($this->status !== UserStatus::ACTIVE) {
            $this->status = UserStatus::ACTIVE;
            $this->save();
        }
    }

    public function suspend(): void
    {
        if ($this->status !== UserStatus::SUSPENDED) {
            $this->status = UserStatus::SUSPENDED;
            $this->save();
        }
    }

    public function deactivate(): void
    {
        if ($this->status !== UserStatus::INACTIVE) {
            $this->status = UserStatus::INACTIVE;
            $this->save();
        }
    }

    public function isActive(): bool
    {
        return $this->status->isActive() && ! $this->trashed();
    }

    public function canLogin(): bool
    {
        return $this->status->canLogin() && ! $this->trashed();
    }

    // ========================================
    // Domain Methods - Profile Updates
    // ========================================

    public function updateProfile(string $name, ?string $avatarUrl = null): void
    {
        $this->name = $name;

        if ($avatarUrl !== null) {
            $this->avatar_url = $avatarUrl;
        }

        $this->save();
    }

    public function changeTheme(Theme $theme): void
    {
        if ($this->theme !== $theme) {
            $this->theme = $theme;
            $this->save();
        }
    }

    public function changePassword(string $newPassword): void
    {
        $this->password = Hash::make($newPassword);
        $this->save();
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return Hash::check($plainPassword, $this->password);
    }

    // ========================================
    // Domain Methods - Notification Preferences
    // ========================================

    public function updateNotificationPreferences(array $preferences): void
    {
        $this->notification_preferences = [
            ...$this->getDefaultNotificationPreferences(),
            ...$preferences,
        ];
        $this->save();
    }

    public function shouldReceiveNotification(string $event): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        $preferences = $this->notification_preferences ?? $this->getDefaultNotificationPreferences();

        if (! ($preferences['email_notifications'] ?? true)) {
            return false;
        }

        return match ($event) {
            'task_assignment' => $preferences['task_assignments'] ?? true,
            'task_comment' => $preferences['task_comments'] ?? true,
            'task_status_change' => $preferences['task_status_changes'] ?? true,
            'mention' => $preferences['mentions'] ?? true,
            'team_invitation' => $preferences['team_invitations'] ?? true,
            default => false,
        };
    }

    protected function getDefaultNotificationPreferences(): array
    {
        return [
            'email_notifications' => true,
            'task_assignments' => true,
            'task_comments' => true,
            'task_status_changes' => true,
            'mentions' => true,
            'team_invitations' => true,
            'daily_digest' => false,
            'weekly_report' => false,
        ];
    }

    // ========================================
    // Domain Methods - Favorites
    // ========================================

    public function addFavorite(FavoriteType $type, string $favoritableId): Favorite
    {
        return $this->favorites()->firstOrCreate([
            'favoritable_type' => $type,
            'favoritable_id' => $favoritableId,
        ]);
    }

    public function removeFavorite(FavoriteType $type, string $favoritableId): void
    {
        $this->favorites()
            ->where('favoritable_type', $type)
            ->where('favoritable_id', $favoritableId)
            ->delete();
    }

    public function hasFavorite(FavoriteType $type, string $favoritableId): bool
    {
        return $this->favorites()
            ->where('favoritable_type', $type)
            ->where('favoritable_id', $favoritableId)
            ->exists();
    }

    public function getFavoritesByType(FavoriteType $type)
    {
        return $this->favorites()
            ->where('favoritable_type', $type)
            ->with('favoritable')
            ->get();
    }

    // ========================================
    // Relationships
    // ========================================

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'user_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }
}
