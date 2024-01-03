<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    // Fonction pour lister les utilisateur
    #[Route('/', name: 'app_home')]
    public function index(UserRepository $ur, Request $request, EntityManagerInterface $entityManager, MessageRepository $mr): Response
    {
        $users = $ur->findAll();

        return $this->render('home/users.html.twig', [
            'users' => $users,
        ]);
    }

    // Fonction pour envoyer un premier message en tant que expéditeur
    #[Route('/initiate-chat/{receiver}', name: 'initiate_chat')]
    public function initiateChat(User $receiver, Request $request, EntityManagerInterface $entityManager): Response
    {
        // On récupère l'utilisateur connecté
        $user = $this->getUser();

        // On crée un nouveau message
        $message = new Message();

        // Défini le destinataire du message
        $message->setReceiver($receiver);

        $form = $this->createForm(MessageType::class, $message);

        if($request->isMethod('POST')) {
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                // On enregistre le message
                $entityManager->persist($message);
                $entityManager->flush();

                // On redirige vers le chat
                return $this->redirectToRoute('app_chat');
            }
        }

        return $this->render('home/initiate_chat.html.twig', [
            'form' => $form->createView(),
            'receiver' => $receiver,
        ]);
    }

    // Fonction pour afficher le chat et envoyer des messages
    #[Route('/chat', name: 'app_chat')]
    public function chat(Request $request, EntityManagerInterface $entityManager, MessageRepository $mr): Response
    {
        $message = new Message();

        $form = $this->createForm(MessageType::class, $message);

        $emptyForm = clone $form;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($message);
            $entityManager->flush();

            $form = $emptyForm;

        }

        return $this->render('home/index.html.twig', [
            'form' => $form,
            'messages' => $mr->findAll([], ['createdAt' => 'DESC']),
        ]);
    }
}
