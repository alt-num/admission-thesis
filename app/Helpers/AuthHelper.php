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

if (!function_exists('generateApplicantPassword')) {
    /**
     * Generate a deterministic password for applicant users based on their reference number.
     * 
     * Format: bor_YY_NNNNN
     * Where:
     * - "bor" is a fixed lowercase prefix
     * - "YY" is the last two digits of the current year
     * - "NNNNN" is the 5-digit zero-padded applicant reference number
     * 
     * Example: Reference number "BOR-2500002" generates password "bor_25_00002"
     * 
     * @param string $appRefNo The applicant reference number (format: CITY_CODE-YYNNNNN)
     * @return string The generated password
     */
    function generateApplicantPassword(string $appRefNo): string
    {
        // Extract the last 7 characters (YY + 5-digit number) from app_ref_no
        // Format is like: BOR-2500002, so we want "2500002"
        $lastSevenChars = substr($appRefNo, -7);
        
        // Extract year (first 2 chars): "25"
        $yearShort = substr($lastSevenChars, 0, 2);
        
        // Extract 5-digit number (last 5 chars): "00002"
        $refNumber = substr($lastSevenChars, 2, 5);
        
        // Format as: bor_YY_NNNNN
        return sprintf('bor_%s_%s', $yearShort, $refNumber);
    }
}

