<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;

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
     * @Route("/new", name="new")
     */
    public function newtrick(Request $request)
    {
        $trick = new Tricks();

        $form = $this->createFormBuilder($trick)
            ->add('name')
            ->add('figure_group')
            ->add('Description')
            ->add('media', FileType::class, [
                'mapped' => false,
                'label' => false,
                'multiple' => true
            ])
            ->getForm();

        $form->handleRequest($request);
        dump($trick);


        if ($form->isSubmitted() && $form->isValid()) {

            $trick->setCreatedAt(new \DateTimeImmutable());
            $trick->setModifiedAt(new \DateTimeImmutable());
            $trick->setUsers($this->getUser());

            // On récupère les images transmises
            $images = $form->get('media')->getData();

            // On boucle sur les images
            foreach ($images as $image) {
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('trick_directory'),
                    $fichier
                );

                // On crée l'image dans la base de données
                $img = new Media();
                $img->setsource($fichier);
                $trick->addMedium($img);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();
        }
        return $this->render('home/new.html.twig', [
            'controller_name' => 'HomeController',
            'formTrick' => $form->createView()
        ]);
    }
}
