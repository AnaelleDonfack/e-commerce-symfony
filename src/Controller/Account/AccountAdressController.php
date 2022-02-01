<?php

namespace App\Controller\Account;

use App\Controller\Classe\Cart;
use App\Entity\Adress;
use App\Form\AdressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAdressController extends AbstractController
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('espace-client/compte/adresses', name: 'account_adress')]
    public function index(): Response
    {
        //dd($this->getUser()->getAdresses());
        return $this->render('account/adress/index.html.twig', [

        ]);
    }

    #[Route('espace-client/compte/creer-une-addresse', name: 'account_adress_add')]
    public function add(Cart $cart,Request $request): Response
    {
        $adress = new Adress();
        $form = $this->createForm(AdressType::class, $adress);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $adress->setClient($this->getUser());

            //Enregistrement de la data dans la bd
            $this->entityManager->persist($adress);
            $this->entityManager->flush();
            if($cart->get()){
                return $this->redirectToRoute('order');
            }else{
                return $this->redirectToRoute('account_adress');
            }

        }
        //dd($this->getUser()->getAdresses());
        return $this->render('account/adress/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('espace-client/compte/modifier-une-addresse/{id}', name: 'account_adress_edit')]
    public function edit(Request $request , $id): Response
    {
        $adress = $this->entityManager->getRepository(Adress::class)->findOneBy(['id' => $id]);

        if(!$adress || $adress->getClient() != $this->getUser()){
            return $this->redirectToRoute('account_adress');
        }
        $form = $this->createForm(AdressType::class, $adress);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->entityManager->flush();

            return $this->redirectToRoute('account_adress');
        }
        //dd($this->getUser()->getAdresses());
        return $this->render('account/adress/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('espace-client/compte/supprimer-une-addresse/{id}', name: 'account_adress_delete')]
    public function delete($id): Response
    {
        $adress = $this->entityManager->getRepository(Adress::class)->findOneBy(['id' => $id]);

        if($adress || $adress->getClient() == $this->getUser()){
            $this->entityManager->remove($adress);
            $this->entityManager->flush();
            $this->addFlash('message', "Votre adresse a bien été supprimé");
        }

        return $this->redirectToRoute('account_adress');
    }
}
