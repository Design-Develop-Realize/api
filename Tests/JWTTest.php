<?php

use API\Library\JWT;

class JWTTest extends PHPUnit\Framework\TestCase {
    private $JWT;

    function testEncodeDecode() {
        $msg = $this->JWT->encode('abc', 'my_key');
        $this->assertEquals($this->JWT->decode($msg, 'my_key'), 'abc');
    }
    function testDecodeFromPython() {
        $msg = 'eyJhbGciOiAiSFMyNTYiLCAidHlwIjogIkpXVCJ9.Iio6aHR0cDovL2FwcGxpY2F0aW9uL2NsaWNreT9ibGFoPTEuMjMmZi5vbz00NTYgQUMwMDAgMTIzIg.E_U8X2YpMT5K1cEiT_3-IvBYfrdIFIeVYeOqre_Z5Cg';
        $this->assertEquals(
            $this->JWT->decode($msg, 'my_key'),
            '*:http://application/clicky?blah=1.23&f.oo=456 AC000 123'
        );
    }
    function testUrlSafeCharacters() {
        $encoded = $this->JWT->encode('f?', 'a');
        $this->assertEquals('f?', $this->JWT->decode($encoded, 'a'));
    }
    function testMalformedUtf8StringsFail() {
        $this->expectException('DomainException');
        $this->JWT->encode(pack('c', 128), 'a');
    }
    function testMalformedJsonThrowsException() {
        $this->expectException('DomainException');
        $this->JWT->jsonDecode('this is not valid JSON string');
    }

    function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->JWT = new JWT();
    }
}