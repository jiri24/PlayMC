<?php

class Random {

    public function __construct() {
        
    }

    // Rovnoměrné pravděpodobnostní rozdělení
    public function uniformDistribution($min, $max) {
        return mt_rand($min, $max);
    }

    // Exponenciální rozdělení
    public function exponentialDistribution($tau) {
        $lambda = 1;
        $r = mt_rand(0, PHP_INT_MAX) / PHP_INT_MAX;
        return (int) (- log(1 - (1 - exp(- $lambda * $tau)) * $r) / $lambda);
    }

    public function nrand($mean, $sd) {
        $x = mt_rand() / mt_getrandmax();
        $y = mt_rand() / mt_getrandmax();
        return sqrt(-2 * log($x)) * cos(2 * pi() * $y) * $sd + $mean;
    }

    // Alternativní rozdělení
    public function bernoulliDistribution($i){
        $r = mt_rand(0, mt_getrandmax()) / mt_getrandmax();
        if ($r > $i){
            return 1;
        } else {
            return 0;
        }
    }
}
