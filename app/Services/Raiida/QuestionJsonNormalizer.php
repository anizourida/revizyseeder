<?php

namespace App\Services\Raiida;

class QuestionJsonNormalizer
{
    public function normalize(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if ($this->isAssociative($value)) {
            ksort($value);
        }

        foreach ($value as $key => $item) {
            $value[$key] = $this->normalize($item);
        }

        return $value;
    }

    private function isAssociative(array $value): bool
    {
        if ($value === []) {
            return false;
        }

        return array_keys($value) !== range(0, count($value) - 1);
    }
}
