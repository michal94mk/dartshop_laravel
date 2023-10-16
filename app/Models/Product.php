<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'category_id', 'brand_id', 'image'];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public static $rules = [
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'required|numeric|min:0.01',
        'category_id' => 'integer|exists:categories,id',
        'brand_id' => 'integer|exists:brands,id',
        'image' => 'string|nullable',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function validate(array $data)
    {
        $validator = Validator::make($data, static::$rules);

        return !$validator->fails();
    }

    // public function getImageUrlAttribute()
    // {
    //     return asset('storage/app/public/images/' . $this->image);
    // }

    public function getErrors()
    {
        return $this->errors ?? new MessageBag();
    }
}
