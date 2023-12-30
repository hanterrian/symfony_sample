<?php

namespace App\Enum;

enum CommentStatus: string
{
    case SUBMITTED = 'submitted';
    case HAM = 'ham';
    case POTENTIAL_SPAM = 'potential_spam';
    case SPAM = 'spam';
    case REJECTED = 'rejected';
    case PUBLISHED = 'published';
}
