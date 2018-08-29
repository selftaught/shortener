<?php

require_once (__DIR__ . '/../include/autoload.php');
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase {

    public function append_test_data($file) {
        $this->assertInstanceOf('File', $file);

        for ($i = 0; $i <= 20; ++$i) {
            try {
                $file->append_line($i);
            }
            catch(Exception $e) {
                print $e->getMessage();
                return false;
            }
        }

        return true;
    }

    private function get_temp_file_path() {
        return sprintf("/tmp/test-%d-%d", time(), getmypid());
    }

    public function test_is_open() {
        $file = new File($this->get_temp_file_path());
        $this->assertTrue($file->is_open());
        $file->rm();
    }

    public function test_is_locked() {
        $file = new File($this->get_temp_file_path());
        $this->assertTrue($file->is_locked());
        $file->rm();
    }

    public function test_as_array() {
        $file = new File($this->get_temp_file_path());
        $this->append_test_data($file);
        $this->assertSame([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20], $file->as_array());
        $this->assertSame([0,1,2,3,4], $file->as_array(5));
        $file->rm();
    }

    public function test_as_string() {
        $file = new File($this->get_temp_file_path());
        $this->append_test_data($file);
        $this->assertSame('01234567891011121314151617181920', $file->as_string());
        $this->assertSame('01234', $file->as_string(5));
        $file->rm();
    }

    public function test_get_last_line() {
        $file = new File();

        $this->assertTrue($file->open($this->get_temp_file_path(), 'a+', true));
        $this->assertTrue($this->append_test_data($file));

        try {
            $last_line = $file->get_last_line();
            $this->assertSame(20, $last_line);
            $file->rm();
        }
		catch (Exception $e) {
            $file->rm();
            die($e->getMessage());
        }

        $file->rm();
    }
}
