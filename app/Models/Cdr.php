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
        "num_of_calls",
        "did",
        "ip_address",
        "cont_source",
        "cont_destination",
    ];

}
