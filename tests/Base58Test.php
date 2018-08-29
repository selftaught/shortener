<?php

require_once (__DIR__ . '/../include/autoload.php');
use PHPUnit\Framework\TestCase;

class Base58Test extends TestCase {

    public function test_charset() {
        $this->assertSame(Base58::$charset, '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ');
    }

    public function test_encode() {
        $this->assertSame(Base58::encode(92305802084020), 'HNHzDkJd');
        $this->assertSame(Base58::encode(90386657329   ), '3nHafcB' );
        $this->assertSame(Base58::encode(7273932953    ), 'c5LNR8'  );
        $this->assertSame(Base58::encode(52748023188   ), '2onapzE' );
        $this->assertSame(Base58::encode(83512252834   ), '3ceG8fb' );
    }

    public function test_decode() {
        $this->assertSame(Base58::decode('HNHzDkJd'), 92305802084020);
        $this->assertSame(Base58::decode('3nHafcB' ), 90386657329   );
        $this->assertSame(Base58::decode('c5LNR8'  ), 7273932953    );
        $this->assertSame(Base58::decode('2onapzE' ), 52748023188   );
        $this->assertSame(Base58::decode('3ceG8fb' ), 83512252834   );
    }
}
