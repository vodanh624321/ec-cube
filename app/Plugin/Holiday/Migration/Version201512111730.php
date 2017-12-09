<?php
namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version201512111730 extends AbstractMigration{

	/**
	* @param Schema $schema
	**/
	public function up(Schema $schema){
		// this up() migration is auto-generated, please modify it to your needs
		$this->createPlgHolidayPlugin($schema);
		$this->createPlgHoliday($schema);
		$this->createPlgHolidayWeek($schema);
		$this->createPlgHolidayConfig($schema);
	}

	/**
	* @param Schema $schema
	**/
	public function down(Schema $schema){
		$app = new \Eccube\Application();

		// this down() migration is auto-generated, please modify it to your needs
		$schema->dropTable('plg_holiday_plugin');
		$schema->dropTable('plg_holiday');
		$schema->dropTable('plg_holiday_week');
		$schema->dropTable('plg_holiday_config');

		/* ブロックの削除 */
		$block_position_delete = "DELETE FROM dtb_block_position WHERE block_id = (SELECT block_id FROM dtb_block WHERE file_name = 'pg_calendar')";
		$this->connection->executeUpdate($block_position_delete);
		$block_delete = "DELETE FROM dtb_block WHERE file_name = 'pg_calendar'";
		$this->connection->executeUpdate($block_delete);

		/* ブロックファイルとCSSファイルの削除 */
		$pg_calendar_filename = $app['config']['template_realdir']."/Block/pg_calendar.twig";
		@unlink($pg_calendar_filename);
		$pg_calendar_css_filename = $app['config']['template_html_realdir']."/css/pg_calendar.css";
		@unlink($pg_calendar_css_filename);
	}

	public function postUp(Schema $schema){
		$app = new \Eccube\Application();
		$app->boot();
		$pluginCode = 'Holiday';
		$pluginName = '定休日管理プラグイン';
		$datetime = date('Y-m-d H:i:s');
		$insert = "INSERT INTO plg_holiday_plugin(plugin_code, plugin_name, create_date, update_date) VALUES ('$pluginCode', '$pluginName', '$datetime', '$datetime');";
		$this->connection->executeUpdate($insert);

		/* 定休日基本設定 */
		$insert = "INSERT INTO plg_holiday_config(config_data, create_date, update_date) VALUES ('2', '$datetime', '$datetime');";
		$this->connection->executeUpdate($insert);

		/* 定休日曜日設定 */
		$insert = "INSERT INTO plg_holiday_week(week, create_date, update_date) VALUES ('".serialize(null)."', '$datetime', '$datetime');";
		$this->connection->executeUpdate($insert);

		/* 初期設定の定休日設定 */
		$Holiday_Title = array(
			"元日(1月1日)", "成人の日(1月第2月曜日)", "建国記念の日(2月11日)", "春分の日(3月21日)", "昭和の日(4月29日)",
			"憲法記念日(5月3日)", "みどりの日(5月4日)", "こどもの日(5月5日)", "海の日(7月第3月曜日)", "敬老の日(9月第3月曜日)",
			"秋分の日(9月23日)", "体育の日(10月第2月曜日)", "文化の日(11月3日)", "勤労感謝の日(11月23日)", "天皇誕生日(12月23日)",
		);
		$Holiday_Month = array(
			"1", "1", "2", "3", "4",
			"5", "5", "5", "7", "9",
			"9", "10", "11", "11", "12",
		);
		$Holiday_Day = array(
			"1", "14", "11", "21", "29",
			"3", "4", "5", "21", "15",
			"23", "13", "3", "23", "23",
		);
		$Holiday_Rank = array(
			"100", "99", "98", "97", "96",
			"95", "94", "93", "92", "91",
			"90", "89", "88", "87", "86",
		);
		for($i=0; $i<count($Holiday_Title); $i++){
			$Holiday_insert = "INSERT INTO plg_holiday(title, month, day, rank, create_date, update_date)
			VALUES ('".$Holiday_Title[$i]."', '".$Holiday_Month[$i]."', '".$Holiday_Day[$i]."', '".$Holiday_Rank[$i]."', '$datetime', '$datetime');";
			$this->connection->executeUpdate($Holiday_insert);
		}

		/* ブロックの追加 */
		$Block_insert = "INSERT INTO dtb_block(
						block_id, device_type_id, block_name, file_name, logic_flg, deletable_flg, create_date, update_date
					) VALUES (
						(SELECT max(block_id) +1 as block_id FROM dtb_block AS sel_dtb_block WHERE device_type_id = 10), '10', '定休日カレンダー', 'pg_calendar', 0, 0, '$datetime', '$datetime'
					);";
		$this->connection->executeUpdate($Block_insert);

		/* ブロックファイルとCSSファイルの生成 */
		$pg_calendar_filename = $app['config']['block_realdir']."/pg_calendar.twig";
		$pg_calendar = file_get_contents($app['config']['plugin_realdir']."/Holiday/View/default/pg_calendar.twig");
		file_put_contents($pg_calendar_filename, $pg_calendar);

		$pg_calendar_css_filename = $app['config']['template_html_realdir']."/css/pg_calendar.css";
		$pg_calendar_css = file_get_contents($app['config']['plugin_realdir']."/Holiday/View/default/pg_calendar.css");
		file_put_contents($pg_calendar_css_filename, $pg_calendar_css);
	}

	/* プラグイン情報管理テーブルの生成 */
	protected function createPlgHolidayPlugin(Schema $schema){
		$table = $schema->createTable("plg_holiday_plugin");
		$table->addColumn('plugin_id', 'integer', array('autoincrement' => true,));
		$table->addColumn('plugin_code', 'text', array('notnull' => true,));
		$table->addColumn('plugin_name', 'text', array('notnull' => true,));
		$table->addColumn('sub_data', 'text', array('notnull' => false,));
		$table->addColumn('auto_update_flg', 'smallint', array('notnull' => true, 'unsigned' => false, 'default' => 0,));
		$table->addColumn('del_flg', 'smallint', array('notnull' => true, 'unsigned' => false, 'default' => 0,));
		$table->addColumn('create_date', 'datetime', array('notnull' => true, 'unsigned' => false,));
		$table->addColumn('update_date', 'datetime', array('notnull' => true, 'unsigned' => false,));
		$table->setPrimaryKey(array('plugin_id'));
	}

	/* 定休日管理テーブルの生成 */
	protected function createPlgHoliday(Schema $schema){
		$table = $schema->createTable("plg_holiday");
		$table->addColumn('holiday_id', 'integer', array('autoincrement' => true,));
		$table->addColumn('title', 'text', array('notnull' => true,));
		$table->addColumn('month', 'smallint', array('notnull' => true,));
		$table->addColumn('day', 'smallint', array('notnull' => true,));
		$table->addColumn('rank', 'integer', array('notnull' => true, 'unsigned' => false, 'default' => 0,));
		$table->addColumn('del_flg', 'smallint', array('notnull' => true, 'unsigned' => false, 'default' => 0,));
		$table->addColumn('create_date', 'datetime', array('notnull' => true, 'unsigned' => false,));
		$table->addColumn('update_date', 'datetime', array('notnull' => true, 'unsigned' => false,));
		$table->setPrimaryKey(array('holiday_id'));
	}

	/* 定休曜日管理テーブルの生成 */
	protected function createPlgHolidayWeek(Schema $schema){
		$table = $schema->createTable("plg_holiday_week");
		$table->addColumn('holiday_week_id', 'integer', array('autoincrement' => true,));
		$table->addColumn('week', 'text', array('notnull' => true,));
		$table->addColumn('del_flg', 'smallint', array('notnull' => true, 'unsigned' => false, 'default' => 0,));
		$table->addColumn('create_date', 'datetime', array('notnull' => true, 'unsigned' => false,));
		$table->addColumn('update_date', 'datetime', array('notnull' => true, 'unsigned' => false,));
		$table->setPrimaryKey(array('holiday_week_id'));
	}

	/* 定休日　基本設定テーブルの生成 */
	protected function createPlgHolidayConfig(Schema $schema){
		$table = $schema->createTable("plg_holiday_config");
		$table->addColumn('holiday_config_id', 'integer', array('autoincrement' => true,));
		$table->addColumn('config_data', 'text', array('notnull' => true,));
		$table->addColumn('del_flg', 'smallint', array('notnull' => true, 'unsigned' => false, 'default' => 0,));
		$table->addColumn('create_date', 'datetime', array('notnull' => true, 'unsigned' => false,));
		$table->addColumn('update_date', 'datetime', array('notnull' => true, 'unsigned' => false,));
		$table->setPrimaryKey(array('holiday_config_id'));
	}

	function getHolidayCode(){
		$config = \Eccube\Application::alias('config');
		return "";
	}
}
