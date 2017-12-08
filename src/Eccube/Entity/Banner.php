<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Util\EntityUtil;

/**
 * Banner
 */
class Banner extends \Eccube\Entity\AbstractEntity
{
    const BANNER = 1;
    const SLIDER = 2;
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getFileName();
    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $file_name;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var integer
     */
    private $type;

    /**
     * @var string
     */
    private $link;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set file_name
     *
     * @param string $fileName
     * @return Banner
     */
    public function setFileName($fileName)
    {
        $this->file_name = $fileName;

        return $this;
    }

    /**
     * Get file_name
     *
     * @return string 
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     * @return Banner
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer 
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set $Link
     *
     * @param string $Link
     * @return Banner
     */
    public function setLink($Link)
    {
        $this->link = $Link;

        return $this;
    }

    /**
     * Get file_name
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set rank
     *
     * @param integer $Type
     * @return Banner
     */
    public function setType($Type)
    {
        $this->type = $Type;

        return $this;
    }

    /**
     * Get Type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return Banner
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }
}
