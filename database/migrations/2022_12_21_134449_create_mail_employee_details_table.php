<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailEmployeeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_employee_details', function (Blueprint $table) {
            $table->id();
            $table->longText('mail_id',200);
            $table->string('from',200);
            $table->longText('subject');
            $table->string('date');
            $table->string('mail_reply_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_employee_details');
    }
}
