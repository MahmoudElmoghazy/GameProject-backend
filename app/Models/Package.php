<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use  HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'image',
        'price'
    ];

    public function userPackage(): HasMany
    {
        return $this->hasMany(UserPackage::class);
    }

    public function packagePermission(): HasMany
    {
        return $this->hasMany(PackagePermission::class);
    }

}
