<?php

namespace App\Models;

use App\Validation\Email;

class User
{
    private string $id;
    private string $name;
    private Email $email;
    private string $password;
}
