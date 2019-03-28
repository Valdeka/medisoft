<?php

namespace App\Model;

/**
 * Class Phrase
 * @package App\Model
 */
class Phrase
{
    const MAX_LENGTH = 255;

    protected $row;

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->row = $this->validate($data);
    }

    /**
     * @param string $data
     * @return bool|string
     */
    protected function validate(string $data)
    {
        if (mb_strlen($data) > static::MAX_LENGTH) {
            $data = mb_substr($data, 0, static::MAX_LENGTH);
        }

        return mb_strtolower($data);
    }

    /**
     * Get statistics of a row
     *
     * @return array
     */
    public function getStatistics()
    {
        $before = 'none';
        $after = 'none';
        $found = [];

        /** Distance between two same chars */
        $distance = [];

        for ($key = 0; $key < mb_strlen($this->row); $key++) {
            $char = mb_substr($this->row, $key, 1);
            if ($key == 0) {
                $found[$char]['after'][$after] = $after;
                $found[$char]['count'] = 1;

                $distance[$char]['min'] = $key;
            } else {
                if (!isset($found[$char])) {
                    $found[$char] = [];
                    $found[$char]['count'] = 1;

                    $distance[$char]['min'] = $key;
                } else {
                    $found[$char]['count']++;

                    $distance[$char]['max'] = $key;
                    $found[$char]['max-distance'] = $distance[$char]['max'] - $distance[$char]['min'];
                }

                $found[$char]['after'][$after] = $after;
                $found[$after]['before'][$char] = $char;

                if ($key == mb_strlen($this->row) - 1) {
                    $found[$char]['before'][$before] = $before;
                }
            }

            $found[$char]['value'] = $char;
            $after = $char;
        }

        return $found;
    }
}