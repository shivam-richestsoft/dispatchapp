<?php

use App\Models\FinanceData;

if (! function_exists('getCoinsToDiamond')) {
    function getCoinsToDiamond($coins)
    { 
       $conversion_rate = FinanceData::value('diamond_per_100_coin');
       return ($conversion_rate/100)*$coins; 
    }
   }

   if (! function_exists('getDiamondsToIncome')) {
    function getDiamondsToIncome($diamonds)
    { 
       $conversion_rate = FinanceData::value('income_per_100_diamonds');
       return ($conversion_rate/100)*$diamonds; 
    }
   }

   if (! function_exists('getCoinsToIncome')) {
      function getCoinsToIncome($coins)
      { 
         $coin_rate = FinanceData::value('diamond_per_100_coin');
         $diamond_rate = FinanceData::value('income_per_100_diamonds');
         return ($diamond_rate/100)*(($coin_rate/100)*$coins); 
      }
     }

     if (! function_exists('getIncomeByRating')) {
      function getIncomeByRating($coins,$rating)
      { 
         $coin_rate = FinanceData::value('diamond_per_100_coin');
         $diamond_rate = FinanceData::value('income_per_100_diamonds');
         $TotalIncome = ($diamond_rate/100)*(($coin_rate/100)*$coins); 
         switch ($rating) {
            case "A":
               $income = (20/100)*$TotalIncome;
               return $income;
              break;
            case "B":
               $income = (15/100)*$TotalIncome;
               return $income;
              break;
            case "C":
               $income = (10/100)*$TotalIncome;
               return $income;
              break;
            default:
              $income = 0;
              return $income;
          }
         
      }
     }

