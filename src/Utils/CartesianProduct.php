<?php
/**
 * This code is derived from https://github.com/bpolaszek/cartesian-product
 * Usually, i would fork the repository and specify the forked github repository in composer.json.
 * There are multiple reasons to do that.
 * 1: License restrictions, 2: We don't need to maintain a 3rd party library.
 * 3: Composer will be able to resolve any dependencies if any other library requires the one we are changing.
 *
 * But in a current situation i am changing the core behavior which will break any library which depends on
 * Original CartesianProduct. And for simplicity sake i just copied and modified the source code.
 * On the actual project if this is the only way, i would also split it into 2 commits to show the diff between
 * The original and modified code (the same way i do the rewrites in Magento if they are necessary).
 */
namespace App\Utils;

use Countable;
use IteratorAggregate;

/**
 * Class CartesianProduct
 * @package App\Utils
 */
class CartesianProduct implements IteratorAggregate, Countable
{

    /**
     * @var array
     */
    private $set = [];

    /**
     * @var bool
     */
    private $isRecursiveStep = false;

    /**
     * @var int
     */
    private $count;

    /**
     * CartesianProduct constructor.
     * @param array $set - A multidimensionnal array.
     */
    public function __construct(array $set)
    {
        $this->set = $set;
    }

    /**
     * @return \Generator
     */
    public function getIterator()
    {
        if (!empty($this->set)) {
            $keys = array_keys($this->set);
            $key = end($keys);
            $subset = array_pop($this->set);
            $this->validate($subset, $key);
            foreach (self::subset($this->set) as $product) {
                foreach ($subset as $value) {
                    if ($value instanceof \Closure) {
                        yield $product . $value($product);
                    } else {
                        yield $product . $value;
                    }
                }
            }
        } else {
            if (true === $this->isRecursiveStep) {
                yield '';
            }
        }
    }

    /**
     * @param $subset
     * @param $key
     */
    private function validate($subset, $key)
    {
        if (!is_array($subset) || empty($subset)) {
            throw new \InvalidArgumentException(sprintf('Key "%s" should return a non-empty array', $key));
        }
    }

    /**
     * @param array $subset
     * @return CartesianProduct
     */
    private static function subset(array $subset)
    {
        $product = new self($subset);
        $product->isRecursiveStep = true;

        return $product;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return iterator_to_array($this);
    }

    /**
     * @return int
     */
    public function count()
    {
        if (null === $this->count) {
            $this->count = (int) array_product(array_map(function ($subset, $key) {
                $this->validate($subset, $key);
                return count($subset);
            }, $this->set, array_keys($this->set)));
        }

        return $this->count;
    }
}