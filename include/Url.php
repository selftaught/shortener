<?php

use Respect\Validation\Validator as v;

/**
 * Url class that takes a short or long url
 * for input and provides helper methods to
 * get both the long and short version of the
 * given url.
 */
class Url
{

    protected $long_url;
    protected $short_url;
    protected $store;

    /**
     * Construct the Url object and set default
     * member variable values.
     */
    public function __construct()
    {
        $this->long_url  = null;
        $this->short_url = null;
        $this->store     = null;
    }

    /**
     * Set the long url.
     *
     * @access public
     * @throws Exception if the given $url doesn't match the url validation regex.
     * @param  string $long_url long url value
     * @return void
     */
    public function setLong($long_url)
    {
        $this->validateUrl($long_url);
        $this->long_url = $long_url;
    }

    /**
     * Set the short url.
     *
     * @access public
     * @throws Exception if the given $url doesn't match the url validation regex
     *      via a call to $this->validateUrl($url).
     * @param  string $short_url short url value
     * @return void
     */
    public function setShort($short_url)
    {
        $this->validateUrl($short_url);
        $this->short_url = $short_url;
    }

    /**
     * Takes a long url, generates, and then
     * returns a shortened url.
     *
     * @access public
     * @throws Exception if the given $long url isn't set or is a zero-len str
     * @param  string $long long url to shorten
     * @return string $short_url  shortened url
     */
    public function createShort($long)
    {
        if (is_null($long) || !strlen($long)) {
            throw new Exception('missing long url!');
        }

        $this->validateUrl($long);

        $url_store = $this->getUrlStore();
        if ($url_store->longUrlExists($long)) {
            $row = $url_store->getRow(array('long_url' => $long));

            if (is_array($row)) {
                $this->short_url = $row['short_url'];
            }
        }

        if (!isset($this->short_url)
            || !strlen($this->short_url)
        ) {
            $this->short_url = sprintf('%s/%s', Config::BASE_URL, $url_store->addEntry($long));
        }

        return $this->short_url;
    }

    /**
     * Check the validity of a given url.
     *
     * @access public
     * @throws Exception if the given $url doesn't match the url validation regex.
     * @param  string $url url to validate
     * @return true
     */
    public function validateUrl($url)
    {
        if (!v::url()->validate($url)) {
            throw new Exception('url is invalid!');
        }
        return true;
    }

    /**
     * Returns an instantiated UrlStore object
     * which gets store in $this->store and returned
     * on subsequent calls to this function.
     *
     * @return object $url_store
     */
    protected function getUrlStore()
    {
        if (is_null($this->store)
            || !is_object($this->store)
        ) {
            $this->store = new UrlStore();
        }

        return $this->store;
    }

    /**
     * Get the corresponding long url for a given short url.
     *
     * @access public
     * @throws Exception if neither a long or short url are set, or a throw
     *      will occur if a long url couldn't be found for the given short url.
     * @param  string $short_url corresponding short url <-> long url value
     * @return string $long_url
     */
    public function get_long_url($short_url)
    {
        $lu_is_null = is_null($this->long_url);
        $su_is_null = is_null($this->short_url);

        if ($su_is_null && isset($short_url)) {
            $this->validateUrl($short_url);
            $su_is_null = false;
            $this->short_url = $short_url;
        }

        if ($lu_is_null) {
            $throw_msg = 'neither a long or a short url was set!';
            if (!$su_is_null) {
                $url_store = $this->getUrlStore();
                $row = $url_store->getRow(array('short_url' => $this->short_url));

                if (is_array($row)) {
                    $this->long_url = $row['long_url'];
                    return $this->long_url;
                }

                $throw_msg = 'a long url could not be found for the shortened url!';
            }

            throw new Exception($throw_msg);
        }

        return $this->long_url;
    }

    /**
     * Shorten the long url and return the value.
     *
     * @access public
     * @throws Exception if both the short and long urls are not set.
     * @param  string $long_url corresponding long url <-> short url value
     * @return string $short_url
     */
    public function shorten($long_url)
    {
        $lu_is_null = is_null($this->long_url);
        $su_is_null = is_null($this->short_url);

        if ($lu_is_null && !is_null($long_url)) {
            $this->validateUrl($long_url);
            $this->long_url = $long_url;
            $lu_is_null = false;
        }

        if ($su_is_null) {
            if ($lu_is_null) {
                throw new Exception('a long url was not set!');
            }
        }

        $this->short_url = $this->createShort($long_url);

        return $this->short_url;
    }
}
