<?php   

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('user_id');
            $table->string('spent_by', 100);
            $table->string('purpose', 100);
            $table->decimal('amount', 15, 2);
            $table->date('date_spent');
            $table->string('notes', 255)->nullable();
            $table->timestamps();
            $table->index(['business_id','user_id','purpose','date_spent']);
        });
    }
    public function down() {
        Schema::dropIfExists('expenses');
    }
};