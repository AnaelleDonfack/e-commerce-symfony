<?php

namespace App\Controller\Account;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AcccountOrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('espace-client/compte/mes-commandes', name: 'account_order')]
    public function index(): Response
    {
        $orders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser());


        return $this->render('account/order/index.html.twig',[
            'orders' => $orders,
        ]);
    }

    #[Route('espace-client/compte/mes-commandes/{reference}', name: 'account_order_show')]
    public function show($reference): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['referenceStripe'=>$reference]);

        if(!$order || $order->getClient() != $this->getUser()){
            $this->addFlash('message', "Cette commande n'existe pas");
            return $this->redirectToRoute('account_order');
        }
        return $this->render('account/order/show.html.twig',[
            'order' => $order,
        ]);
    }
}
