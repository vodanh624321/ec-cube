<?php
namespace Plugin\Holiday\Entity;

class Holiday extends \Eccube\Entity\AbstractEntity{
	/**
	* @return string
	**/
	public function __toString(){
		return $this->getMethod();
	}

	/**
	* @var integer
	**/
	private $id;

	/**
	* @var string
	**/
	private $title;

	/**
	* @var string
	**/
	private $month;

	/**
	* @var string
	**/
	private $day;

	/**
	* @var integer
	**/
	private $rank;

	/**
	* @var integer
	**/
	private $del_flg;

	/**
	* @var \DateTime
	**/
	private $create_date;

	/**
	* @var \DateTime
	**/
	private $update_date;

	/**
	* Constructor
	**/
	public function __construct(){
	}

	/**
	* Set holiday id
	*
	* @param  string $id
	* @return Holiday
	**/
	public function setId($id){
		$this->id = $id;
		return $this;
	}

	/**
	* Get id
	*
	* @return integer
	**/
	public function getId(){
		return $this->id;
	}

	/**
	* Get title
	*
	* @return string
	**/
	public function getTitle(){
		return $this->title;
	}

	/**
	* Set title
	*
	* @param  string $title
	* @return Holiday
	**/
	public function setTitle($title){
		$this->title = $title;
		return $this;
	}

	/**
	* Get month
	*
	* @return string
	**/
	public function getMonth(){
		return $this->month;
	}

	/**
	* Set month
	*
	* @param  string $month
	* @return Holiday
	**/
	public function setMonth($month){
		$this->month = $month;
		return $this;
	}

	/**
	* Get day
	*
	* @return string
	**/
	public function getDay(){
		return $this->day;
	}

	/**
	* Set day
	*
	* @param  string $month
	* @return Holiday
	**/
	public function setDay($day){
		$this->day = $day;
		return $this;
	}

	/**
	* Get rank
	*
	* @return integer
	**/
	public function getRank(){
		return $this->rank;
	}

	/**
	* Set rank
	*
	* @param  integer $rank
	* @return Holiday
	**/
	public function setRank($rank){
		$this->rank = $rank;
		return $this;
	}

	/**
	* Set del_flg
	*
	* @param  integer $delFlg
	* @return Holiday
	**/
	public function setDelFlg($delFlg){
		$this->del_flg = $delFlg;
		return $this;
	}

	/**
	* Get del_flg
	*
	* @return integer
	**/
	public function getDelFlg(){
		return $this->del_flg;
	}

	/**
	* Set create_date
	*
	* @param  \DateTime $createDate
	* @return Holiday
	**/
	public function setCreateDate($createDate){
		$this->create_date = $createDate;
		return $this;
	}

	/**
	* Get create_date
	*
	* @return \DateTime
	**/
	public function getCreateDate(){
		return $this->create_date;
	}

	/**
	* Set update_date
	*
	* @param  \DateTime $updateDate
	* @return Holiday
	**/
	public function setUpdateDate($updateDate){
		$this->update_date = $updateDate;
		return $this;
	}

	/**
	* Get update_date
	*
	* @return \DateTime
	**/
	public function getUpdateDate(){
		return $this->update_date;
	}
}
