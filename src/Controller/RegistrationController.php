<?php

namespace App\Controller;

use App\Entity\Users;
use App\Service\SendMailService;
use App\Service\JWTService;
use App\Security\UsersAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{



    /**
     * @Route("/signup", name="signup")
     */
    public function signup(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator, EntityManagerInterface $entityManager,  SendMailService $email, JWTService $jwt): Response
    {
        $user = new Users();

        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur'
            ])
            ->add('Email', EmailType::class)
            ->add('password', PasswordType::class, [
                'label' => 'Entrez votre mot de passe'
            ])
            ->add('Photo', FileType::class, [
                'required' => false,
                'label' => 'Photo de profil'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encodage du mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $avatarFile = $form->get('Photo')->getData();
            $avatar_uploads_directory = $this->getParameter('avatar_directory');

            if (!$avatarFile) {
                $avatarFilename = "DefaultAvatar.png";
                $user->setPhoto($avatarFilename);
            } else {
                $avatarFilename = md5(uniqid()) . '.' . $avatarFile->guessExtension();
                $avatarFile->move(
                    $avatar_uploads_directory,
                    $avatarFilename
                );
                $user->setPhoto($avatarFilename);
            }


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // On génère le JWT de l'utilisateur
            // On crée le Header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // On crée le Payload
            $payload = [
                'user_id' => $user->getId()
            ];

            // On génère le token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            $email->send(
                'no-reply@snowtricks.com',
                $user->getEmail(),
                'Activation de votre compte sur le site SnowTricks',
                'registration',
                compact('user', 'token')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );

            $this->addFlash('success', 'Votre compte a bien été créé');

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/signup.html.twig', [
            'user' => $user,
            'registrationForm' => $form->createView()
        ]);
    }

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser($token, JWTService $jwt, UsersRepository $usersRepository, EntityManagerInterface $em): Response
    {
        //On vérifie si le token est valide, n'a pas expiré et n'a pas été modifié
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            // On récupère le payload
            $payload = $jwt->getPayload($token);

            // On récupère le user du token
            $user = $usersRepository->find($payload['user_id']);

            //On vérifie que l'utilisateur existe et n'a pas encore activé son compte
            if ($user && !$user->getIsVerified()) {
                $user->setIsVerified(true);
                $em->flush($user);
                $this->addFlash('success', 'Compte activé');
                return $this->redirectToRoute('home');
            }
        }
        // Ici un problème se pose dans le token
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/renvoiverif', name: 'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail, UsersRepository $usersRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        if ($user->getIsVerified()) {
            $this->addFlash('warning', 'Cet utilisateur est déjà activé');
            return $this->redirectToRoute('home');
        }

        // On génère le JWT de l'utilisateur
        // On crée le Header
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        // On crée le Payload
        $payload = [
            'user_id' => $user->getId()
        ];

        // On génère le token
        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        // On envoie un mail
        $mail->send(
            'no-reply@snowtricks.fr',
            $user->getEmail(),
            'Activation de votre compte sur le site Snowtricks',
            'registration',
            compact('user', 'token')
        );
        $this->addFlash('success', 'Email de vérification envoyé');
        return $this->redirectToRoute('profile_index');
    }
}
