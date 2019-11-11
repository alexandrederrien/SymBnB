<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Service\Paginator;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * Permet d'afficher la liste d'une annonce.
     * Directement dans la route on met un requirement sur le paramètre page entre chevrons pour qu'il accepte uniquement les nombres.
     * Le point d'interrogation sert à dire que ce paramètre est optionnel, suivi d'un 1 qui est la valeur par défaut
     *
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     * @param AdRepository $repo
     * @param int $page
     * @param Paginator $paginator
     * @return Response
     */
    public function index(AdRepository $repo, $page, Paginator $paginator)
    {
        $paginator
            ->setEntityClass(Ad::class)
            ->setCurrentPage($page)
        ;

        return $this->render('admin/ad/index.html.twig', [
            'paginator' => $paginator
        ]);
    }

    /**
     * Permet d'éditer une annonce
     *
     * @Route("admin/ads/{id}/edit", name="admin_ads_edit")
     * @param Ad $ad
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);;
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été modifiée"
            );
        }

        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une annonce
     *
     * @Route("admin/ads/{id}/delete", name="admin_ads_delete")
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager)
    {
        if(count($ad->getBookings()) > 0) {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'annonce <strong>{$ad->getTitle()}</strong> car elle possède déjà des réservations"
            );
        } else {
            $manager->remove($ad);;
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée"
            );
        }

        return $this->redirectToRoute('admin_ads_index');
    }
}
