<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Model\Group;

#[ORM\Entity]
#[ORM\Table(name: "fos_user_user__group")]
class UserGroup extends Group
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    protected ?int $id = null;

    #[ORM\Column(type: "string", length: 180, nullable: true)] // Adjusted to nullable string
    protected ?string $name = null;

    #[ORM\Column(type: "json")]
    protected array $roles = [];

    public function __construct(?string $name = null, array $roles = [])
    {
        $this->name = $name;
        $this->roles = $roles;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function addRole(string $role): self
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): self
    {
        if (false !== $key = array_search($role, $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }


}
