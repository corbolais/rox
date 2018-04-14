<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MembersTrad.
 *
 * @ORM\Table(name="MembersTrads", uniqueConstraints={@ORM\UniqueConstraint(name="Unique_entry", columns={"IdTrad", "IdOwner", "IdLanguage"})}, indexes={@ORM\Index(name="IdTrad", columns={"IdTrad"}), @ORM\Index(name="IdLanguage", columns={"IdLanguage"})})
 * @ORM\Entity
 */
class MembersTrad
{
    /**
     * @var int
     *
     * @ORM\Column(name="IdOwner", type="integer", nullable=false)
     */
    private $idowner;

    /**
     * @var int
     *
     * @ORM\Column(name="IdTrad", type="integer", nullable=false)
     */
    private $idtrad;

    /**
     * @var int
     *
     * @ORM\Column(name="IdTranslator", type="integer", nullable=false)
     */
    private $idtranslator;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     */
    private $updated = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created = '0000-00-00 00:00:00';

    /**
     * @var string
     *
     * @ORM\Column(name="Type", type="string", nullable=false)
     */
    private $type = 'member';

    /**
     * @var string
     *
     * @ORM\Column(name="Sentence", type="text", length=65535, nullable=false)
     */
    private $sentence;

    /**
     * @var int
     *
     * @ORM\Column(name="IdRecord", type="integer", nullable=false)
     */
    private $idrecord = '-1';

    /**
     * @var string
     *
     * @ORM\Column(name="TableColumn", type="string", length=200, nullable=false)
     */
    private $tablecolumn = 'NotSet';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Language
     *
     * @ORM\ManyToOne(targetEntity="Language")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="IdLanguage", referencedColumnName="id")
     * })
     */
    private $language;

    /**
     * Set idowner.
     *
     * @param int $idowner
     *
     * @return MembersTrad
     */
    public function setIdowner($idowner)
    {
        $this->idowner = $idowner;

        return $this;
    }

    /**
     * Get idowner.
     *
     * @return int
     */
    public function getIdowner()
    {
        return $this->idowner;
    }

    /**
     * Set idtrad.
     *
     * @param int $idtrad
     *
     * @return MembersTrad
     */
    public function setIdtrad($idtrad)
    {
        $this->idtrad = $idtrad;

        return $this;
    }

    /**
     * Get idtrad.
     *
     * @return int
     */
    public function getIdtrad()
    {
        return $this->idtrad;
    }

    /**
     * Set idtranslator.
     *
     * @param int $idtranslator
     *
     * @return MembersTrad
     */
    public function setIdtranslator($idtranslator)
    {
        $this->idtranslator = $idtranslator;

        return $this;
    }

    /**
     * Get idtranslator.
     *
     * @return int
     */
    public function getIdtranslator()
    {
        return $this->idtranslator;
    }

    /**
     * Set updated.
     *
     * @param \DateTime $updated
     *
     * @return MembersTrad
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated.
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return MembersTrad
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return MembersTrad
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set sentence.
     *
     * @param string $sentence
     *
     * @return MembersTrad
     */
    public function setSentence($sentence)
    {
        $this->sentence = $sentence;

        return $this;
    }

    /**
     * Get sentence.
     *
     * @return string
     */
    public function getSentence()
    {
        return $this->sentence;
    }

    /**
     * Set idrecord.
     *
     * @param int $idrecord
     *
     * @return MembersTrad
     */
    public function setIdrecord($idrecord)
    {
        $this->idrecord = $idrecord;

        return $this;
    }

    /**
     * Get idrecord.
     *
     * @return int
     */
    public function getIdrecord()
    {
        return $this->idrecord;
    }

    /**
     * Set tablecolumn.
     *
     * @param string $tablecolumn
     *
     * @return MembersTrad
     */
    public function setTablecolumn($tablecolumn)
    {
        $this->tablecolumn = $tablecolumn;

        return $this;
    }

    /**
     * Get tablecolumn.
     *
     * @return string
     */
    public function getTablecolumn()
    {
        return $this->tablecolumn;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set language.
     *
     * @param Language $language
     *
     * @return MembersTrad
     */
    public function setLanguage(Language $language = null)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get idlanguage.
     *
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }
}