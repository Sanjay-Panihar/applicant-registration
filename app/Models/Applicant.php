<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'address',
        'dob',
        'gender',
        'resume',
        'photo',
    ];

    public static $rules = [
        'first_name' => 'required',
        'last_name' => 'required',
        'phone' => 'required|numeric|digits:10',
        'email' => 'required|email|unique:applicants',
        'address' => 'required',
        'dob' => 'required|date|before:date("d-m-Y", strtotime("-18 years"))',
        'gender' => 'required',
        'resume' => 'nullable|mimes:pdf,docx|max:2048',
        'photo' => 'nullable|mimes:jpg,png|max:2048',
    ];
}
