<?php

// *Add 'active' class to Active Route
if (!function_exists('setActive')) {
    function setActive($uri, $output = 'active')
    {
        if (is_array($uri)) {
            foreach ($uri as $u) {
                if (Route::is($u)) {
                    return $output;
                }
            }
        } else {
            if (Route::is($uri)) {
                return $output;
            }
        }
        
    }
}
