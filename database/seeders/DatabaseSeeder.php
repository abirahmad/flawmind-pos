<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call(SeedAccountingAccountsTable::class);
        $this->call(SeedAccountingAccountsTransactionsTable::class);
        $this->call(SeedAccountingAccountTypesTable::class);
        $this->call(SeedAccountingAccTransMappingsTable::class);
        $this->call(SeedAccountingBudgetsTable::class);
        $this->call(SeedAccountsTable::class);
        $this->call(SeedAccountTransactionsTable::class);
        $this->call(SeedActivityLogTable::class);
        $this->call(SeedBarcodesTable::class);
        $this->call(SeedBusinessTable::class);
        $this->call(SeedBusinessLocationsTable::class);
        $this->call(SeedCashRegistersTable::class);
        $this->call(SeedContactsTable::class);
        $this->call(SeedCurrenciesTable::class);
        $this->call(SeedEssentialsPayrollGroupsTable::class);
        $this->call(SeedEssentialsPayrollGroupTransactionsTable::class);
        $this->call(SeedEssentialsShiftsTable::class);
        $this->call(SeedEssentialsUserShiftsTable::class);
        $this->call(SeedInvoiceLayoutsTable::class);
        $this->call(SeedInvoiceSchemesTable::class);
        $this->call(SeedMigrationsTable::class);
        $this->call(SeedModelHasPermissionsTable::class);
        $this->call(SeedModelHasRolesTable::class);
        $this->call(SeedNotificationsTable::class);
        $this->call(SeedNotificationTemplatesTable::class);
        $this->call(SeedOauthClientsTable::class);
        $this->call(SeedOauthPersonalAccessClientsTable::class);
        $this->call(SeedPermissionsTable::class);
        $this->call(SeedProductsTable::class);
        $this->call(SeedProductLocationsTable::class);
        $this->call(SeedProductVariationsTable::class);
        $this->call(SeedPurchaseLinesTable::class);
        $this->call(SeedReferenceCountsTable::class);
        $this->call(SeedRolesTable::class);
        $this->call(SeedRoleHasPermissionsTable::class);
        $this->call(SeedSystemTable::class);
        $this->call(SeedTransactionsTable::class);
        $this->call(SeedTransactionPaymentsTable::class);
        $this->call(SeedTransactionSellLinesTable::class);
        $this->call(SeedTransactionSellLinesPurchaseLinesTable::class);
        $this->call(SeedUnitsTable::class);
        $this->call(SeedUsersTable::class);
        $this->call(SeedVariationsTable::class);
        $this->call(SeedVariationLocationDetailsTable::class);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
