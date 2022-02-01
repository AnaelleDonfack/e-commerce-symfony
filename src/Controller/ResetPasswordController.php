<?php

namespace App\Controller;

use App\Controller\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ForgetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager =$entityManager;
    }

    #[Route('/mot-de-passe-oublie', name: 'reset_password')]
    public function index(Request $request): Response
    {
        if($this->getUser()){
            return$this->redirectToRoute('home');
        }

        if($request->get('email')){
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email'=>$request->get('email')]);
            if($user){
                //Etape 1 : save in database the request to change password with user, token, createdAt
                $reset_password = new ResetPassword();
                $reset_password->setClient($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTimeImmutable());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                //Etape 2 : Send mail to user with link to reset his password
                $mail = new Mail();
                $url = $this->generateUrl('update_password',['token'=>$reset_password->getToken()]);
                $content = "Bonjour ".$user->getFirstname()."<br/><br/> Vous avez demandé à réinitialiser votre mot de passe sur le site Andy's Crochet.<br/><br/><br/>";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='".$url."'>mettre à jour votre mot de passe</a>";

                $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), "Réinitialiser votre mot de passe sur Andy's Crochet",$content);
                $this->addFlash('notice', "Un mail de réinitialisation vous a été envoyé");
            }else{
                $this->addFlash('notice',"Oups il y'a eu un souci");
            }
        }
        return $this->render('reset_password/index.html.twig', [

        ]);
    }

    #[Route('/modifier-mon-mot-de-passe-oublie/{token}', name: 'update_password')]
    public function update(Request $request, $token,UserPasswordHasherInterface $passwordHasher): Response
    {
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneBy(['token'=>$token]);
        if(!$reset_password){
            return $this->redirectToRoute('reset_password');
        }
        //Vérifier si createtAt = now - 3h (user a 3h pour modifier le password)
        $now = new \DateTime();
        if($now > $reset_password->getCreatedAt()->modify('+ 3 hour') ){
            $this->addFlash('notice','Votre demande de mot de passe a expiré. Merci de la renouveller');
            return $this->redirectToRoute('reset_password');
        }
        //Modification password
        $form = $this->createForm(ForgetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $new_pwd = $form->get('new_password')->getData();
            //Encodage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($reset_password->getClient(),$new_pwd);
            $reset_password->getClient()->setPassword($hashedPassword);

            $this->entityManager->flush();
            $this->addFlash('notice','Votre mot de passe a bien été mis à jour');

            return $this->redirectToRoute('app_login');

        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView(),
        ]);


    }
}
