<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionsUploadsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transactions_uploads', function(Blueprint $table)
		{
			$table->integer('file_id', true);
			$table->integer('orig_file_id')->nullable();
			$table->integer('Transaction_Docs_ID')->index('fk_transactions_uploads_file_id');
			$table->integer('Agent_ID');
			$table->bigInteger('Listing_ID');
			$table->bigInteger('Contract_ID')->nullable();
			$table->bigInteger('Referral_ID')->nullable();
			$table->integer('template_id')->nullable();
			$table->text('file_location')->nullable();
			$table->text('file_name')->nullable();
			$table->text('file_name_orig')->nullable();
			$table->text('file_name_display')->nullable();
			$table->string('file_type', 45)->nullable();
			$table->integer('pages_total')->nullable();
			$table->string('form_categories', 45)->nullable();
			$table->integer('form_tags')->nullable();
			$table->integer('checklist_group_id')->nullable();
			$table->string('state', 5)->nullable();
			$table->integer('form_group_id')->nullable();
			$table->text('helper_text')->nullable();
			$table->string('published', 45)->nullable();
			$table->integer('page_width')->nullable();
			$table->integer('page_height')->nullable();
			$table->string('page_size', 15)->nullable();
			$table->timestamps(10);
			$table->string('active', 45)->nullable();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('docs_transactions_uploads');
	}

}
