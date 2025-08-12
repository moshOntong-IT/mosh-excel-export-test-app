<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Mosh\ExcelExportStreamer\Contracts\ExportableInterface;

class User extends Authenticatable implements ExportableInterface
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getExportColumns(): array
    {
        return ['id', 'name', 'email', 'email_verified_at', 'created_at'];
    }

    public function getExportHeaders(): array
    {
        return [
            'User ID',
            'Full Name',
            'Email Address',
            'Email Verified',
            'Registration Date'
        ];
    }

    public function transformForExport(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at 
                ? $this->email_verified_at->format('Y-m-d H:i:s') 
                : 'Not Verified',
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
