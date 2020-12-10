<?php

namespace Twitter\Http;

class Response
{
    protected string $content = '';
    protected array $headers = [];
    protected int $statusCode = 200;

    public function __construct(string $content = '', int $statusCode = 200, array $headers = ['Content-Type' => 'text/html'])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function getHeader(string $headerName): ?string
    {
        return $this->headers[$headerName] ?? null;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }


    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
    }

    public function send()
    {
        // En têtes (headers)
        /**
         * [
         *  'Content-Type' => 'text/html',
         *  'lang' => 'fr-FR
         * ]
         */
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        // Le code
        http_response_code($this->statusCode);

        // Le contenu avec un gros ECHO
        echo $this->content;
    }
}
