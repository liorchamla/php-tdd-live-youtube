<?php

use PHPUnit\Framework\TestCase;
use Twitter\Http\Request;

class RequestTest extends TestCase
{
    /** @test */
    public function we_can_instantiate_a_request()
    {
        $request = new Request([
            'author' => 'Lior',
            'content' => 'Contenu à la con'
        ]);

        $this->assertEquals('Lior', $request->get('author'));
        $this->assertEquals('Contenu à la con', $request->get('content'));
        $this->assertNull($request->get('inexistant'));
    }
}
