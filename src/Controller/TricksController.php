<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Entity\Media;
use App\Entity\Comments;
use App\Entity\Videos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CommentFormType;
use App\Form\TrickFormType;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TricksController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $getDoctrine,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @Route("/trick/{Slug}/{page?1}", name="show")
     */
    public function show(Tricks $trick, $page, Request $request, CommentsRepository $commentsRepository)
    {
        $comment = new Comments();
        $commentForm = $this->createForm(CommentFormType::class, $comment);
        $commentForm->handleRequest($request);

        $commentsperpage = 10;
        $nbcomments = $trick->getComments()->count();
        (int)$nbpages = ceil(num: $nbcomments / $commentsperpage);

        $comments = $commentsRepository->findBy(['Tricks' => $trick], ['id' => 'DESC'], limit: $commentsperpage, offset: ($page - 1) * $commentsperpage);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setCreatedAt(new \DateTimeImmutable())
                ->setUsers($this->getUser())
                ->setTricks($trick);

            $entityManager = $this->getDoctrine->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('show', [
                'Slug' => $trick->getSlug()
            ]);
        }

        return $this->render('home/show.html.twig', [
            'trick' => $trick,
            'images' => $trick->getMedia(),
            'figureGroup' => $trick->getFigureGroup(),
            'videos' => $trick->getVideos(),
            'nbpages' => $nbpages,
            'page' => $page,
            'comments' => $comments,
            'Slug' => $trick->getSlug(),
            'formComment' => $commentForm->createView()
        ]);
    }

    /**
     * @Route("/new", name="new")
     * @Route("/edit/{Slug}", name="edit")
     */
    public function tricks(Tricks $trick = null, Media $images = null, Videos $videos = null, Request $request)
    {
        if (!$trick) {
            $trick = new Tricks();
        }

        if (!$images) {
            $images = $trick->getMedia();
        }

        if (!$videos) {
            $videos = $trick->getVideos();
        }
        $trickForm = $this->createForm(TrickFormType::class, $trick);
        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {

            if (!$trick->getId()) {
                $trick->setCreatedAt(new \DateTimeImmutable());
            }

            $slugger = new AsciiSlugger();
            $slug = $slugger->slug($trick->getName());
            $trick->setSlug($slug);

            $trick->setModifiedAt(new \DateTimeImmutable());
            $trick->setUsers($this->getUser());

            $mainMediaFile = $trickForm->get('mainMedia')->getData();
            $mainMedia_uploads_directory = $this->getParameter('main_media_directory');

            if (!$mainMediaFile) {
                $mainMediaFilename = "DefaultMainTrick.jpg";
                $trick->setMainMedia($mainMediaFilename);
            } else {
                $mainMediaFilename = md5(uniqid()) . '.' . $mainMediaFile->guessExtension();
                $mainMediaFile->move(
                    $mainMedia_uploads_directory,
                    $mainMediaFilename
                );
                $trick->setMainMedia($mainMediaFilename);
            }


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

            // On récupère les vidéos transmises
            $video = $trickForm->get('video')->getData();

            // On crée la source
            if (!empty($video)) {
                $vid = new Videos();
                $vid->setsource($video);
                $trick->addVideos($vid);
            }

            if ($trick->getId() !== null) {
                $this->addFlash('success', 'Modification effectuée');
            } else {
                $this->addFlash('success', 'Nouvelle figure ajoutée');
            }

            $entityManager = $this->getDoctrine->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('tricks');
        }
        return $this->render('home/new.html.twig', [
            'controller_name' => 'HomeController',
            'editMode' => $trick->getId() !== null,
            'images' => $trick->getMedia(),
            'videos' => $trick->getVideos(),
            'formTrick' => $trickForm->createView()
        ]);
    }

    /**
     * @Route("/delete/{Slug}", name="delete")
     */
    public function deleteTrick(Tricks $trick)
    {
        $this->entityManager->remove($trick);
        $this->entityManager->flush();
        $this->addFlash('success', 'Figure supprimée');
        return $this->redirectToRoute('tricks');
    }
}
