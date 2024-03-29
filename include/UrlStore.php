<?php

/**
 * Class UrlStore
 */
class UrlStore extends File
{

    protected $path;

    /**
     * Constructor
     *
     * @throws Exception if File::open which gets called from File's
     *    constructor, fails.
     *
     * @access public
     * @return void
     */
    public function __construct($path = null)
    {
        $this->path = (is_null($path) ? Config::URL_CSV_FILE_PATH : $path);
        parent::__construct($this->path);
    }

    /**
     * Returns a File object for the CSV.
     *
     * @return object
     */
    public function file()
    {
        return $this->file;
    }

    /**
     * Appends an entry to the csv file after creating
     * a shortened URI off of the row index.
     *
     * @access public
     * @param  $long_url
     * @return void
     */
    public function addEntry($long_url, $short_uri = null)
    {
        if (is_null($long_url)) {
            throw new Exception('long_url was null!');
        }

        $this->blockStore();

        $idx   = $this->getNextIndex();
        $url   = new Url($long_url);
        $short = (is_null($short_uri) || !is_string($short_uri) ? Base58::encode($idx) : $short_uri);

        if (!is_int($idx) || !$idx) {
            $this->unblockStore();
            throw new Exception('couldnt get the next available index!');
        }

        if (is_null($short) || !is_string($short) || !strlen($short)) {
            $this->unblockStore();
            throw new Exception('shortened url is missing or invalid!');
        }

        try {
            $this->appendLine(sprintf('%d,%s,%s,%s', $idx, $short, $long_url, date('Y-m-d H:i:s')));
            $this->unblockStore();
        } catch (Exception $e) {
            $this->unblockStore();
            exit($e->getMessage());
        }

        return $short;
    }

    /**
     * Checks to see if a given long url has a short
     * url entry in the csv file.
     *
     * @access public
     * @return bool
     */
    public function longUrlExists($long_url)
    {
        if (!is_string($long_url)
            || is_null($this->findMatchingLine("#,?$long_url,?#"))
        ) {
            return false;
        }
        return true;
    }

    /**
     * Sets a write blocking lock on the csv file.
     *
     * @access public
     * @return void
     */
    public function blockStore()
    {
        $this->setBlocking();
    }

    /**
     * Removes a write blocking lock from the csv file.
     *
     * @access public
     * @return void
     */
    public function unblockStore()
    {
        $this->setBlocking(false);
    }

    /**
     * Get's a row from the url csv file where $cond_vars are optional.
     *
     * @access public
     * @param  $cond_vars
     * @return void
     */
    public function getRow($cond_vars)
    {
        if (!is_array($cond_vars)) {
            throw new Exception("cond_vars param must be an array!");
        }

        $key  = null;
        $flag = false;

        foreach (['index', 'short_url', 'long_url'] as $i) {
            if (array_key_exists($i, $cond_vars)) {
                $flag = true;
                $key  = $i;
                break;
            }
        }

        if (!$flag) {
            throw new Exception("short_url, long_url, or index must be set in the param 'cond_vars'");
        }

        $line = $this->findMatchingLine('#,?' . $cond_vars[$key] . ',?#');

        if (is_null($line)) {
            throw new Exception("unable to find the $key!");
        }

        $boom = explode(',', $line);

        if (count($boom) != 4) {
            throw new Exception("matching line doesn't have the expected number of values!");
        }

        $row = array(
            'index'     => $boom[0],
            'short_url' => sprintf('%s/%s', Config::BASE_URL, $boom[1]),
            'long_url'  => $boom[2],
            'datetime'  => $boom[3]
        );

        return $row;
    }

    /**
     * Get's the next available id based on the entries in the url csv.
     *
     * @access public
     * @throws Exception
     * @return int
     */
    public function getNextIndex()
    {
        $line = $this->getLastLine();

        if (!strlen($line)) {
            return 1;
        }

        $boom = explode(',', $line);

        if (count($boom) != 4) {
            throw new Exception("invalid number of columns");
        }

        return (int)$boom[0] + 1;
    }
}
