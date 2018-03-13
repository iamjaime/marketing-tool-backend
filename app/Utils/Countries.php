<?php

namespace App\Utils;

use Webpatser\Countries\Countries;

/**
 * CountryList
 *
 */
class Country extends Countries {

    //Overrides the original Countries package class function
    //because they have an error in it lol


    /**
     * Get the countries from the JSON file, if it hasn't already been loaded.
     *
     * @return array
     */
    protected function getCountries()
    {
        //Get the countries from the JSON file
        if (!$this->countries){
            $this->countries = json_decode(file_get_contents('vendor/webpatser/laravel-countries/src/Webpatser/Countries/Models/countries.json'), true);
        }

        //Return the countries
        return $this->countries;
    }


}
