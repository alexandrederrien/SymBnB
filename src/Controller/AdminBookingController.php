<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use App\Service\Paginator;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_bookings_index")
     * @param BookingRepository $repo
     * @param $page
     * @param Paginator $paginator
     * @return Response
     */
    public function index(BookingRepository $repo, $page, Paginator $paginator)
    {
        $paginator
            ->setEntityClass(Booking::class)
            ->setCurrentPage($page)
        ;

        return $this->render('admin/booking/index.html.twig', [
            'paginator' => $paginator
        ]);
    }

    /**
     * Permet de modifier une réservation
     *
     * @Route("admin/bookings/{id}/edit", name="admin_bookings_edit")
     * @param Booking $booking
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Booking $booking, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AdminBookingType::class, $booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation n°<strong>{$booking->getId()}</strong> a bien été modifiée"
            );

            return $this->redirectToRoute('admin_bookings_index');
        }

        return $this->render('admin/booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une réservation
     *
     * @Route("admin/bookings/{id}/delete", name="admin_bookings_delete")
     * @param Booking $booking
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Booking $booking, ObjectManager $manager)
    {
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            "La réservation n°<strong>{$booking->getId()}</strong> a bien été supprimé"
        );

        return $this->redirectToRoute('admin_bookings_index');
    }
}
