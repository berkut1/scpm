<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\OsTemplate;

use App\Model\ControlPanel\Entity\Panel\SolidCP\HostingSpace\SolidcpHostingSpace;
use Doctrine\ORM\Mapping as ORM;

/**
 * OsTemplate
 *
 * @ORM\Table(
 *     name="cp_solidcp_hosting_space_os_templates",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="unique_ids_cp_solidcp_hosting_space_os_templates", columns={"path", "id_hosting_space"})
 *  },
 *     indexes={
 *          @ORM\Index(name="cp_solidcp_hosting_space_os_templates_id_hosting_space_idx", columns={"id_hosting_space"})
 *  })
 * @ORM\Entity
 */
class OsTemplate
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=SolidcpHostingSpace::class, inversedBy="osTemplates")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_hosting_space", referencedColumnName="id", nullable=false)
     * })
     */
    private SolidcpHostingSpace $hostingSpace;

    /**
     * @ORM\Column(name="path", type="string", length=128, nullable=false)
     */
    private string $path;

    /**
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
     */
    private string $name;

    public function __construct(SolidcpHostingSpace $hostingSpace, string $path, string $name)
    {
        $this->hostingSpace = $hostingSpace;
        $this->path = $path;
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getHostingSpace(): SolidcpHostingSpace
    {
        return $this->hostingSpace;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
