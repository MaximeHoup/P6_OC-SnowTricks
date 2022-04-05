<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Entity\Media;
use App\Entity\Comments;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Form\CommentType;
use App\Repository\TricksRepository;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(TricksRepository $trickRepository)
    {
        $tricks = $trickRepository->findAll();

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/trick/{id}", name="show")
     */
    public function show($id, Request $request)
    {
        $trick = $this->getDoctrine()->getRepository(Tricks::class)->find($id);
        $comments = $this->getDoctrine()->getRepository(Comments::class)->findAll();
        $images = $this->getDoctrine()->getRepository(Media::class)->findby(['tricks' => $trick->getId()]);


        $comment = new Comments();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setCreatedAt(new \DateTimeImmutable())
                ->setUsers($this->getUser())
                ->setTricks($trick);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('show', [
                'trick' => $trick,
                'id' => $id,
                'images' => $images,
                'comments' => $comments,
                'formComment' => $commentForm->createView()
            ]);
        }

        return $this->render('home/show.html.twig', [
            'controller_name' => 'HomeController',
            'trick' => $trick,
            'images' => $images,
            'comments' => $comments,
            'formComment' => $commentForm->createView()
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

    /**
     * @Route("/new", name="new")
     * @Route("/trick/{id}/edit", name="edit")
     */
    public function tricks(Tricks $trick = null, Request $request)
    {
        if (!$trick) {
            $trick = new Tricks();
        }

        $trickForm = $this->createFormBuilder($trick)
            ->add('name')
            ->add('figure_group')
            ->add('Description')
            ->add('mainMedia', FileType::class, [
                'data_class' => null
            ])
            ->add('media', FileType::class, [
                'data_class' => null,
                'mapped' => false,
                'label' => false,
                'multiple' => true
            ])
            ->getForm();

        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {

            if (!$trick->getId()) {
                $trick->setCreatedAt(new \DateTimeImmutable());
            }
            $trick->setModifiedAt(new \DateTimeImmutable());
            $trick->setUsers($this->getUser());

            $mainMediaFile = $trickForm->get('mainMedia')->getData();
            $mainMedia_uploads_directory = $this->getParameter('main_media_directory');
            $mainMediaFilename = md5(uniqid()) . '.' . $mainMediaFile->guessExtension();
            $mainMediaFile->move(
                $mainMedia_uploads_directory,
                $mainMediaFilename
            );
            $trick->setMainMedia($mainMediaFilename);


            // On récupère les images transmises
            $images = $trickForm->get('media')->getData();

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

            $this->addFlash('success', 'Nouvelle figure ajoutée');
            return $this->redirectToRoute('show', ['id' => $trick->getId()]);
        }
        return $this->render('home/new.html.twig', [
            'controller_name' => 'HomeController',
            'formTrick' => $trickForm->createView(),
            'editMode' => $trick->getId() !== null
        ]);
    }

    /**
     * @Route("/trick/{id}/delete", name="delete")
     */
    public function deleteTrick(Tricks $trick = null,  $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $trick = $entityManager->getRepository(Tricks::class)->find($id);

        $comments = $entityManager->getRepository(Comments::class)->findBy(['Tricks' => $trick->getId()]);
        foreach ($comments as $comment) {
            $entityManager->remove($comment);
        }
        $entityManager->remove($trick);
        $entityManager->flush();
        return $this->redirectToRoute('home');
    }
}
