<?php

class ResponseTest extends \Codeception\TestCase\Test
{

    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $response = null;

    protected function _before()
    {
        $di = new \Phalcon\DI\FactoryDefault();
        $this->response = new \ApiBird\Response($di);
    }

    protected function _after()
    {
        
    }

}
