<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMINISTRATOR = 'ADMINISTRATOR';
    case DOSEN = 'DOSEN';
    case KAPRODI = 'KAPRODI';
    case REVIEWER = 'REVIEWER';
}
