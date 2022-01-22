<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Order;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/espace-client/commande/merci/{stripeSessionId}', name: 'order_validate')]
    public function index($stripeSessionId, Cart $cart): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['stripeSessionId'=>$stripeSessionId]);

        if(!$order || $order->getClient() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        // Modifier le status isPaid de notre commande en le passant à true :1
        if(!$order->getIsPaid()){
            //Vider la session cart
            $cart->remove();
            $order->setIsPaid(1);

            $this->entityManager->flush();
            //Envoyer un email au client pour lui confirmer sa commande
            $content = "Bonjour ".$order->getClient()->getFirstname()."<br/> Merci pour votre commande";
            $mail = new Mail();
            $mail->send($order->getClient()->getEmail(), $order->getClient()->getFirstname(), "Votre commande Andy's Crochet est confirmée",$content);
        }




        // Afficher les quelques infos de la commande de l'utilisateur
        return $this->render('order_success/index.html.twig', [
            'order' => $order,
        ]);
    }
}
