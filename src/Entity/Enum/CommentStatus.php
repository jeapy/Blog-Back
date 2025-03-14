<?php

namespace App\Entity\Enum;

enum CommentStatus : string{

    case PENDING = 'pending';
    case APPROVED = 'approved';
    case SPAM = 'spam';
}