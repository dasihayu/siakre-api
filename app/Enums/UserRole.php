<?php

namespace App\Enums;

enum UserRole: string
{
    case KAPRODI = 'KAPRODI';
    case DOSEN = 'DOSEN';
    case ADMINISTRATOR = 'ADMINISTRATOR';
    case OPERATOR = 'OPERATOR';
    case REVIEWER = 'REVIEWER';
    case PIMPINAN = 'PIMPINAN';
}
