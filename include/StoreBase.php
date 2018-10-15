<?php

namespace Shortener {
    abstract class StoreBase
    {
        /**
         * Appends an entry to the csv file after creating
         * a shortened URI off of the row index.
         *
         * @access public
         * @param  $long_url
         * @return void
         */
        abstract public function addEntry($long_url, $short_uri = null);

        /**
         * Checks to see if a given long url has a short
         * url entry in the csv file.
         *
         * @access public
         * @return bool
         */
        abstract public function longUrlExists($long_url);

        /**
         * Get's a row from the url csv file where $cond_vars are optional.
         *
         * @access public
         * @param  $cond_vars
         * @return void
         */
        abstract public function getRow($cond_vars);

        /**
         * Get's the next available id based on the entries in the url csv.
         *
         * @access public
         * @throws Exception
         * @return int
         */
        abstract public function getNextIndex();
    }
}
