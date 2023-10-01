<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public static function rules($id = null)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:brands,name',
        ];

        if ($id) {
            $rules['name'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('brands', 'name')->ignore($id),
            ];
        }
        return $rules;
    }
}
