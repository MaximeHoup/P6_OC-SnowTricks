<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Entity\Media;
use App\Entity\Comments;
use App\Entity\FigureGroup;
use App\Entity\Videos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\CommentType;
use App\Repository\CommentsRepository;
use App\Repository\TricksRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $getDoctrine
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
     * @Route("/trick/{id}-{name}/{page?1}", name="show")
     */
    public function show($id, $name, $page, CommentsRepository $commentsRepository, Request $request)
    {
        $trick = $this->getDoctrine->getRepository(Tricks::class)->find($id);
        $name = $this->getDoctrine->getRepository(Tricks::class)->find($name);
        $images = $this->getDoctrine->getRepository(Media::class)->findby(['tricks' => $trick->getId()]);
        $videos = $this->getDoctrine->getRepository(Videos::class)->findby(['trick' => $trick->getId()]);
        $figureGroup = $this->getDoctrine->getRepository(FigureGroup::class)->find($id);

        $comment = new Comments();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        $commentsperpage = 10;
        $nbcomments = $commentsRepository->count([]);
        (int)$nbpages = ceil(num: $nbcomments / $commentsperpage);
        $comments = $this->getDoctrine->getRepository(Comments::class)->findBy(['Tricks' => $trick->getId()], ['id' => 'DESC'], limit: $commentsperpage, offset: ($page - 1) * $commentsperpage);


        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setCreatedAt(new \DateTimeImmutable())
                ->setUsers($this->getUser())
                ->setTricks($trick);

            $entityManager = $this->getDoctrine->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('tricks', [
                'trick' => $trick,
                'id' => $id,
                'images' => $images,
                'figureGroup' => $figureGroup,
                'videos' => $videos,
                'comments' => $comments,
                'nbpages' => $nbpages,
                'page' => $page,
                'name' => $name,
                'formComment' => $commentForm->createView()
            ]);
        }

        return $this->render('home/show.html.twig', [
            'controller_name' => 'HomeController',
            'trick' => $trick,
            'id' => $id,
            'images' => $images,
            'figureGroup' => $figureGroup,
            'videos' => $videos,
            'comments' => $comments,
            'nbpages' => $nbpages,
            'page' => $page,
            'name' => $name,
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
    public function tricks(Tricks $trick = null, Media $images = null, Videos $videos = null, Request $request)
    {
        if (!$trick) {
            $trick = new Tricks();
        }

        if (!$images) {
            $images = $this->getDoctrine->getRepository(Media::class)->findby(['tricks' => $trick->getId()]);
        }

        if (!$videos) {
            $videos = $this->getDoctrine->getRepository(Videos::class)->findby(['trick' => $trick->getId()]);
        }

        $trickForm = $this->createFormBuilder($trick)
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('figureGroup', EntityType::class, [
                'class' => FigureGroup::class,
                'choice_label' => 'name',
                'label' => 'Groupe auquel appartient la figure'
            ])
            ->add('Description')
            ->add('mainMedia', FileType::class, [
                'data_class' => null,
                'mapped' => false,
                'label' => false,
                'required' => false
            ])
            ->add('media', FileType::class, [
                'data_class' => null,
                'mapped' => false,
                'label' => false,
                'multiple' => true,
                'required' => false
            ])
            ->add('video', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Vidéo'
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
            'images' => $images,
            'videos' => $videos,
            'formTrick' => $trickForm->createView()
        ]);
    }

    /**
     * @Route("/trick/{id}/delete", name="delete")
     */
    public function deleteTrick(Tricks $trick = null, $id)
    {
        $entityManager = $this->getDoctrine->getManager();
        $trick = $entityManager->getRepository(Tricks::class)->find($id);

        $comments = $entityManager->getRepository(Comments::class)->findBy(['Tricks' => $trick->getId()]);
        foreach ($comments as $comment) {
            $entityManager->remove($comment);
        }
        $entityManager->remove($trick);
        $entityManager->flush();
        return $this->redirectToRoute('tricks');
    }

    /**
     * @Route("/media/{id}/delete", name="deleteimg")
     */
    public function deleteImage(Media $media = null, $id)
    {
        $entityManager = $this->getDoctrine->getManager();
        $media = $entityManager->getRepository(Media::class)->find($id);
        $entityManager->remove($media);
        $entityManager->flush();
        return $this->redirectToRoute('tricks');
    }

    /**
     * @Route("/video/{id}/delete", name="deletevideo")
     */
    public function deleteVideo(Videos $video = null, $id)
    {
        $entityManager = $this->getDoctrine->getManager();
        $video = $entityManager->getRepository(Videos::class)->find($id);
        $entityManager->remove($video);
        $entityManager->flush();
        return $this->redirectToRoute('tricks');
    }
}
