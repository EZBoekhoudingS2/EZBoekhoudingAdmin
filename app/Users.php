<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'users';

    public $timestamps = false;

    protected $fillable = [
        'email',
        'vnaam',
        'anaam',
        'straat',
        'postcode',
        'plaats',
        'telefoon',
        'bedrijfsnaam',
        'iban',
        'iban_naam',
        'btw',
        'kvk'
    ];

    protected $guarded = [];

}
