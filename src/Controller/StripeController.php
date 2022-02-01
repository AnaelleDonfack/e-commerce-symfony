<?php

namespace App\Controller;

use App\Controller\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use http\Url;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/espace-client/commande/create-session/{reference}', name: 'stripe_create_session')]
    public function index(Cart $cart, $stripe_test_key,$reference): Response
    {
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
        $products_for_stripe = [];

        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['referenceStripe'=>$reference]);
        if(!$order){
            new JsonResponse(['error' => "Votre commande n'existe pas "]);
            return $this->redirectToRoute('order');
        }

        foreach ($order->getOrderDetails()->getValues() as $product) {
            $product_object = $this->entityManager->getRepository(Product::class)->findOneBy(['name'=>$product->getProduct()]);
            $products_for_stripe[] =[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product_object->getIllustration() ],
                    ],
                ],
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                //'price' => '{{PRICE_ID}}',
                'quantity' => $product->getQuantity(),
            ];


        }
        $products_for_stripe[] =[
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],
            # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
            //'price' => '{{PRICE_ID}}',
            'quantity' => 1,
        ];
        Stripe::setApiKey($stripe_test_key);
        $email= $this->getUser()->getEmail();
        $checkout_session = Session::create([
            'customer_email' => $email,
            'payment_method_types' => ['card'],
            'line_items' => [$products_for_stripe],
            'mode' => 'payment',
            'success_url' =>$YOUR_DOMAIN.'/espace-client/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' =>$YOUR_DOMAIN.'/espace-client/commande/erreur/{CHECKOUT_SESSION_ID}',
            //'success_url' => $this->generateUrl('success_url',[],UrlGeneratorInterface::ABSOLUTE_URL),
            //'cancel_url' => $this->generateUrl('cancel_url',[],UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        $order->setStripeSessionId($checkout_session->id);
        $this->entityManager->flush();
        return $this->redirect($checkout_session->url, 303);
        //$response->withHeader('Location', $checkout_session->url)->withStatus(303);
    }

    #[Route('/success-url', name: 'success_url')]
    public function successUrl(): Response{
        return $this->render('order/add.html.twig',[]);
    }

    #[Route('/cancel-url', name: 'cancel_url')]
    public function cancelUrl(): Response{
        return $this->render('order/cancel.html.twig',[]);
    }
}
