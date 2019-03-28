<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\PokerType;
use App\Model\Deck;


class PokerController extends AbstractController
{

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     *
     * @Route("/poker/index", name="pick_a_card", methods="GET")
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(PokerType::class, null, [
            'action' => $this->generateUrl('start_the_game'),
            'method' => 'POST',
        ]);

        $error = $request->get('error') ?? '';

        return $this->render('poker/index.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     *
     * @Route("/poker/start", name="start_the_game", methods="POST")
     */
    public function start(Request $request): RedirectResponse
    {
        $formData = $request->request->get('poker');
        $card = mb_strtoupper($formData['card'] ?? '');

        if (!$card) {
            throw new Exception('Please, specify the card you want to guess');
        }

        $deck = Deck::generateDeck();

        if (!in_array($card, $deck)) {
            return $this->redirectToRoute('pick_a_card', ['error' => 'invalid_card']);
        }

        $this->get('session')->set('deck', $deck);
        $this->get('session')->set('card', $card);

        return $this->redirectToRoute('chance');
    }

    /**
     * @return Response | RedirectResponse
     *
     * @Route("/poker/chance", name="chance", methods="GET")
     */
    public function chance(): Response
    {
        $card = $this->get('session')->get('card') ?? '';
        $deck = $this->get('session')->get('deck') ?? [];

        if (!$card || !$deck) {
            return $this->redirectToRoute('pick_a_card');
        }

        return $this->render('poker/chance.html.twig', [
            'card' => $card,
            'chance' => (1 / count($deck)) * 100
        ]);
    }

    /**
     * @return RedirectResponse
     * @throws Exception
     *
     * @Route("/poker/draw", name="draw_a_card", methods="GET")
     */
    public function draw(): RedirectResponse
    {
        $card = $this->get('session')->get('card') ?? '';
        $deck = $this->get('session')->get('deck') ?? [];
        $lastCard = array_pop($deck);

        if ($card == $lastCard) {
            $this->get('session')->set('card', '');
            $this->get('session')->set('deck', []);

            return $this->redirectToRoute('finish_the_game', [
                'card' => $lastCard,
                'last_chance' => (1 / (count($deck) + 1)) * 100,
                'tries' => Deck::getDeckSize() - count($deck)
            ]);
        }

        $this->get('session')->set('deck', $deck);

        return $this->redirectToRoute('chance', ['last_card' => $lastCard]);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/poker/finish", name="finish_the_game", methods="GET")
     */
    public function finish(Request $request): Response
    {
        $card = $request->get('card');
        $lastChance = $request->get('last_chance');
        $tries = $request->get('tries');

        return $this->render('poker/finish.html.twig', [
            'card' => $card,
            'last_chance' => $lastChance,
            'tries' => $tries
        ]);
    }
}
