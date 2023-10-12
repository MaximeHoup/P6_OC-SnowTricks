<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use Doctrine\Persistence\ManagerRegistry;

class UsersController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $getDoctrine
    ) {
    }

    /**
     * @Route("/allusers", name="users")
     */
    public function index(): Response
    {

        $users = $this->getDoctrine->getRepository(Users::class)->findAll();

        return $this->render('users/index.html.twig', compact('users'));
    }
}
