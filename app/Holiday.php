<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'uid', 'summary', 'date_start', 'date_end', 'description'
    ];

    public function findByUid($uid)
    {
        return $self::where('uid', $uid)->findOrFail(1);
    }
}
