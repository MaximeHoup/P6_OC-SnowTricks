<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TricksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $getDoctrine,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @Route("/", name="home")
     */
    public function index(TricksRepository $trickRepository)
    {
        $tricks = $trickRepository->findBy([], ['Created_at' => 'DESC'], limit: 15);

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks
        ]);
    }



    /**
     * @Route("/tricks/{page?1}", name="tricks")
     */
    public function alltricks(TricksRepository $trickRepository, $page)
    {
        $trickperpage = 10;
        $nbtricks = $trickRepository->count([]);
        $nbpages = ceil(num: $nbtricks / $trickperpage);

        $tricks = $trickRepository->findBy([], ['id' => 'ASC'], limit: $trickperpage, offset: ($page - 1) * $trickperpage);

        return $this->render('home/tricks.html.twig', [
            'tricks' => $tricks,
            'nbpages' => $nbpages,
            'page' => $page
        ]);
    }
}
