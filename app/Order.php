<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    const COURSE_TYPE = 1;
    const MANUSCRIPT_TYPE = 2;
    const WORKSHOP_TYPE = 3;

    protected $fillable = ['user_id', 'item_id', 'type', 'package_id', 'plan_id'];

}