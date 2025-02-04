<?php

namespace App\Controller\Page;

use App\Domain\SendSimpleConfirmationSmsCommand;
use App\Domain\SendTeamConfirmationSmsCommand;
use App\Entity\Crossing;
use App\Entity\Reservation\SimpleReservation;
use App\Entity\Reservation\TeamReservation;
use App\Entity\Review;
use App\Entity\User;
use App\Enum\SiteMessagePlaceEnum;
use App\Form\ReviewType;
use App\Form\SimpleReservationType;
use App\Form\TeamReservationType;
use App\Repository\CrossingRepository;
use App\Repository\SimpleReservationRepository;
use App\Repository\SiteMessageRepository;
use App\Repository\TeamReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class CrossingController extends AbstractController
{
    #[Route(path: '/crossings', name: 'crossings')]
    public function crossings(CrossingRepository $crossingRepository): Response
    {
        return $this->render('page/crossing/list.html.twig', [
            'crossings' => $crossingRepository->findAllFuturCrossings(),
        ]);
    }

    #[Route(path: '/crossings/{id}', name: 'crossing')]
    public function crossing(
        Request $request,
        Crossing $crossing,
        EntityManagerInterface $em,
        TeamReservationRepository $teamReservationRepository,
        SimpleReservationRepository $simpleReservationRepository,
        SiteMessageRepository $siteMessageRepository,
        MessageBusInterface $bus,
    ): Response {
        $user = $this->getUser();
        $date = $crossing->getDate();
        $isPassed = !$date || $date < new \DateTime();

        $teamReservationForms = null;
        $simpleReservationForm = null;
        $reviewForm = null;

        if ($user && $user instanceof User && !$isPassed) {
            foreach ($user->getOwnedTeams() as $team) {
                $newTeamReservation = new TeamReservation();
                $form = $this->createForm(TeamReservationType::class, $newTeamReservation, ['team' => $team]);

                $teamReservation = $teamReservationRepository->findOneBy(['crossing' => $crossing, 'team' => $team]);
                if (null !== $teamReservation) {
                    continue;
                }

                try {
                    $form->handleRequest($request);
                } catch (\Exception) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de la réservation.');
                }

                $displayForm = true;
                if ($form->isSubmitted() && $form->isValid()) {
                    $newTeamReservation->setCrossing($crossing);
                    $newTeamReservation->setTeam($team);
                    $em->persist($newTeamReservation);
                    $em->flush();

                    $displayForm = false;
                    $bus->dispatch(new SendTeamConfirmationSmsCommand($team, $crossing));
                    $this->addFlash('success', 'Réservation effectuée avec succès.');
                }

                if ($displayForm) {
                    $teamReservationForms[] = $form->createView();
                }
            }

            $simpleReservation = $simpleReservationRepository->findOneBy(['crossing' => $crossing, 'passenger' => $user]);
            if (!$simpleReservation) {
                $newSimpleReservation = new SimpleReservation();

                $displayForm = true;
                $simpleReservationForm = $this->createForm(SimpleReservationType::class, $newSimpleReservation);

                try {
                    $simpleReservationForm->handleRequest($request);
                } catch (\Exception) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de la réservation.');
                }

                if ($simpleReservationForm->isSubmitted() && $simpleReservationForm->isValid()) {
                    $newSimpleReservation->setCrossing($crossing);
                    $newSimpleReservation->setPassenger($user);
                    $em->persist($newSimpleReservation);
                    $em->flush();

                    $displayForm = false;
                    $bus->dispatch(new SendSimpleConfirmationSmsCommand($user, $crossing));
                    $this->addFlash('success', 'Réservation effectuée avec succès.');
                }

                $simpleReservationForm = $displayForm ? $simpleReservationForm->createView() : null;
            }

            $newReview = new Review();
            $reviewForm = $this->createForm(ReviewType::class, $newReview);

            try {
                $reviewForm->handleRequest($request);
            } catch (\Exception) {
                $this->addFlash('danger', 'Une erreur est survenue lors de la publication de votre avis.');
            }

            if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
                $newReview->setCrossing($crossing);
                $newReview->setAuthor($user);
                $em->persist($newReview);
                $em->flush();

                $this->addFlash('success', 'Avis publié avec succès.');
            }

            $reviewForm = $reviewForm->createView();
        }

        $availablePlaces = $crossing->getRafts()->reduce(fn ($carry, $raft) => $carry + $raft->getPlaces(), 0);
        $remainingPlaces = $availablePlaces;

        foreach ($crossing->getReservations() as $reservation) {
            if ($reservation instanceof SimpleReservation) {
                --$remainingPlaces;
            } elseif ($reservation instanceof TeamReservation) {
                $remainingPlaces -= $reservation->getTeam()?->getMembers()?->count() ?? 0;
            }
        }

        return $this->render('page/crossing/index.html.twig', [
            'crossing' => $crossing,
            'availablePlaces' => $availablePlaces,
            'remainingPlaces' => $remainingPlaces < 0 ? 0 : $remainingPlaces,
            'teamReservationForms' => $teamReservationForms,
            'simpleReservationForm' => $simpleReservationForm,
            'reviewForm' => $reviewForm,
            'isPassed' => $isPassed,
            'passedMessage' => $siteMessageRepository->findByPlace(SiteMessagePlaceEnum::PASSED_CROSSING)?->getContent() ?? '',
            'unlimitedMessage' => $siteMessageRepository->findByPlace(SiteMessagePlaceEnum::UNLIMITED_CROSSING)?->getContent() ?? '',
        ]);
    }
}
