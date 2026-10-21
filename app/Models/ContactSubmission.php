<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    /** @use HasFactory<\Database\Factories\ContactSubmissionFactory> */
    use HasFactory;

    public const NEW = 'new';
    public const READ = 'read';
    public const CLOSED = 'closed';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'topic',
        'message',
        'status',
        'email_status',
        'email_sent_at',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'email_sent_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }
}