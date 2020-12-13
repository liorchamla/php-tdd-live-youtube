<?php

use PHPUnit\Framework\TestCase;
use Twitter\Http\Request;
use Twitter\Http\Response;
use Twitter\Validation\RequestValidator;

class RequestValidatorTest extends TestCase
{
    protected RequestValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new RequestValidator;
    }

    /** @test */
    public function validator_will_give_no_response_if_no_error_is_found()
    {
        // Etant donné une request avec des infos
        $request = new Request([
            'champ1' => 'Lior',
            'champ2' => 42,
            'champ3' => 'Kikoo'
        ]);
        // Et les champs obligatoires sont
        $requiredFields = ['champ1', 'champ3'];

        // Quand j'appelle le validateur en précisant la request et les champs obligatoires
        $result = $this->validator->validateFields($request, $requiredFields);

        // Alors le resultat devrait être null (signifiant qu'il n'y a pas d'erreur)
        $this->assertNull($result);
    }

    /** @test */
    public function validator_will_give_a_response_for_one_missing_field()
    {
        // Etant donné une request avec des infos
        $request = new Request([
            'champ1' => 'Lior',
            'champ2' => 42,
        ]);
        // Et les champs obligatoires sont
        $requiredFields = ['champ1', 'champ3'];

        // Quand j'appelle le validateur en précisant la request et les champs obligatoires
        $result = $this->validator->validateFields($request, $requiredFields);

        // Alors le resultat devrait être une response
        $this->assertInstanceOf(Response::class, $result);

        // Et elle devrait indiquer que 'champ3' manque
        $this->assertEquals('Le champ champ3 est manquant', $result->getContent());
    }

    public function validator_will_give_a_response_for_many_missing_fields()
    {
        // Etant donné une request avec des infos
        $request = new Request([
            'champ1' => 'Lior',
            'champ2' => 42,
        ]);
        // Et les champs obligatoires sont
        $requiredFields = ['champ1', 'champ3', 'champ4'];

        // Quand j'appelle le validateur en précisant la request et les champs obligatoires
        $result = $this->validator->validateFields($request, $requiredFields);

        // Alors le resultat devrait être une response
        $this->assertInstanceOf(Response::class, $result);

        // Et elle devrait indiquer que 'champ3' et 'champ4' manquent
        $this->assertEquals('Les champs champ3, champ4 sont manquants', $result->getContent());
    }
}
