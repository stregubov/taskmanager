<?php
/**
 * User: Svyatoslav Tregubov
 * DateTime: 06.07.2022, 22:42
 * Company: Asteq
 */

namespace App\Services;

class TimeConverterService
{
    private const TEMPLATE = "/(?:\d+[дчмДЧМ]+)/im";

    public function convertToHours(string $timeExpression): float
    {
        if (!$this->isTimeValid($timeExpression)) {
            throw new \Exception('Невалидное время');
        }

        $hours = 0;

        $parts = $this->resolveExpressionParts($timeExpression);

        foreach ($parts as $part) {
            foreach ($part as $value) {
                $hours += $this->partToHours($value);
            }
        }

        return $hours;
    }

    private function isTimeValid(string $timeExpression): bool
    {
        return preg_match_all(self::TEMPLATE, $timeExpression);
    }

    private function resolveExpressionParts(string $timeExpression): array
    {
        $matches = [];
        preg_match_all(self::TEMPLATE, $timeExpression, $matches);
        return $matches;
    }

    private function partToHours(string $letter): float
    {
        $letter = strtolower($letter);
        $multiplier = 1;

        $_letter = preg_replace('/\d+/', '', $letter);
        $value = str_replace($_letter, '', $letter);

        switch ($_letter) {
            case 'д':
                $multiplier = 8;
                break;
            case 'ч':
                $multiplier = 1;
                break;
            case 'м':
                $multiplier = 1 / 60;
                break;
        }

        return round($value * $multiplier, 2);
    }
}