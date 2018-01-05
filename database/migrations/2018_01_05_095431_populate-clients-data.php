<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PopulateClientsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $sql = "INSERT INTO `oauth_clients` (`id`,`user_id`,`name`,`secret`,`redirect`,`personal_access_client`,`password_client`,`revoked`,`created_at`,`updated_at`) VALUES (1,NULL,'Gateway Personal Access Client','rh7EcaMJOwTZ3qCb4glsOnr1YDYFPJae71jcGwif','http://localhost',1,0,0,NOW(),NOW());";
            $sql .= "INSERT INTO `oauth_clients` (`id`,`user_id`,`name`,`secret`,`redirect`,`personal_access_client`,`password_client`,`revoked`,`created_at`,`updated_at`) VALUES (2,NULL,'Gateway Password Grant Client','VQVB307T7obFNc4TZqm5i4dkadYVzBCbCoo14Ibs','http://localhost',0,1,0,NOW(),NOW());";
            $sql .= "INSERT INTO `oauth_clients` (`id`,`user_id`,`name`,`secret`,`redirect`,`personal_access_client`,`password_client`,`revoked`,`created_at`,`updated_at`) VALUES (3,NULL,'UI Password Grant Client','OZiAoiI7h4kgxflwQJltX3cR7UNb1uGsSaD2aKfM','http://localhost',0,1,0,NOW(),NOW());";
            $sql .= "INSERT INTO `oauth_personal_access_clients` (`id`,`client_id`,`created_at`,`updated_at`) VALUES (1,1,NOW(),NOW());";

            DB::connection()->getPdo()->exec($sql);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
        });
    }
}
