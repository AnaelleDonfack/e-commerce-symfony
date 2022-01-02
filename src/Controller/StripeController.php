<?php

namespace App\Controller;

use App\Classe\Cart;
use http\Url;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeController extends AbstractController
{
    #[Route('/espace-client/commande/create-session', name: 'stripe_create_session')]
    public function index(Cart $cart, $stripe_test_key): Response
    {

        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
        foreach ($cart->getFull() as $product) {
            $products_for_stripe[] =[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product['product']->getPrice(),
                    'product_data' => [
                        'name' => $product['product']->getName(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product['product']->getIllustration() ],
                    ],
                ],
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                //'price' => '{{PRICE_ID}}',
                'quantity' => $product['quantity'],
            ];

        }
        Stripe::setApiKey($stripe_test_key);

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [$products_for_stripe],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success_url',[],UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel_url',[],UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
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
