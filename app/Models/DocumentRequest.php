<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'status',
        'admin_response',
        'document_type',
        'document_path',
    ];

    // hard coded document types for simplicity, types dont get changed frequantly
    public const DOCUMENT_TYPES = [
        1 => 'Attestation de scolarité',
        2 => 'Relevé de notes',
        3 => 'Attestation de réinscription',
    ];

    public function student()
    {
        $this->belongsTo(Student::class);
    }
}
