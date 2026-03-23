<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Document extends Model
{
    protected $table = 'documents';
    protected $primaryKey = 'doc_id';
    public $timestamps = true;

    protected $fillable = [
        'document_number',
        'type_id',
        'document_name',
        'originating_section_id',
        'created_by',
        'current_section_id',
        'current_holder_id',
        'status',
        'is_active',
        'created_at',
        'updated_at',
    ];

    public function type()
    {
        return $this->belongsTo(DocumentType::class, 'type_id');
    }

    public function attachments()
    {
        return $this->hasMany(DocumentAttachment::class, 'doc_id');
    }

    public function actions()
    {
        return $this->hasMany(DocumentAction::class, 'doc_id');
    }

    public function currentSection()
    {
        return $this->belongsTo(Section::class, 'current_section_id', 'section_id');
    }

    public function currentHolder()
    {
        return $this->belongsTo(User::class, 'current_holder_id', 'user_id');
    }


    public function getRouteKeyName()
    {
        return 'doc_id';
    }

    public function originatingSection()
    {
        return $this->belongsTo(Section::class, 'originating_section_id', 'section_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function trackingHistory(): Collection
    {
        // Load actions with user info
        $actions = $this->hasMany(DocumentAction::class, 'doc_id', 'doc_id')
                        ->with('user') // load the user who performed the action
                        ->orderBy('action_datetime')
                        ->get();

        // Set section dynamically
        $previousSectionId = $this->originating_section_id;
        foreach ($actions as $action) {
            $action->section = Section::find($previousSectionId);
            $previousSectionId = $action->section_id;
        }

        return $actions;
    }
}

