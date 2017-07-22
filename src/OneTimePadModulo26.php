<?php
/**
 * The One Time Pad, Modulo 26.
 *
 *This class uses the modular arithmetic to perform one time pad encryption.  It is intended to be used for
 *educational purposes only, and to demonstrate how one time pad can be done using PHP.
 *
 * @author Michael Hall <mike@unrivaledcreations.com>
 * @license UNLICENSE
 * @version 1.0
 */

namespace unrivaled\OneTimePad\OneTimePadModulo26;

/**
 * Class OneTimePadModulo26
 */
class OneTimePadModulo26
{

    /**
     * function encrypt($cipherKey : string, $plainText : string)
     *
     * The encrypt(...) function uses modular arithmetic to encrypt a message.  It requires the cipher keys and the
     * plain text message as input parameters.
     *
     * @param string $cipherKey The one time pad cipher key.
     * @param string $plainText The plain text message to be encrypted and transmitted.
     * @return bool|string Returns the one time pad cipher text, or **false** on failure.
     */
    public function encrypt($cipherKey, $plainText)
    {
        // The one-time pad only works if we use a purely random key sequence that never repeats.
        // The cipher key must also be at least as long as the plain text message.
        if (strlen($cipherKey) < strlen($plainText)) {
            return false;
        }

        // Let's build a cipher text message safe for transmission.  By convention, we group in sets of 5 characters.
        $cipherText = '';
        for ($letter = 0; $letter < strlen($plainText); $letter++) {
            /*
             * ASCII letter 'A' has a value of 65, but we must work within the set of numbers {0, 1, 2, ..., 25}.
             * So, we subtract 65 from the ASCII value of the character in question. Once we finish the
             * modular arithmetic, we'll then simply add that 65 right back in to produce a printable character.
             */
            $plainTextCharacterAscii = ord($plainText[$letter]);
            $cipherKeyCharacterAscii = ord($cipherKey[$letter]);

            $plainTextCharacterInteger = $plainTextCharacterAscii - 65; // {0, 1, 2, ..., 25}
            $cipherKeyCharacterInteger = $cipherKeyCharacterAscii - 65; // {0, 1, 2, ..., 25}

            $oneTimePad = ($plainTextCharacterInteger + $cipherKeyCharacterInteger) % 26; // {0, 1, 2, ..., 25}

            $cipherTextCharacterAscii = $oneTimePad + 65;
            $cipherText .= chr($cipherTextCharacterAscii);

            /*
             * See all that code?  It's in "long form" for understandability.  It could be reduced to:
             * $cipherText .= chr((((ord($plainText[$letter]) - 65) + (ord($cipherKey[$letter]) - 65)) % 26) + 65);
             */
        }
        return ($cipherText);
    }

    /**
     * function decrypt($cipherKey : string, $cipherText : string)
     *
     * The decrypt(...) function uses modular arithmetic to decrypt a message.  It requires the cipher keys and the
     * encrypted cipher text message as input parameters.
     *
     * @param string $cipherKey The one time pad cipher key with which the original message was encrypted.
     * @param string $cipherText The one time pad cipher text to be decrypted.
     * @return bool|string Returns the original, decrypted plain text message, or **false** on failure.
     */
    public function decrypt($cipherKey, $cipherText)
    {
        // The one-time pad only works if we use a purely random key sequence that never repeats.
        // The cipher key must also be at least as long as the plain text message.
        if (strlen($cipherKey) < strlen($cipherText)) {
            return false;
        }

        // Let's decipher the message.  By convention, we group in sets of 5 characters.
        $plainText = '';
        for ($letter = 0; $letter < strlen($cipherText); $letter++) {
            /*
             * ASCII letter 'A' has a value of 65, but we must work within the set of numbers {0, 1, 2, ..., 25}.
             * So, we subtract 65 from the ASCII value of the character in question. Once we finish the
             * modular arithmetic, we'll then simply add that 65 right back in to produce a printable character.
             *
             * See the "+ 26"?  That's because we must have a value {>= 0} to get a positive result {0, 1, 2, ..., 25}.
             * We could decipher a value as low as {-25} so we add 26 to make sure we use {>= 0} in all calculations.
             * Since we are adding +26 to the dividend and since 26 is a multiple of the modulus, the value of the
             * remainder will be unaffected; yet, it guarantees a {>= 0} dividend in the modulo calculation.
             * (The quotient would be affected by this addition; but the quotient is dropped in modular arithmetic.)
             */

            $cipherTextCharacterAscii = ord($cipherText[$letter]);
            $cipherKeyCharacterAscii = ord($cipherKey[$letter]);

            $cipherTextCharacterInteger = $cipherTextCharacterAscii - 65; // {0, 1, 2, ..., 25}
            $cipherKeyCharacterInteger = $cipherKeyCharacterAscii - 65; // {0, 1, 2, ..., 25}

            $oneTimePad = ($cipherTextCharacterInteger - $cipherKeyCharacterInteger + 26) % 26;

            $plainTextCharacterAscii = $oneTimePad + 65;
            $plainText .= chr($plainTextCharacterAscii);
            /*
             * See all that code up above?  It's "long form" for understandability.  It could be reduced to:
             * $plainText .= chr((((ord($cipherText[$letter]) - 65) - (ord($cipherKey[$letter]) - 65) + 26) % 26) + 65);
             */
        }
        return ($plainText);
    }

    /**
     * tty (TeleType).
     *
     * This function receives a string and returns that same string, formatted into groups of 5 characters each, to
     * make you feel like a REAL spy.
     *
     * @param string $text
     * @return string Returns a string, broken up into groups of 5 characters each.
     */
    public function tty($text)
    {
        /*
         * Render $text in sets of 5 characters.
         */
        $spacedMessage = '';
        for ($letter = 0; $letter < strlen($text); $letter++) {
            $spacedMessage .= $text[$letter];
            if (!(($letter + 1) % 5)) {
                $spacedMessage .= ' ';
            }
        }
        return $spacedMessage;
    }

    /**
     * @return string Returns a printable string containing the Vigenere table, also known as the Tabula Recta.
     */
    public function get_vigenere_table()
    {
        static $table = '';
        if (!strlen($table)) {
            // Show the plain text characters across the top.
            $table .= '  '; // Skip two spaces at the beginning...
            for ($letter = 0; $letter < 26; $letter++) {
                $table .= (chr($letter + 65) . ' ');
            }
            $table .= "\n"; // ...and show a newline at the end.

            // How show each row; one row for each letter of key characters.
            for ($letter = 0; $letter < 26; $letter++) {
                $table .= chr($letter + 65) . ' ';  // Consume two characters - first the key, second a space.
                for ($cipher = 0; $cipher < 26; $cipher++) {
                    $table .= chr((($letter + $cipher) % 26) + 65) . ' ';
                }
                $table .= "\n"; // ...and show a newline at the end.
            }
        }
        return $table;
    }

    public function get_tabula_recta()
    {
        return $this->get_vigenere_table();
    }
}
