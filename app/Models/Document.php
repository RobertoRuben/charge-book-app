<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'subject',
        'page_count',
        'registry_number',
        'file_path',
        'employee_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'page_count' => 'integer',
        'registry_number' => 'integer',
    ];

    /**
     * The attributes that should be hidden.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'file_path',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($document) {
            if (empty($document->registry_number)) {
                // Get the last registry number
                $lastDocument = self::orderBy('registry_number', 'desc')->first();
                $nextNumber = $lastDocument ? $lastDocument->registry_number + 1 : 1;

                // Set the next number
                $document->registry_number = $nextNumber;
            }
        });

        static::deleting(function ($document) {
            if ($document->file_path) {
                Storage::delete($document->file_path);
            }
        });
    }

    /**
     * Get the formatted registry number with leading zeros.
     */
    public function getFormattedRegistryNumberAttribute(): string
    {
        return str_pad($this->registry_number, 10, '0', STR_PAD_LEFT);
    }

    /**
     * Get the document file URL.
     */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    /**
     * Get the employee that owns the document.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
