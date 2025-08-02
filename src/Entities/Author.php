<?php

declare(strict_types=1);
namespace App\Entities;

class Author {
    private int $id;
    private string $firstName;
    private string $lastName;
    private string $username;
    private string $email;
    private string $password;
    private string $orcid;
    private string $afiliation;

    public function __construct(
        int $id,
        string $firstName,
        string $lastName,
        string $username,
        string $email,
        string $password,
        string $orcid,
        string $afiliation
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->username = $username;
        $this->email = $email;
        $this->password = password_hash($password, algo: PASSWORD_DEFAULT);
        $this->orcid = $orcid;
        $this->afiliation = $afiliation;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getFirstName(): string {
        return $this->firstName;
    }

    public function getLastName(): string {
        return $this->lastName;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getOrcid(): string {
        return $this->orcid;
    }

    public function getAfiliation(): string {
        return $this->afiliation;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setFirstName(string $firstName): void {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void {
        $this->lastName = $lastName;
    }

    public function setUsername(string $username): void {
        $this->username = $username;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setPassword(string $password): void {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    public function setOrcid(string $orcid): void {
        $this->orcid = $orcid;
    }

    public function setAfiliation(string $afiliation): void {
        $this->afiliation = $afiliation;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'username' => $this->username,
            'email' => $this->email,
            'orcid' => $this->orcid,
            'afiliation' => $this->afiliation
        ];
    }

    public static function fromArray(array $data): self {
        return new self(
            $data['id'],
            $data['firstName'],
            $data['lastName'],
            $data['username'],
            $data['email'],
            $data['password'],
            $data['orcid'],
            $data['afiliation']
        );
    }



}
