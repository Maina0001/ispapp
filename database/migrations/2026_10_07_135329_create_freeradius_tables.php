<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- radreply: attributes returned on successful auth (e.g., Mikrotik-Rate-Limit)
        Schema::create('radreply', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 64)->default('')->index();
            $table->string('attribute', 64)->default('');
            $table->string('op', 2)->default('=');
            $table->string('value', 253)->default('');
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
        });

        // --- radgroupcheck: group-level authorization checks
        Schema::create('radgroupcheck', function (Blueprint $table) {
            $table->increments('id');
            $table->string('groupname', 64)->default('')->index();
            $table->string('attribute', 64)->default('');
            $table->string('op', 2)->default('==');
            $table->string('value', 253)->default('');
        });

        // --- radgroupreply: group-level reply attributes
        Schema::create('radgroupreply', function (Blueprint $table) {
            $table->increments('id');
            $table->string('groupname', 64)->default('')->index();
            $table->string('attribute', 64)->default('');
            $table->string('op', 2)->default('=');
            $table->string('value', 253)->default('');
        });

        // --- radusergroup: user → group mapping
        Schema::create('radusergroup', function (Blueprint $table) {
            $table->string('username', 64)->default('')->index();
            $table->string('groupname', 64)->default('');
            $table->integer('priority')->default(1);
        });

        // --- nas: known Network Access Servers (your MikroTiks)
        Schema::create('nas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nasname', 128)->index();
            $table->string('shortname', 32)->nullable();
            $table->string('type', 30)->default('other');
            $table->integer('ports')->nullable();
            $table->string('secret', 60)->default('secret');
            $table->string('server', 64)->nullable();
            $table->string('community', 50)->nullable();
            $table->string('description', 200)->default('RADIUS Client');
        });

        // --- radacct: session/accounting records (data usage lives here)
        Schema::create('radacct', function (Blueprint $table) {
            $table->bigIncrements('radacctid');
            $table->string('acctsessionid', 64)->default('')->index();
            $table->string('acctuniqueid', 32)->default('')->unique();
            $table->string('username', 64)->default('')->index();
            $table->string('groupname', 64)->default('');
            $table->string('realm', 64)->nullable();
            $table->string('nasipaddress', 15)->default('')->index();
            $table->string('nasportid', 32)->nullable();
            $table->string('nasporttype', 32)->nullable();
            $table->timestamp('acctstarttime')->nullable()->index();
            $table->timestamp('acctupdatetime')->nullable();
            $table->timestamp('acctstoptime')->nullable()->index();
            $table->integer('acctinterval')->nullable();
            $table->integer('acctsessiontime')->unsigned()->nullable();
            $table->string('acctauthentic', 32)->nullable();
            $table->string('connectinfo_start', 50)->nullable();
            $table->string('connectinfo_stop', 50)->nullable();
            $table->bigInteger('acctinputoctets')->nullable();
            $table->bigInteger('acctoutputoctets')->nullable();
            $table->string('calledstationid', 50)->default('');
            $table->string('callingstationid', 50)->default('');
            $table->string('acctterminatecause', 32)->default('');
            $table->string('servicetype', 32)->nullable();
            $table->string('framedprotocol', 32)->nullable();
            $table->string('framedipaddress', 15)->default('');
            $table->string('framedipv6address', 45)->nullable();
            $table->string('framedipv6prefix', 45)->nullable();
            $table->string('framedinterfaceid', 44)->nullable();
            $table->string('delegatedipv6prefix', 45)->nullable();
        });

        // --- radpostauth: post-authentication log
        Schema::create('radpostauth', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username', 64)->default('')->index();
            $table->string('pass', 64)->default('');
            $table->string('reply', 32)->default('');
            $table->timestamp('authdate')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radpostauth');
        Schema::dropIfExists('radacct');
        Schema::dropIfExists('nas');
        Schema::dropIfExists('radusergroup');
        Schema::dropIfExists('radgroupreply');
        Schema::dropIfExists('radgroupcheck');
        Schema::dropIfExists('radreply');
    }
};