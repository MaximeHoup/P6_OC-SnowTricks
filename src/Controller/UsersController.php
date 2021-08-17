<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;

class UsersController extends AbstractController
{
    /**
     * @Route("/allusers", name="users")
     */
    public function index(): Response
    {

        $users = $this->getDoctrine()->getRepository(Users::class)->findAll();

        return $this->render('users/index.html.twig', compact('users'));
    }
}
