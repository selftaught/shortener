<?php

require_once (__DIR__ . '/../include/autoload.php');
use PHPUnit\Framework\TestCase;


class UrlStoreTest extends TestCase {

	protected $long_url;
	protected $url_store;
	protected $url_store_file;

	public function setUp() {
		$this->temp_csv       = new File();
        $this->long_url       = 'https://sucuri.net';
        $this->url_store_file = sprintf("/tmp/unittest-urlshorttest-%d.csv", getmypid());
		$this->url_store      = new UrlStore($this->url_store_file);
	}

	public function tearDown() {
		$this->url_store->set_blocking(false);
        $this->url_store->rm();
	}

    public function test_add_entry() {
		$this->url_store->add_entry($this->long_url);
        $line = $this->url_store->get_last_line();
        $this->assertFalse(is_null($line));
        $this->assertTrue(is_string($line));
        $boom = explode(',', $line);
        $this->assertCount(4, $boom);
        $this->assertEquals($this->long_url, $boom[2]);
	}	

	public function test_long_url_exists() {
        $this->url_store->add_entry($this->long_url);
        $this->assertTrue($this->url_store->long_url_exists($this->long_url));
	}

    public function test_get_row() {
        $long_url = sprintf('unit-test.%d', getmypid());
        $this->url_store->add_entry($long_url);
        $row = $this->url_store->get_row(['long_url' => $long_url]);
        $this->assertTrue(is_array($row));
	}

	public function test_get_next_index() {
        $index = $this->url_store->get_next_index();
        $this->url_store->add_entry($this->long_url);
        $next_index = $this->url_store->get_next_index();
        $this->assertEquals($index + 1, $next_index);
	}
}

