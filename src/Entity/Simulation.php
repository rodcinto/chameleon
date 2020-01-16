<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SimulationRepository")
 */
class Simulation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $http_verb;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $parameters;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $request_body_content;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $response_body_content;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $response_content_type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $response_code;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $response_delay;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ttl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $query_string;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alias;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getHttpVerb(): ?string
    {
        return $this->http_verb;
    }

    public function setHttpVerb(string $http_verb): self
    {
        $this->http_verb = $http_verb;

        return $this;
    }

    public function getParameters(): ?string
    {
        return $this->parameters;
    }

    public function setParameters(?string $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getRequestBodyContent(): ?string
    {
        return $this->request_body_content;
    }

    public function setRequestBodyContent(?string $request_body_content): self
    {
        $this->request_body_content = $request_body_content;

        return $this;
    }

    public function getResponseBodyContent(): ?string
    {
        return $this->response_body_content;
    }

    public function setResponseBodyContent(?string $response_body_content): self
    {
        $this->response_body_content = $response_body_content;

        return $this;
    }

    public function getResponseContentType(): ?string
    {
        return $this->response_content_type;
    }

    public function setResponseContentType(?string $response_content_type): self
    {
        $this->response_content_type = $response_content_type;

        return $this;
    }

    public function getResponseCode(): ?int
    {
        return $this->response_code;
    }

    public function setResponseCode(?int $response_code): self
    {
        $this->response_code = $response_code;

        return $this;
    }

    public function getResponseDelay(): ?int
    {
        return $this->response_delay;
    }

    public function setResponseDelay(?int $response_delay): self
    {
        $this->response_delay = $response_delay;

        return $this;
    }

    public function getTtl(): ?int
    {
        return $this->ttl;
    }

    public function setTtl(?int $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }

    public function getQueryString(): ?string
    {
        return $this->query_string;
    }

    public function setQueryString(?string $query_string): self
    {
        $this->query_string = $query_string;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }
}
