<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCourseOrderAttachmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('course_order_attachments', function(Blueprint $table)
		{
			$table->foreign('course_id', 'course_order_attachments_course_id')->references('id')->on('courses')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('package_id', 'course_order_attachments_package_id')->references('id')->on('packages')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('user_id', 'course_order_attachments_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('course_order_attachments', function(Blueprint $table)
		{
			$table->dropForeign('course_order_attachments_course_id');
			$table->dropForeign('course_order_attachments_package_id');
			$table->dropForeign('course_order_attachments_user_id');
		});
	}

}
