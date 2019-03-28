<?php

use PHPUnit\Framework\TestCase;
use App\Model\Deck;

class DeckTest extends TestCase
{
    protected $rightCards = ['C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'C9', 'C10', 'CJ', 'CQ', 'CK', 'CA', 'D2',
        'D3', 'D4', 'D5', 'D6', 'D7', 'D8', 'D9', 'D10', 'DJ', 'DQ', 'DK', 'DA', 'H2', 'H3', 'H4',
        'H5', 'H6', 'H7', 'H8', 'H9', 'H10', 'HJ', 'HQ', 'HK', 'HA', 'S2', 'S3', 'S4', 'S5', 'S6',
        'S7', 'S8', 'S9', 'S10', 'SJ', 'SQ', 'SK', 'SA'];

    public function testDeckHasAllCards()
    {
        $deck = Deck::generateDeck(false);

        foreach ($this->rightCards as $card) {
            $this->assertTrue(
                in_array($card, $deck),
                sprintf('Generated deck does not contain %s card', $card)
            );
        }
    }

    public function testDeckDoesNotHaveExtraCards()
    {
        $deck = Deck::generateDeck(false);

        foreach ($deck as $card) {
            $this->assertFalse(
                !in_array($card, $this->rightCards),
                sprintf('Generated deck has a wrong card: %s', $card)
            );
        }
    }

    /**
     * This is actually not the best test. There is a slight chance that the test will fail
     * Even tho the deck is randomized. Also the test assumes some detail about the Deck implementation
     * By using integers as a key for card collection.
     */
    public function testIsDeckRandomized()
    {
        $standardDeck = Deck::generateDeck(false);
        $randomizedDeck = Deck::generateDeck(true);

        $standardDeckSize = count($standardDeck);
        $randomizedDeckSize = count($randomizedDeck);

        $this->assertEquals(
            $standardDeckSize,
            $randomizedDeckSize,
            sprintf(
                'Size of randomized deck (%s) is different from the size of randomized deck (%s)',
                $standardDeckSize,
                $randomizedDeckSize
            ));

        $allCardsInTheSameOrderFlag = true;
        for ($i = 0; $i < $standardDeckSize; $i++) {
            if ($standardDeck[$i] != $randomizedDeck[$i]) {
                $allCardsInTheSameOrderFlag = false;
            }
        }

        $this->assertFalse($allCardsInTheSameOrderFlag, 'Deck is not randomized');
    }
}
