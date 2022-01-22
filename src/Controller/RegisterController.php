<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/inscription', name: 'register')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {

        $notifiaction = null;
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() ){
            $user = $form->getData();

            //Check if user doesn't already exist in database
            $search_mail = $this->entityManager->getRepository(User::class)->findOneBy(['email'=>$user->getEmail()]);

            if(!$search_mail){
                $hashedPassword = $passwordHasher->hashPassword($user,$user->getPassword());
                $user->setPassword($hashedPassword);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $notifiaction = "Votre inscription s'est correctement déroulé. Vous pouvez dès à présent vous connecter à votre compte";
                $content = "Bonjour ".$user->getFirstname()."<br/> Bienvenue sur la première boutique dédiée au made in France";
                $mail = new Mail();
                $mail->send($user->getEmail(), $user->getFirstname(), "Bienvenue chez Andy's Crochet",$content);

            }else{
                $notifiaction = "L'email que vous avez renseigné existe déja";
            }

        }
        return $this->render('register/index.html.twig',[
            'register_form' => $form->createView(),
            'notification' => $notifiaction,
        ]);
    }
}
