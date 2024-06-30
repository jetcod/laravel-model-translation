<?php

namespace Jetcod\Laravel\Translation\Test\Stubs;

use Illuminate\Database\Eloquent\Model;
use Jetcod\Laravel\Translation\Traits\TranslatableTrait;

class Post extends Model
{
    use TranslatableTrait;

    protected $fillable = ['name'];
}
