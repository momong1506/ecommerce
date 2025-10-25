<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * EmailLog Model
 *
 * Tracks email delivery status with retry logic
 */
class EmailLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'recipient_email',
        'status',
        'retry_count',
        'error_message',
        'sent_at',
        'failed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'retry_count' => 'integer',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    /**
     * Get the order that owns the email log.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope to filter pending emails.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter sent emails.
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope to filter failed emails.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
