<?php
namespace App\Models;

class SuperSubmission extends Submission
{
    protected $table = 'submissions';
    // Di sini Super Admin bisa memiliki akses ke scope yang berbeda
}