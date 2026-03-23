<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentAction extends Model
{
    protected $table = 'document_actions'; // Make sure table name is correct
    protected $primaryKey = 'action_id';
    public $timestamps = false;

    protected $fillable = [
        'doc_id',
        'section_id',
        'position_id',
        'user_id',
        'action_type',
        'remarks',
        'action_datetime',
    ];


    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relationship back to Document
    public function document()
    {
        return $this->belongsTo(Document::class, 'doc_id', 'doc_id');
    }
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'section_id');
    }
}
