<?php

namespace Lib;

use Lib\Session;

class Dump 
{
    public static $reset = false;
    public static $words = [];
    public static $correct = [];
    public static $dump = 'memory_dump';
    public static $input = 'memory_input';

    public static function reset()
    {
        self::$reset = true;
        Session::remove(self::$input);
        Session::remove(self::$dump);
    }

    public static function words($words = [])
    {
        self::$words = $words;
    }

    public static function correct($words = [])
    {
        self::$correct = $words;
    }

    public static function memory($rows = 16, $cols = 12, $header = "") 
    {
        if(empty(self::$words)) {
            self::$words = ["HACK", "PASSWORD", "SECURITY", "VAULT", "ACCESS", "DENIED", "TERMINAL", "ADMIN", "PASS"];
        }
        
        $words = array_map('strtoupper', array_merge(self::$words, self::$correct));
        
        if (self::$reset || !Session::has(self::$dump)) {
            Session::remove(self::$input);
            
            $totalChars = $rows * $cols * 2;
            $symbols = ['<', '>', '[', ']', '{', '}', '(', ')', '/', '\\', '|', '?', '!', '@', '#', '$', '%', '^', '&', '*', '-', '_', '+', '=', '.', ',', ':', ';'];
            
            $data = [];
            for ($i = 0; $i < $totalChars; $i++) {
                $data[$i] = $symbols[array_rand($symbols)];
            }

            $usedPositions = [];
            foreach ($words as $word) {
                $wordLen = strlen($word);
                $attempts = 0;
                
                do {
                    $attempts++;
                    // Find tilfældig position
                    $pos = rand(0, $totalChars - $wordLen);
                    
                    // --- LINJE TJEK ---
                    // Find ud af hvilken række ordet starter og slutter på
                    $startRow = floor($pos / $cols);
                    $endRow = floor(($pos + $wordLen - 1) / $cols);
                    
                    // Hvis de ikke er på samme række, er det en kollision (ordet knækker)
                    $collision = ($startRow !== $endRow);
                    
                    // Hvis ordet holder sig på én linje, tjek for overlap med andre ord
                    if (!$collision) {
                        for ($j = $pos; $j < $pos + $wordLen; $j++) {
                            if (isset($usedPositions[$j])) {
                                $collision = true;
                                break;
                            }
                        }
                    }
                    
                    // Sikkerhed: Stop hvis vi ikke kan finde plads efter 1000 forsøg
                    if ($attempts > 1000) break; 
                    
                } while ($collision);

                // Indsæt ordet
                for ($j = 0; $j < $wordLen; $j++) {
                    $data[$pos + $j] = $word[$j];
                    $usedPositions[$pos + $j] = true;
                }
            }
            Session::set(self::$dump, $data);
            self::$reset = false;
        } else {
            $data = Session::get(self::$dump);
        }

        $wrongGuesses = self::data();
        $dataString = implode('', $data);
        
        foreach ($wrongGuesses as $wrongWord) {
            $replacement = str_repeat('.', strlen($wrongWord));
            $dataString = str_ireplace($wrongWord, $replacement, $dataString);
        }
        
        $displayData = str_split($dataString);
        $hexBase = 0xF964; 

        $output = $header;
        for ($i = 0; $i < $rows; $i++) {
            $addrL = sprintf("0x%04X", $hexBase + ($i * $cols));
            $addrR = sprintf("0x%04X", $hexBase + (($rows + $i) * $cols));

            $charsLeft = implode('', array_slice($displayData, $i * $cols, $cols));
            $charsRight = implode('', array_slice($displayData, ($rows + $i) * $cols, $cols));

            $output .= "$addrL $charsLeft   $addrR $charsRight\n";
        }
        
        echo $output;
    }

    public static function data()
    {
        // Returnerer altid et array, så count() i din controller aldrig fejler
        $val = Session::get(self::$input);
        return is_array($val) ? $val : [];
    }

    public static function input($word) 
    {
        $input = strtoupper(trim($word));
        $correctOnes = array_map('strtoupper', self::$correct);

        if (in_array($input, $correctOnes)) {
            return true;
        } else {
            $wrongGuesses = self::data();
            if (!in_array($input, $wrongGuesses) && $input !== '') {
                $wrongGuesses[] = $input;
                Session::set(self::$input, $wrongGuesses);
            }
            return false;
        }
    }
}