<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UsersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Users::class;
    }

  
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnDetail(),
            EmailField::new('email','Adresse Email'),
            TextField::new('username','Pseudo'),
            TextField::new('full_name','Nom complet'),
            TextareaField::new('bio','Bref description de l\'utilisateur')->hideOnIndex(),
            ImageField::new('avatar_url','avatar')
                        ->setBasePath('uploads/user')
                        ->setUploadDir('public/uploads/user')
                        ->setUploadedFileNamePattern('[randomhash].[extension]')->hideOnIndex()
                      ,
            
            ChoiceField::new('roles')->setChoices([
                'Administrateur'=>'ROLE_ADMIN',
                'Auteur' => 'ROLE_AUTOR',
                'Lecteur' => 'ROLE_READER'
            ])->allowMultipleChoices()
            ->renderAsBadges([
               
                'ROLE_ADMIN' => 'success',
                'ROLE_AUTOR' => 'warning',
                'ROLE_READER' => 'info',
            ])
            ->renderExpanded(),
            TextField::new('password','Mot de passe')->onlyWhenCreating(),
            BooleanField::new('is_verified','Est verifié ?')->renderAsSwitch(false)->hideWhenUpdating(),
            DateTimeField::new('created_at', 'Crée le')->onlyOnDetail(),
            DateTimeField::new('last_login','Dernière connexion')->hideOnForm()
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_NEW, Action::INDEX);
    }
  
}
