<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    const CREATED_AT = 'date_added';
    const UPDATED_AT = 'last_updated';

    protected $fillable = ['user_id','name','email','phone','status','date_added','last_updated'];

    const statuses = ['New', 'In Progress', 'Closed'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
