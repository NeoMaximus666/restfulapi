<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(
 *  definition="Meeting",
 *  @SWG\Property(
 *      property="id",
 *      type="integer",
 *      format="int64"
 *  ),
 *  @SWG\Property(
 *      property="title",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="description",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="time",
 *      type="string",
 *      format="date-time"
 *  )
 * )
 */
class Meeting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'time',
    ];

    public function users(){
        return $this->belongsToMany(User::class);
    }
}
