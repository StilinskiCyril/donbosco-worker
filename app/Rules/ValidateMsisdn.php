<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateMsisdn implements ValidationRule
{
    public $unique_check;
    public $model;
    public $column;
    public $source_param;


    public function __construct($unique_check = true, $model = 'User', $column = 'msisdn', $source_param = 'msisdn')
    {
        $this->model = 'App\\Models\\'.$model;
        $this->column = $column;
        $this->unique_check = $unique_check;
        $this->source_param = $source_param;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $validation = validatePhone($value);

        if (!$validation['isValid']) {
            $fail($validation['msisdn']. ' is invalid');
        }

        if ($this->unique_check){
            $count = $this->model::where($this->column, $validation['msisdn'])->count();

            if ($count > 0) {
                $fail('Msisdn '.$validation['msisdn']. ' already taken');
            }
        }
        request()->merge([$this->source_param => $validation['msisdn']]);
    }
}
