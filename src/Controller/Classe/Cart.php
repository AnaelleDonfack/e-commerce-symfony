<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class Cart
 * @package App\Classe
 * Contenant toute la mécanique concernant le panier : ajout, suppression
 */
class Cart{

    private $requestStack;
    private $entityManager;
    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;

    }

    public function getFull(){

        $cartComplete = [];
        if($this->get()) {
            foreach ($this->get() as $id => $quantity) {
                $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);
                if($product){
                    $cartComplete[] = [
                        'product' => $product,
                    'quantity' => $quantity,
                ];
                }else{
                    $this->delete($id);
                    continue;
                }
            }
        }

        return $cartComplete;

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
        //Cree une session panier:cart auquel tu associes un tableau[id, quantity]
        $session->set('cart', $cart);
    }

    public function decrease($id){

        $session = $this->requestStack->getSession();
        //Tu me renvoies une session sous forme de tableau
        $cart = $session->get('cart',[]);

        //Vérifier si la quantité de notre produit > 1
        if($cart[$id] > 1){
            $cart[$id]--;
        }else{
            unset($cart[$id]);
        }

        //Cree une session panier:cart auquel tu associes un tableau[id, quantity]
        $session->set('cart', $cart);
    }

    public function get(){

        return $this->requestStack->getSession()->get('cart');
    }

    public function remove(){
        return $this->requestStack->getSession()->remove('cart');
    }

    public function delete($id){
        $cart = $this->requestStack->getSession()->get('cart');

        //Retire du tableau cart celui qui a l'id passé en paramètre
         unset($cart[$id]);

         return $this->requestStack->getSession()->set('cart', $cart);
    }
}