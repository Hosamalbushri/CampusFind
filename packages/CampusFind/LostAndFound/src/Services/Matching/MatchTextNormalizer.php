<?php

namespace CampusFind\LostAndFound\Services\Matching;

class MatchTextNormalizer
{
    public function normalize(?string $value): string
    {
        $value = mb_strtolower(trim((string) $value), 'UTF-8');
        $value = preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06ED}]/u', '', $value) ?? $value;
        $value = str_replace(['أ', 'إ', 'آ', 'ٱ', 'ى', 'ة'], ['ا', 'ا', 'ا', 'ا', 'ي', 'ه'], $value);
        $value = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $value) ?? $value;

        return trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
    }

    public function similarity(?string $left, ?string $right): ?float
    {
        $left = $this->normalize($left);
        $right = $this->normalize($right);

        if ($left === '' || $right === '') {
            return null;
        }

        $leftTokens = array_values(array_unique(explode(' ', $left)));
        $rightTokens = array_values(array_unique(explode(' ', $right)));
        $intersection = count(array_intersect($leftTokens, $rightTokens));
        $tokenDice = (2 * $intersection) / (count($leftTokens) + count($rightTokens));

        return max($tokenDice, $this->characterTrigramDice($left, $right));
    }

    private function characterTrigramDice(string $left, string $right): float
    {
        $leftTrigrams = $this->trigrams($left);
        $rightTrigrams = $this->trigrams($right);

        if ($leftTrigrams === [] || $rightTrigrams === []) {
            return $left === $right ? 1.0 : 0.0;
        }

        $intersection = count(array_intersect($leftTrigrams, $rightTrigrams));

        return (2 * $intersection) / (count($leftTrigrams) + count($rightTrigrams));
    }

    /** @return array<int, string> */
    private function trigrams(string $value): array
    {
        $characters = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $trigrams = [];

        for ($index = 0; $index <= count($characters) - 3; $index++) {
            $trigrams[] = implode('', array_slice($characters, $index, 3));
        }

        return array_values(array_unique($trigrams));
    }
}
