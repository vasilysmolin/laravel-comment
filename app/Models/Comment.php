<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'comment_id',
      'article_id',
      'text',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function childrenComments()
    {
        return $this->hasMany(Comment::class, 'comment_id', 'id')
            ->with('user');
    }
}
