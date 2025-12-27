<?php

namespace App\Enums;

enum UserTypeEnum: string
{
    case User = 'user';
    case Admin = 'admin';
    case Author = 'author';
    case Editor = "editor";
}
