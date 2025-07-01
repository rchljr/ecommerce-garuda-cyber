    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Jalankan migrasi.
         */
        public function up(): void
        {
            // Pastikan nama tabel di sini sesuai: 'product_option_values'
            Schema::create('product_option_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_option_id')->constrained('product_options')->onDelete('cascade');
                $table->string('value');
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }

        /**
         * Batalkan migrasi.
         */
        public function down(): void
        {
            Schema::dropIfExists('product_option_values');
        }
    };
    