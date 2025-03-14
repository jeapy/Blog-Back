<?php

namespace App\Entity\Enum;

enum ArticleStatus : string{

    case BROUILLON = 'draft';
    case PUBLIER = 'published';
    case ARCHIVER = 'archived';
}