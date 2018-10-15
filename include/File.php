<?php

/**
 * Class File
 */
class File
{

    protected $block;
    protected $path;
    protected $fptr;
    protected $open;
    protected $locked;
    protected $mode;

    /**
     * Constructor
     *
     * @access public
     * @throws Exception from unabled called to $this->open()
     *
     * @param string $path  Path of file to open.
     * @param string $mode  Mode to open the file in.
     * @param bool   $block Path of file to open.
     *
     * @return void
     */
    public function __construct($path = null, $mode = 'a+', $block = false)
    {
        if (!is_null($path)) {
            $this->open($path, $mode, $block);
        }
    }

    /**
     * Destructor
     *
     * @access public
     *
     * @return void
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Opens a specified file.
     *
     * @access public
     * @throws Exception if a resource type value wasn't returned from fopen()
     *     or if a lock couldn't be acquired on the file resource.
     *
     * @param string $path  Path of file to open.
     * @param string $mode  Mode to open the file in.
     * @param bool   $block Acquire an exclusive write lock that blocks.
     *
     * @return bool   $is_open
     */
    public function open($path = null, $mode = 'a+', $block = false)
    {
        $this->block = $block;
        $this->mode  = $mode;

        if ($this->is_open()) {
            return $this->open;
        }

        if (!is_null($path) && is_null($this->path)) {
            $this->path = $path;
        }

        $this->fptr   = fopen($this->path, $this->mode);
        $this->locked = flock($this->fptr, $this->block ? LOCK_EX : LOCK_EX | LOCK_NB);

        if (!is_resource($this->fptr) || !$this->locked) {
            $this->open = false;
            throw new Exception("could not acquire file resource and or lock.\n");
        }

        $this->open = true;
        return $this->open;
    }

    /**
     * Closes the file and releases the file lock
     * if there is one.
     *
     * @access public
     * @return void
     */
    public function close()
    {
        if (is_resource($this->fptr)) {
            if ($this->locked) {
                flock($this->fptr, LOCK_UN);
                $this->locked = false;
            }

            $this->open = false;

            fflush($this->fptr);
            fclose($this->fptr);
        }
    }

    /**
     * Sets / unsets write blocking on the file.
     *
     * @access public
     * @param  bool $block (default = true)
     * @return void
     */
    public function set_blocking($block = true)
    {
        if ($this->is_open()) {
            $this->close();
        }

        if (is_resource($this->fptr)) {
            $this->block = (bool)$block;
            flock($this->fptr, ($block ? LOCK_EX : LOCK_EX | LOCK_NB));
        }
    }

    /**
     * Closes and then removes the file.
     *
     * @access public
     * @return void
     */
    public function rm()
    {
        if ($this->is_open()) {
            $this->close();
        }

        if (file_exists($this->path)) {
            unlink($this->path);
        }
    }

    /**
     * Returns whether the file is open or not.
     *
     * @return bool $is_open
     */
    public function is_open()
    {
        return is_resource($this->fptr) && $this->open;
    }

    /**
     * Returns whether the file is locked or not.
     *
     * @access public
     * @return bool $this->locked
     */
    public function is_locked()
    {
        return $this->locked;
    }

    /**
     * Returns whether the file is blocking or not.
     *
     * @access public
     * @return bool $this->locked
     */
    public function is_blocked()
    {
        return $this->block;
    }

    /**
     * Returns the contents of a file as an array
     * of newline delimited byte strings.
     *
     * @access public
     * @throws Exception from unabled called to $this->open()
     * @param  int $line_count specified number of lines to return
     * @return array $arr
     */
    public function as_array($line_count = null)
    {
        if (!is_resource($this->fptr)) {
            $this->open();
        }

        rewind($this->fptr);

        $lines = [];
        $count = 0;

        while (!feof($this->fptr)) {
            if (!is_null($line_count) && $count >= $line_count) {
                rewind($this->fptr);
                break;
            }

            $line = trim(fgets($this->fptr));

            if (preg_match('/^\d+$/', $line)) {
                $line = (int)$line;
            }

            if (strlen($line)) {
                $lines[] = $line;
            }

            ++$count;
        }

        return $lines;
    }

    /**
     * Returns the contents of a file as a string
     * and optionally, a string of a specified length.
     *
     * @throws Exception from unabled called to $this->open()
     * @access public
     * @param  int $byte_count specified string length to return
     * @return string $str
     */
    public function as_string($byte_count = null)
    {
        if (!is_resource($this->fptr)) {
            $this->open();
        }

        rewind($this->fptr);

        $str = '';
        while (!feof($this->fptr)) {
            $str .= str_replace('\n', '', trim(fgets($this->fptr)));
            $len  = strlen($str);

            if (!is_null($byte_count) && $len > $byte_count) {
                if ($len > $byte_count) {
                    $str = substr($str, 0, $byte_count);
                    break;
                } else {
                    break;
                }
            }
        }

        return $str;
    }

    /**
     * Returns the last line of the file.
     *
     * @access public
     * @throws Exception from unabled called to $this->open()
     * @return string $last_line
     */
    public function get_last_line()
    {
        if (!is_resource($this->fptr)) {
            $this->open();
        }

        rewind($this->fptr);

        $last_line = '';
        while (!feof($this->fptr)) {
            $line = trim(fgets($this->fptr));
            if (strlen($line)) {
                if (preg_match("/^\d+$/", $line)) {
                    $line = (int)$line;
                }
                $last_line = $line;
            }
        }

        return $last_line;
    }


    /**
     * Appends a given line to the file.
     *
     * @access public
     * @throws Exception if fputs returns FALSE because it failed to write.
     * @param  string $line Data to write to file.
     * @return void
     */
    public function append_line($line)
    {
        if (!is_resource($this->fptr)) {
            $this->open();
        }

        $line .= PHP_EOL;
        $ret = fputs($this->fptr, $line);

        if ($ret === false) {
            throw new Exception('fputs returned FALSE');
        }
    }

    /**
     * Finds a line that matches the $pattern param.
     *
     * @access public
     * @param  string $pattern Regex pattern to match
     * @return string
     */
    public function find_matching_line($pattern)
    {
        if (!is_resource($this->fptr)) {
            $this->open();
        }

        rewind($this->fptr);
        
        while (!feof($this->fptr)) {
            $line = trim(fgets($this->fptr));
            if (preg_match($pattern, $line)) {
                return $line;
            }
        }
        return null;
    }

    /**
     * Truncates contents from file.
     *
     * @param  bool $close_after_truncate (default = false)
     * @return void
     */
    public function truncate($close_after_truncate = false)
    {
        if (!is_resource($this->file())) {
            $this->open();
        }

        $this->set_blocking();
        ftruncate($this->file, 0);
        $this->set_blocking(false);

        if ($close_after_truncate) {
            $this->close();
        }
    }
}
