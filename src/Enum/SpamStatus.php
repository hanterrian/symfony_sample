<?php

namespace App\Enum;

enum SpamStatus: int
{
    case SPAM = 2;
    case POSSIBLE_SPAM = 1;
    case NOT_SPAM = 0;
}
