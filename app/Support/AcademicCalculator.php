<?php

namespace App\Support;

class AcademicCalculator
{
    public static function gradePointFromMarks(float $marks): float
    {
        if ($marks >= 90) {
            return 4.0;
        }

        if ($marks >= 85) {
            return 3.7;
        }

        if ($marks >= 80) {
            return 3.3;
        }

        if ($marks >= 75) {
            return 3.0;
        }

        if ($marks >= 70) {
            return 2.7;
        }

        if ($marks >= 65) {
            return 2.3;
        }

        if ($marks >= 60) {
            return 2.0;
        }

        return 0.0;
    }

    public static function qualityPoints(float $gradePoint, int $creditHours): float
    {
        return round($gradePoint * $creditHours, 2);
    }

    public static function gpa(float $qualityPoints, int $creditHours): float
    {
        if ($creditHours <= 0) {
            return 0.0;
        }

        return round($qualityPoints / $creditHours, 2);
    }
}
