<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'is_group',
        'created_by',
        'group_id'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class)->withPivot('last_read_at')->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
