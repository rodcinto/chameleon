<?php
namespace App\Service;

use Psr\Log\LoggerInterface;

class Similarity
{
    /**
     * @param string $candidateText
     * @param string $requestText
     * @param string $logMessage
     * @return float
     */
    public function compareTexts(
        string $candidateText,
        string $requestText
    ):float
    {
        if ('' === $candidateText && '' === $requestText) {
            return 100;
        }

        $similarityResult = 0;

        similar_text(
            $requestText,
            $candidateText,
            $similarityResult
        );

        return $similarityResult;
    }
}
