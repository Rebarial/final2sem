<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Profile;
use App\Form\UserType;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function index(Request $request, ProfileRepository $repo, UserRepository $urep)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$urep->findOneBy(array("username" => $user->getUsername()))) {
                // Encode the new users password
                $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

                // Set their role
                $user->setRoles(['ROLE_USER']);
                // Add profile
                $profile = new Profile();
                $profile->setUser($user);
                $profile->setMoney(0);
                $repo->add($profile, true);
                // Save
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('app_login');
            }
            else {
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}