<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\PhraseType;
use App\Model\Phrase;


/**
 * Class PhraseController
 * @package App\Controller
 */
class PhraseController extends AbstractController
{

    /**
     * @return Response
     *
     * @Route("/phrase/index", name ="phrase",  methods="GET")
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(PhraseType::class, null, [
            'action' => $this->generateUrl('analyze'),
            'method' => 'POST',
        ]);

        return $this->render('phrase/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/phrase/analyze", name="analyze", methods="POST")
     */
    public function analyze(Request $request): Response
    {
        try {
            $form = $this->createForm(PhraseType::class);
            $form->handleRequest($request);
            $phrase = $form->get('phrase')->getData() ?? '';

            $row = new Phrase($phrase);
            $data = $row->getStatistics();

            return $this->render('phrase/results.html.twig', [
                'data' => $data
            ]);
        } catch (Exception $e) {
            return $this->render('phrase/error.html.twig');
        }
    }
}