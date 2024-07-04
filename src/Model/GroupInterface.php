<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model;

interface GroupInterface
{
    public function addRole(string $role): self;

    public function getId(): ?int;

    public function getName(): ?string;

    public function hasRole(string $role): bool;

    public function getRoles(): array;

    public function removeRole(string $role): self;

    public function setName(string $name): self;

    public function setRoles(array $roles): self;
}