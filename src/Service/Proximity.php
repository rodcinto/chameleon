<?php
namespace App\Service;

use App\Service\Similarity;

class Proximity
{
    const DEFAULT_CUT_FACTOR = 90;

    /**
     * @var Similarity
     */
    private $similarity;

    /**
     * @var integer
     */
    private $minimumScore = 0;

    /**
     * @var array
     */
    private $stack = [];

    /**
     * @param Similarity $similarity
     * @return void
     */
    public function setSimilarity(Similarity $similarity):void
    {
        $this->similarity = $similarity;
    }

    /**
     * @param string $referenceText
     * @param string $targetText
     * @param integer $cutFactor
     * @return float
     */
    public function addComparison(
        string $referenceText,
        string $targetText,
        int $cutFactor = self::DEFAULT_CUT_FACTOR
    ):float
    {
        $comparison = $this->similarity->compareTexts(
            $this->normalize($referenceText),
            $this->normalize($targetText)
        );
        $result = $comparison >= $cutFactor ? $comparison : 0;
        $this->stack[] = $result;

        return $result;
    }

    /**
     * @return float
     */
    public function calculateAverageScore():float
    {
        $stackSum = array_reduce($this->stack, function($carry, $score) {
            return $carry + $score;
        });

        $score = $stackSum / count($this->stack);

        // Reset the stack.
        $this->stack = [];

        return $score;
    }

    /**
     * @param string $text
     * @return string
     */
    private function normalize(string $text):string
    {
        return trim(
            str_replace(["\r", "\n"], '', $text)
        );
    }
}
