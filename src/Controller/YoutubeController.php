<?php

namespace App\Controller;

use App\Entity\Youtube;
use App\Form\YoutubeType;
use App\Repository\YoutubeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class YoutubeController extends AbstractController
{
    /**
     * @Route("/", name="app_home" ,methods="GET")
     */
    public function index(YoutubeRepository $youtubeRepository): Response
    {
        return $this->render('youtube/index.html.twig', [
            'youtubes' => $youtubeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/add", name="app_add" ,methods="POST|GET")
     */
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $youtube = new Youtube();

        $form = $this->createForm(YoutubeType::class, $youtube);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $youtube = $form->getData();

            $em->persist($youtube);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }


        return $this->renderForm('youtube/add.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @Route("/video/{id}", name="app_video",methods="GET")
     */
    public function video(Youtube $youtube): Response
    {
        return $this->render('youtube/video.html.twig', [
            'name' => $youtube->getName(),
            'url' => $youtube->getUrl(),
        ]);
    }


    /**
     * @Route("/del", name="delete_all", methods="DELETE|GET")
     */
    public function deleteAll(EntityManagerInterface $em): Response
    {
        $connection = $em->getConnection();
        $platform   = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL('youtube'));
        $connection->exec('ALTER SEQUENCE youtube_id_seq RESTART WITH 1');
        return $this->redirectToRoute('app_home');
    }
}