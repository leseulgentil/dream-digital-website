<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaAsset extends Model
{
    protected $fillable = [
        'path',
        'filename',
        'mime_type',
        'size_bytes',
        'width',
        'height',
        'alt_text',
        'credit',
        'source_url',
        'uploaded_by_id',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }
}
