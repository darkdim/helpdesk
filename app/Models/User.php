<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property UserRole $role
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection $ticketsAsCustomer
 * @property-read \Illuminate\Database\Eloquent\Collection $ticketsAsAssignee
 * @property-read \Illuminate\Database\Eloquent\Collection $messages
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
        /* @chisel-2fa */
        'two_factor_confirmed_at' => 'datetime',
        /* @end-chisel-2fa */
    ];

    public function isCustomer(): bool
    {
        return $this->role === UserRole::Customer;
    }

    public function isAgent(): bool
    {
        return $this->role === UserRole::Agent;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function ticketsAsCustomer(): HasMany
    {
        return $this->hasMany(Ticket::class, 'customer_id');
    }

    public function ticketsAsAssignee(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assignee_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }
}
