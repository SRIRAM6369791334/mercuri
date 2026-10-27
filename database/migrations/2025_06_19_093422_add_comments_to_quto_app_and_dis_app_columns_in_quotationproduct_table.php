<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE quotationproduct MODIFY quto_app TINYINT COMMENT '0 = pending, 1 = reject, 2 = approved'");
        DB::statement("ALTER TABLE quotationproduct MODIFY dis_app TINYINT COMMENT '0 = reject, 1 = pending, 2 = approved'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE quotationproduct MODIFY quto_app TINYINT");
        DB::statement("ALTER TABLE quotationproduct MODIFY dis_app TINYINT");
    }
};

