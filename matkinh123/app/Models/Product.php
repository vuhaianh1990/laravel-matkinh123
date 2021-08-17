<?php

namespace Matkinh123\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    public function categories()
    {
        return $this->belongsTo('App\Category');
    }

    public function attributes()
    {
        return $this->belongsTo('App\Attribute');
    }

    public function manufacturers()
    {
        return $this->belongsTo('App\Manufacturer');
    }
}
