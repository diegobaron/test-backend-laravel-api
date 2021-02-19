<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Course extends Model
{
    use HasFactory;

    protected $table = "courses";

    protected $fillable = [
        'name',
        'school_id',
        'description',
        'start_date'
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
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
            'school_id' => [
                'required'
            ],
            'description' => [
                'required'
            ],
            'start_date' => [
                'required'
            ]
        ];
    }

    protected static function validatorMessages() :array
    {
        return [
            'name.required' => 'Informe o nome do curso',
            'school_id.required' => 'Informe o ID da escola',
            'description.required' => 'Informe a descrição do curso',
            'start_date' => 'Informe a data inicial do curso'
        ];
    }

}
