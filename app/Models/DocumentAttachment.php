<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentAttachment extends Model
{
    protected $table = 'document_attachments';
    protected $primaryKey = 'attachment_id';
    public $timestamps = false;

    protected $fillable = [
        'doc_id',
        'file_original_name',
        'file_stored_name',
        'file_path',
        'file_type',
        'file_size',
        'version_number',
        'is_active',
        'uploaded_by',
        'uploaded_at',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'doc_id');
    }
}

