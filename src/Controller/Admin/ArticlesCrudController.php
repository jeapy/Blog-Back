<?php

namespace App\Controller\Admin;

use App\Entity\Users;

use App\Entity\Articles;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Enum\ArticleStatus;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ArticlesCrudController extends AbstractCrudController
{

    public function __construct(private readonly EntityManagerInterface $entityManager,private readonly AdminUrlGenerator $crudUrlGenerator)
    {
    
    }

    public static function getEntityFqcn(): string
    {
        return Articles::class;
    }

    
    public function configureFields(string $pageName): iterable
    {

       if ($this->isGranted('ROLE_ADMIN') ){
        $article_status = ArticleStatus::cases() ;
       }else{
        $article_status = [ArticleStatus::BROUILLON] ;
       }

        return [
            FormField::addColumn(8),
            TextField::new('title','Titre'),
            SlugField::new('slug')->setTargetFieldName('title')->hideOnIndex(),
            ImageField::new('featured_image_url','image d\'illustration')
                        ->setBasePath('uploads/article')
                        ->setUploadDir('public/uploads/article')
                        ->setUploadedFileNamePattern('[randomhash].[extension]')->hideOnIndex(),
            TextEditorField::new('content','Article')->hideOnIndex(),
            TextareaField::new('excerpt','Résumé'),
          

            FormField::addColumn(4),  
            AssociationField::new('categories')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->autocomplete()
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getcategories()->map(function($category) {
                        return $category->getName();
                    })->toArray());
                })
                ,    
                AssociationField::new('tags')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->autocomplete()
                ->hideOnIndex(),        
            AssociationField::new('author','Auteur')->setQueryBuilder(
                fn (QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Users::class)->findCurrentUser()
                )
                ->renderAsNativeWidget()
                ->hideWhenUpdating(),
       
            ChoiceField::new('status')
            ->setChoices($article_status)
            ->renderExpanded()
            ->renderAsBadges([
                'draft' => 'danger',
                'published' => 'success',
                'archived' => 'info',
            ])->hideWhenUpdating(),
            BooleanField::new('is_featured','A la une ?')->renderAsSwitch(false),
            UrlField::new('shareUrl', 'Partagez')
                ->setTemplatePath('admin/field/facebook_share.html.twig')
                ->onlyOnDetail(),
            

        ];
    }
    public function configureActions(Actions $actions): Actions
    {

        $previewShare = Action::new('previewShare', 'Preview Share', 'fas fa-eye')
            ->linkToCrudAction('previewShare')
            ->addCssClass('btn btn-info')
           ;
            


        $shareOnFacebook = Action::new('shareOnFacebook', 'Partagez', 'fas fa-facebook')
            ->linkToRoute('admin_news_share', function (Articles $news) {
                return [
                    'id' => $news->getId(),
                ];
            })
            ->addCssClass('btn btn-primary')
            ->displayAsButton();

     $archiver = Action::new('archiver', 'Archiver', 'fas fa-archive')
                    ->linkToCrudAction('archiver')
                    ->displayIf(static function ($entity) {
                            return $entity->getStatus()===ArticleStatus::PUBLIER || $entity->getStatus()===ArticleStatus::BROUILLON;
                        })
                    ->setCssClass('btn btn-danger');
     $publier = Action::new('publier', 'Publier', 'fas fa-paper-plane')
                    ->linkToCrudAction('publier')
                    ->displayIf(static function ($entity) {
                                            return $entity->getStatus()===ArticleStatus::BROUILLON;
                                        })
                    ->setCssClass('btn btn-success');
       
        return $actions

            ->add(Crud::PAGE_DETAIL, $previewShare)
          //  ->add(Crud::PAGE_INDEX, $shareOnFacebook)

            ->add(Crud::PAGE_EDIT, $archiver)
             ->add(Crud::PAGE_EDIT, $publier)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            
            ->setPermission($publier, 'ROLE_ADMIN')
            ->setPermission($archiver, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_ADMIN');
    }

    public function previewShare(AdminContext $context)
    {
        $news = $context->getEntity()->getInstance();
        
        return $this->render('admin/article/preview_share.html.twig', [
            'news' => $news,
            'shareUrl' => $this->generateUrl('app_articles_show', 
                ['id' => $news->getId()], 
                \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL
            )
        ]);
    }

    public function archiver(AdminContext $context)
    {
        $article = $context->getEntity()->getInstance();
        $article->setStatus(ArticleStatus::ARCHIVER);
        $this->entityManager->flush();

        $this->addFlash('success', "<span style='color:green;'><strong>L\'article ".$article->getTitle()." est bien <u>archivé</u>.</strong></span>");

        $url = $this->crudUrlGenerator
            ->setController(ArticlesCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

     public function publier(AdminContext $context)
    {
        $article = $context->getEntity()->getInstance();
        $article->setStatus(ArticleStatus::PUBLIER);
        $this->entityManager->flush();

        $this->addFlash('success', "<span style='color:green;'><strong>L'article ".$article->getTitle()." est bien <u>publié</u>.</strong></span>");

        $url = $this->crudUrlGenerator
            ->setController(ArticlesCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }
}
