use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->json('lokasiMasuk')->change();  // Ubah dari text ke json
            $table->json('lokasiPulang')->nullable()->change();  // Ubah dari text ke json
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->text('lokasiMasuk')->change();  // Kembalikan ke text jika rollback
            $table->text('lokasiPulang')->nullable()->change();
        });
    }
};
