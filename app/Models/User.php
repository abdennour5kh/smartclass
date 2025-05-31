<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
    ];

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    // overided func for laravel to find the user email
    public function routeNotificationForMail()
    {
        switch ($this->role) {
            case 'student':
                return $this->student?->email;
            case 'teacher':
                return $this->teacher?->email;
            case 'admin':
                return $this->admin?->email;
            default:
                return null;
        }
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class)->withPivot('last_read_at')->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // helpful in an unified approch where we only work with User
    public function getFullNameAttribute()
    {
        if ($this->role === 'teacher' && $this->teacher) {
            return $this->teacher->first_name . ' ' . $this->teacher->last_name;
        }

        if ($this->role === 'student' && $this->student) {
            return $this->student->first_name . ' ' . $this->student->last_name;
        }

        if ($this->role === 'admin' && $this->admin) {
            return $this->admin->first_name . ' ' . $this->admin->last_name;
        }

        return 'Unknown User';
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->role === 'teacher' && $this->teacher?->img_url) {
            return asset('storage/' . $this->teacher->img_url);
        }

        if ($this->role === 'student' && $this->student?->img_url) {
            return asset('storage/' . $this->student->img_url);
        }

        if ($this->role === 'admin' && $this->admin?->img_url) {
            return asset('storage/' . $this->admin->img_url);
        }

        return asset('images/default-avatar.png');
    }

    public function roleModel()
    {
        return match ($this->role) {
            'student' => $this->student,
            'teacher' => $this->teacher,
            'admin' => $this->admin,
            default => null,
        };
    }




    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
}
