<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Videos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class MediasController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $getDoctrine,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @Route("/media/{id}/delete", name="deleteimg")
     */
    public function deleteImage(Media $media)
    {
        //$entityManager = $this->getDoctrine->getManager();
        //$media = $entityManager->getRepository(Media::class)->find($id);
        $this->entityManager->remove($media);
        $this->entityManager->flush();
        return $this->redirectToRoute('edit', [
            'Slug' => $media->getTricks()->getSlug()
        ]);
    }

    /**
     * @Route("/video/{id}/delete", name="deletevideo")
     */
    public function deleteVideo(Videos $video)
    {
        //$entityManager = $this->getDoctrine->getManager();
        //$video = $entityManager->getRepository(Videos::class)->find($id);
        $this->entityManager->remove($video);
        $this->entityManager->flush();
        return $this->redirectToRoute('edit', [
            'Slug' => $video->getTrick()->getSlug()
        ]);
    }
}
