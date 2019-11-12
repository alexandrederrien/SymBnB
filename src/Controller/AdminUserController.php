<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\User;
use App\Form\AdminUserType;
use App\Service\Paginator;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{
    /**
     * Permet d'afficher la liste des utilisateurs.
     * Directement dans la route on met un requirement sur le paramètre page entre chevrons pour qu'il accepte uniquement les nombres.
     * Le point d'interrogation sert à dire que ce paramètre est optionnel, suivi d'un 1 qui est la valeur par défaut
     *
     * @Route("/admin/users/{page<\d+>?1}", name="admin_users_index")
     * @param int $page
     * @param Paginator $paginator
     * @return Response
     */
    public function index($page, Paginator $paginator)
    {
        $paginator
            ->setEntityClass(User::class)
            ->setCurrentPage($page)
        ;

        return $this->render('admin/user/index.html.twig', [
            'paginator' => $paginator
        ]);
    }

    /**
     * Permet d'éditer un utilisateur
     *
     * @Route("admin/users/{id}/edit", name="admin_users_edit")
     * @param User $user
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(User $user, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AdminUserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);;
            $manager->flush();

            $this->addFlash(
                'success',
                "L'utilisateur <strong>{$user->getFullName()}</strong> a bien été modifié"
            );
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un utilisateur
     *
     * @Route("admin/users/{id}/delete", name="admin_users_delete")
     * @param User $user
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(User $user, ObjectManager $manager)
    {
        if(count($user->getAds()) > 0) {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'utilisateur <strong>{$user->getFullName()}</strong> car il possède déjà des annonces"
            );
        } else {
            $manager->remove($user);;
            $manager->flush();

            $this->addFlash(
                'success',
                "L'utilisateur <strong>{$user->getFullName()}</strong> a bien été supprimé"
            );
        }

        return $this->redirectToRoute('admin_users_index');
    }
}
