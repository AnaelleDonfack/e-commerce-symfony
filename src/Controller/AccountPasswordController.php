<?php

namespace App\Controller;

use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/modifier-mot-de-passe', name: 'account_password')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $params = $this->checkUser();

        $user = $params['user'];

        $form = $this->createForm(ResetPasswordType::class, $user);
        $params['reset_form'] = $form->createView();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $old_pwd = $form->get('old_password')->getData();

            if($passwordHasher->isPasswordValid($user, $old_pwd)){
                $new_pwd = $form->get('new_password')->getData();
                $hashedPassword = $passwordHasher->hashPassword($user,$new_pwd);
                $user->setPassword($hashedPassword);

                $this->entityManager->flush();
                $params['notification'] = "Votre mot de passe a été mis à jour";
            }else{
                $params['notification'] = "Votre mot de passe actuel est incorrect";
            }
        }
        return $this->render('account/password.html.twig',$params);
    }

    public function checkUser(){

        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('home');
        }
        $params['user'] = $user;

        return $params;

    }
}
