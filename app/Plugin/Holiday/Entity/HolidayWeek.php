<?php
namespace Plugin\Holiday\Entity;

class HolidayWeek extends \Eccube\Entity\AbstractEntity{
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
	* @var integer
	**/
	//private $week;
	public $week;

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
	* Set holidayweek id
	*
	* @param  string $id
	* @return HolidayWeek
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
	* Get week
	*
	* @return integer
	**/
	public function getWeek(){
		return $this->week;
	}

	/**
	* Set week
	*
	* @param  integer $week
	* @return HolidayWeek
	**/
	public function setWeek($week){
		$this->week = $week;
		return $this;
	}

	/**
	* Set del_flg
	*
	* @param  integer $delFlg
	* @return HolidayWeek
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
	* @return HolidayWeek
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
	* @return HolidayWeek
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
