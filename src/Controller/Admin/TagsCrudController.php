<?php

namespace App\Controller\Admin;

use App\Entity\Tags;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TagsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tags::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
           
            TextField::new('name'),
            SlugField::new('slug')->setTargetFieldName('name'),
        ];
    }

}
