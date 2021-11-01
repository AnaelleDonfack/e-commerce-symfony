<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart{

    private $requestStack;
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    //Fonction qui permet d'ajout un produit au panier
    public function add($id){

        $session = $this->requestStack->getSession();
        //Tu me renvoies une session sous forme de tableau
        $cart = $session->get('cart',[]);

        //SI le produit d'id spécifique existe deja tu incrémentes
        if(!empty($cart[$id])){
            $cart[$id]++;
        }else{
            $cart[$id] = 1;
        }
        //Cree une session panier auquel tu associes un tableau[id, quantity]
        $session->set('cart', $cart);
    }

    public function get(){

        return $this->requestStack->getSession()->get('cart');
    }

    public function remove(){
        return $this->requestStack->getSession()->remove('cart');
    }
}