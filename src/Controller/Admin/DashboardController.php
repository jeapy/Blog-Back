<?php

namespace App\Controller\Admin;

use App\Entity\Tags;
use App\Entity\Users;
use App\Entity\Articles;
use App\Entity\Categories;
use Symfony\UX\Chartjs\Model\Chart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private readonly EntityManagerInterface $em,private readonly ChartBuilderInterface $chartBuilder)
        {
            
        }

        
   
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $users_count = $this->em->getRepository(Users::class)->countUsers() ;
        $art_published = $this->em->getRepository(Articles::class)->countPublishedArticle() ;
        $art_archived = $this->em->getRepository(Articles::class)->countArchivedArticle() ;

        $chart_bar = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart_bar->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    
                    'backgroundColor'=> [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(201, 203, 207, 0.2)'
                      ],
                      'borderColor'=> [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                      ],
                      'borderWidth'=> 1,
                    'data' => [65, 10, 5, 2, 20, 30, 45],
                ],
            ],
        ]);

        $chart_bar->setOptions([
            'scales' => [
                'y' => [
                    'beginAtZero'=> true
                ],
            ],
        ]);

        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        // Set data
        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'My First Dataset',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [0, 10, 5, 2, 20, 30, 45],
                ],
                [
                    'label' => 'My Second Dataset',
                    'backgroundColor' => 'rgb(54, 162, 235)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'data' => [15, 20, 12, 25, 30, 40, 35],
                ],
            ],
        ]);

        // Set options
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);
        

        return $this->render('admin/dashboard.html.twig',
        [
        'chart' => $chart,
        'chart_bar' => $chart_bar,
        'user'=> $users_count,
        'published_article' => $art_published,
        'archived_article' => $art_archived
    ]);
        
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('admin/dashboard.html.twig');
    }

    public function configureAssets(): Assets
    {
        return Assets::new(); 
      //  ->addAssetMapperEntry('app');
    }


    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
        
            ->setTitle('Blog')
            ;
    }

   

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

   
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', Users::class)
        ->setPermission('ROLE_ADMIN');

        yield MenuItem::linkToCrud('Articles', 'fas fa-newspaper', Articles::class);

        yield MenuItem::linkToCrud('Catégories', 'fas fa-layer-group', Categories::class)
        ->setPermission('ROLE_ADMIN');

        yield MenuItem::linkToCrud('Tags', 'fas fa-tag', Tags::class)
        ->setPermission('ROLE_ADMIN');
                    
       
        yield MenuItem::section();
        yield MenuItem::linkToRoute('Retour au site', 'fas fa-undo', 'app_home', ['controller_name' => 'HomeController']);
    }

    
}

