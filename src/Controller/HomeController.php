<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/tricks", name="tricks")
     */
    public function tricks(): Response
    {
        return $this->render('home/tricks.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/signin", name="signin")
     */
    public function signin(): Response
    {
        return $this->render('home/signin.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/signup", name="signup")
     */
    public function signup(): Response
    {
        return $this->render('home/signup.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
