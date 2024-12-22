<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Center;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/activity')]
final class ActivityController extends AbstractController
{
    #[Route(name: 'app_activity_index', methods: ['GET'])]
    public function index(ActivityRepository $activityRepository): Response
    {
        return $this->render('activity/index.html.twig', [
            'activities' => $activityRepository->findAll(),
        ]);
    }

    #[Route('/center/{center_id}/new', name: 'app_activity_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $center_id, EntityManagerInterface $entityManager): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $center = $entityManager->getRepository(Center::class)->find($center_id);
            if (!$center) {
                throw $this->createNotFoundException('Center not found.');
            }

            $activity->setCenter($center);
            $entityManager->persist($activity);
            $entityManager->flush();

            return $this->redirectToRoute('app_activity_show', ['center_id' => $center_id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('activity/new.html.twig', [
            'activity' => $activity,
            'form' => $form,
            'center_id' => $center_id,
        ]);
    }


    #[Route('/center/{center_id}/show', name: 'app_activity_show', methods: ['GET'])]
    public function show(int $center_id, ActivityRepository $activityRepository): Response
    {
        // Fetch activities associated with the center ID
        $activities = $activityRepository->findBy(['center' => $center_id]);

        return $this->render('activity/show.html.twig', [
            'activities' => $activities,
            'center_id' => $center_id,
        ]);
    }


    #[Route('/{id}/edit/center/{center_id}', name: 'app_activity_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Activity $activity, int $center_id, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $center = $entityManager->getRepository(Center::class)->find($center_id);
            if (!$center) {
                throw $this->createNotFoundException('Center not found.');
            }

            $activity->setCenter($center);
            $entityManager->flush();

            return $this->redirectToRoute('app_activity_show', ['center_id' => $center_id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('activity/edit.html.twig', [
            'activity' => $activity,
            'form' => $form,
            'center_id' => $center_id,
        ]);
    }


    #[Route('/{id}', name: 'app_activity_delete', methods: ['POST'])]
    public function delete(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        $center = $activity->getCenter(); // Fetch the related center
        $center_id = $center?->getId(); // Safely get the center ID

        if ($this->isCsrfTokenValid('delete' . $activity->getId(), $request->get('_token'))) {
            $entityManager->remove($activity);
            $entityManager->flush();
        }

        if ($center_id) {
            // Redirect to the show page of the specific center
            return $this->redirectToRoute('app_activity_show', ['center_id' => $center_id], Response::HTTP_SEE_OTHER);
        }

        // If no center is associated, redirect to the index page
        return $this->redirectToRoute('app_activity_index', [], Response::HTTP_SEE_OTHER);
    }
}
