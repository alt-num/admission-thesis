<?php

if (!function_exists('generateRandomPassword')) {
    /**
     * Generate a random 5-character password using uppercase letters and numbers.
     * 
     * @return string
     */
    function generateRandomPassword(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';

        for ($i = 0; $i < 5; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $password;
    }
}

