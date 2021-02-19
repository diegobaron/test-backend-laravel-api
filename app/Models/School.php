<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class School extends Model
{
    use HasFactory;

    protected $table = "schools";

    protected $fillable = [
        'name',
        'city'
    ];

    public function courses()
    {
        return $this->hasMany(Course::class, 'school_id', 'id');
    }

    public static function validator(array $data, string $action)
    {
        return Validator::make(
            $data,
            self::rules($data, $action),
            self::validatorMessages()
        );
    } 

    protected static function rules(array $data, string $action) :array
    {
        return [
            'name' => [
                'required'
            ],
            'city' => [
                'required'
            ]
        ];
    }

    protected static function validatorMessages() :array
    {
        return [
            'name.required' => 'Informe o nome da escola',
            'city.required' => 'Informe a cidade da escola'
        ];
    }
}
