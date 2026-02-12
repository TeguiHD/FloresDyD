<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedAttributes;

class PaymentProof extends Model
{
    use HasFactory, HasEncryptedAttributes;

    protected $fillable = [
        'order_id',
        'uploaded_by',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'file_hash',
        'transaction_code_encrypted',
        'declared_amount',
        'transaction_date',
        'bank_origin',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'file_metadata',
    ];

    protected array $encryptedAttributes = [
        'transaction_code_encrypted',
    ];

    protected function casts(): array
    {
        return [
            'declared_amount' => 'integer',
            'transaction_date' => 'date',
            'verified_at' => 'datetime',
            'file_metadata' => 'array',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}

