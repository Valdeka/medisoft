<?php

namespace App\Model;

use App\Utils\CartesianProduct;

class Deck
{

    const SUITES = ['C', 'D', 'H', 'S'];

    const RANKS = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];

    public static function generateDeck(bool $randomize = true): array
    {
        $deckGeneration = [
            'suites' => static::SUITES,
            'ranks' => static::RANKS
        ];

        $cartesianProduct = (new CartesianProduct($deckGeneration))->asArray();

        if ($randomize) {
            shuffle($cartesianProduct);
        }

        return $cartesianProduct;
    }

    public static function getDeckSize(): int
    {
        return count(static::SUITES) * count(static::RANKS);
    }
}
