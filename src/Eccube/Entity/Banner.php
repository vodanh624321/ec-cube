<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Util\EntityUtil;

/**
 * Banner
 */
class Banner extends \Eccube\Entity\AbstractEntity
{
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
