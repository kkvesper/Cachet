<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncidentTranslationsTable extends Migration
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'incident_translations';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('incident_id');
            $table->string('locale', 5);
            $table->string('name');
            $table->longText('message');
            $table->timestamps();

            $table->index(['incident_id', 'locale'], 'translated_incident');

            $table
                ->foreign('incident_id')
                ->references('id')
                ->on('incidents')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
