<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('name', 'asc');
    }

    public function scopeWithProducts(Builder $query): Builder
    {
        return $query->whereHas('products');
    }

    public static function rules($id = null)
    {
        if ($id) {
            return [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('categories')->ignore($id)
                ]
            ];
        }

        return [
            'name' => 'required|string|max:255|unique:categories'
        ];
    }
}
