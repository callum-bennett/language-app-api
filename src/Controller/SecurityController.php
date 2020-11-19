<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\AppAuthenticator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return new JsonResponse(['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request,ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder,
            AppAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler) {

        $username = $request->request->get("username");
        $rawPassword = $request->request->get("password");

        try {
            $user = new User();

            $user->setUsername($username);
            $user->setPlainPassword($rawPassword);

            $violations = $validator->validate($user);
            if ($violations->count() > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[] = $violation->getMessage();
                }

                return new JsonResponse($errors, 400);
            }

            $password = $passwordEncoder->encodePassword($user, $rawPassword);
            $user->eraseCredentials();
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        catch (Exception $e) {
            return new JsonResponse("An unknown error occurred.", 400);
        }

        return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                "main"
        );
    }
}
