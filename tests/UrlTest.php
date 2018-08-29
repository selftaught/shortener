<?php

require_once (__DIR__ . '/../include/autoload.php');
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase {

    protected $url;
    protected $long_url;
    protected $short_url;

    public function setUp() {
        $this->url       = new Url();
        $this->long_url  = 'https://sucuri.net';
        $this->short_url = 'https://rip.sh/2vF';
    }

    public function test_validate_valid_urls() {
        $valid_urls = ['https://www.sucuri.net', 'https://sucuri.net'];
        foreach ($valid_urls as $valid_url) {
            $this->assertTrue($this->url->validate_url($valid_url));
        }
    }

    public function test_validate_invalid_urls() {
        $invalid_urls = ['dead', 'beef', 'localhost', '/etc/passwd', 'htps://0x00sec.org'];
        foreach ($invalid_urls as $invalid_url) {
            try {
                $this->url->validate_url($invalid_url);
            }
            catch(Exception $e) {
                $this->assertEquals($e->getMessage(), 'url is invalid!');
            }
        }
    }

    public function test_set_short() {
        $this->url->set_short($this->short_url);
        $this->assertSame(
            $this->short_url,
            $this->url->shorten($this->long_url)
        );
    }

    public function test_set_long() {
        $this->url->set_long($this->long_url);
        $this->assertSame(
            $this->long_url,
            $this->url->get_long_url($this->short_url)
        );
    }
}
