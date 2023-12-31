<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Symfony\UX\Turbo\TurboBundle;
use Symfony\Component\Mercure\Update;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création d'un nouveau message
        $message = new Message();

        $form = $this->createForm(MessageType::class, $message);

        // Utilisé pour réinitialiser le formulaire après l'envoi "Post"
        $emptyForm = clone $form;

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            // Enregistrement du message en BDD
            $entityManager->persist($message);
            $entityManager->flush();
            // Force un formulaire vide a initialiser
            // Il va remplacer le contenu du Turbo Frame après l'envoi "Post"
            $form = $emptyForm;
        }

        return $this->render('home/index.html.twig', [
            'form' => $form,
        ]);
    }
}
