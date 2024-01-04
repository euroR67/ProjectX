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
        // Trouver l'utilisateur qui a comme id 1
        $user = $ur->find(1);

        return $this->render('home/users.html.twig', [
            'user' => $user,
        ]);
    }

    // Fonction pour envoyer un premier message en tant que expéditeur
    #[Route('/sendMessage/{id}', name: 'app_send_message')]
    public function initiateChat(UserRepository $ur, User $receiver, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            
            $receiverId = filter_var($request->request->get('receiver_id'), FILTER_SANITIZE_NUMBER_INT);
            $content = filter_var($request->request->get('content'), FILTER_SANITIZE_STRING);

            $receiverId = $request->request->get('receiver_id');
            $content = $request->request->get('content');

            $receiver = $ur->find($receiverId);

            if (!$receiver) {
                throw $this->createNotFoundException('L\'utilisateur n\'a pas été trouvé.');
            }

            $message = new Message();
            $message->setSender($user);
            $message->setReceiver($receiver);
            $message->setContent($content);

            $entityManager->persist($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home');
    }

    // Fonction pour lister les discussions

    // Fonction pour envoyer un message
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
