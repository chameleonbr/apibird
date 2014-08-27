<?php

class RequestTest extends \Codeception\TestCase\Test
{

    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $request = null;

    protected function _before()
    {
        $di = new \Phalcon\DI\FactoryDefault();
        $this->request = new \ApiBird\Request($di);
    }

    protected function _after()
    {
        
    }
    // tests
    public function testContentType()
    {
        $_SERVER['HTTP_CONTENT_TYPE'] = 'text/plain';
        $this->assertEquals('text/plain', $this->request->getContentType());
    }

    public function testEmptyContentType()
    {
        $_SERVER['HTTP_CONTENT_TYPE'] = '*/*';
        $this->assertEquals('', $this->request->getContentType());
    }
    
    public function testBestAccept()
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $this->assertEquals('application/json', $this->request->getBestAccept());
    }
    
    public function testEmptyBestAccept()
    {
        $_SERVER['HTTP_ACCEPT'] = '*/*';
        $this->assertEquals('', $this->request->getBestAccept());
    }

}
