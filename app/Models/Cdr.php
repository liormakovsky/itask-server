<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cdr extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cdr';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "customer_id",
        "date_time",
        "duration",
        "did",
        "ip_address",
        "cont_source",
        "cont_destination",
    ];

        /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

}
