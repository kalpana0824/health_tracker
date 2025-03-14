<?php
// Get BMI category based on BMI value
function getBmiCategory($bmi) {
    if ($bmi < 18.5) {
        return "Underweight";
    } elseif ($bmi >= 18.5 && $bmi < 25) {
        return "Normal weight";
    } elseif ($bmi >= 25 && $bmi < 30) {
        return "Overweight";
    } else {
        return "Obese";
    }
}
?>

