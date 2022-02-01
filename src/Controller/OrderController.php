<?php

namespace App\Controller;

use App\Entity\OrderDetails;
use App\Form\OrderType;
use App\Controller\Classe\Cart;
use App\Entity\Order;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/espace-client/commandes', name: 'order')]
    public function index(Cart $cart, Request $request ): Response
    {

        if(!($this->getUser()->getAdresses()->getValues())){
            return $this->redirectToRoute('account_adress_add');
        }
        $form = $this->createForm(OrderType::class, null, [
            'user' =>$this->getUser()
        ]);
        
        return $this->render('order/index.html.twig',[
            'form' => $form->createView(),
            'cart' => $cart->getFull(),
        ]);
    }

    #[Route('/espace-client/commande/recapitulatif', name: 'order_recap', methods: ['POST'])]
    public function add(Cart $cart, Request $request): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            'user' =>$this->getUser()
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $carrier = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname().''.$delivery->getLastname();
            if($delivery->getCompany()) {
                $delivery_content .= '<br/>'.$delivery->getCompany();

            }
            $delivery_content .= '<br/>'.$delivery->getAddress();
            $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity();
            $delivery_content .= '<br/>'.$delivery->getCountry();

            //Enregistrer ma commande : Orders
            $order = new Order();
            $date = new \DateTime();
            $reference = $date->format('dmY').'-'.uniqid();
            $order->setReferenceStripe($reference);
            $order->setClient($this->getUser());
            $order->setCarrierName($carrier->getName());
            $order->setCarrierPrice($carrier->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);
            $order->setState(0);

            $this->entityManager->persist($order);
            //Enregistrer mes produits : OrderDetails
            foreach ($cart->getFull() as $product){
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $this->entityManager->persist($orderDetails);
            }

            $this->entityManager->flush();

            return $this->render('order/add.html.twig',[
                'cart' => $cart->getFull(),
                'carrier' => $carrier,
                'delivery' => $delivery_content,
                'reference_stripe' => $order->getReferenceStripe(),
            ]);
        }

        return $this->redirectToRoute('cart');
        

    }
}
