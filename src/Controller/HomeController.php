<?php

namespace App\Controller;

use Twig\Environment;
use App\Entity\Articles;
use App\Repository\UsersRepository;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    public function __construct(private readonly UsersRepository $usersRepository,
    private readonly Environment $twig,
    private readonly ChartBuilderInterface $chartBuilder)
    {
        
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
     // dd( $this->usersRepository->findCurrentUser()) ;

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

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'chart' => $chart,
        ]);
    }

    #[Route('/admin/news/{id}/share', name: 'admin_news_share')]
    public function share(Articles $news): Response
    {
        $this->addOpenGraphMetadata($news);

        $shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . 
            urlencode($this->generateUrl('app_articles_show', 
                ['id' => $news->getId()], 
                \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL
            ));

        return $this->redirect($shareUrl);
    }

    private function addOpenGraphMetadata(Articles $news): void
    {
        // Add OpenGraph meta tags for better sharing experience
        $this->twig->addGlobal('og_title', $news->getTitle());
        $this->twig->addGlobal('og_description', $news->getExcerpt());
        $this->twig->addGlobal('og_image', $news->getFeaturedImageUrl());
        $this->twig->addGlobal('og_url', $this->generateUrl('app_articles_show', 
            ['id' => $news->getId()], 
            \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL
        ));
    }
}
