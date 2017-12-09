<?php
namespace Plugin\Holiday\Entity;

class HolidayConfig extends \Eccube\Entity\AbstractEntity{
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
	private $config_data;

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
	* Get config_data
	*
	* @return integer
	**/
	public function getConfigData(){
		return $this->config_data;
	}

	/**
	* Set config_data
	*
	* @param  integer $config_data
	* @return HolidayConfig
	**/
	public function setConfigData($config_data){
		$this->config_data = $config_data;
		return $this;
	}

	/**
	* Set del_flg
	*
	* @param  integer $delFlg
	* @return HolidayConfig
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
	* @return HolidayConfig
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
	* @return HolidayConfig
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
