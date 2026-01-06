<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RestoreSourceData extends Command
{
    protected $signature = 'app:restore-source-data';
    protected $description = 'Restore data from Prisma SQL backup with CUID to UUID mapping';

    private array $idMap = [];
    private string $backupPath = '/Users/volkaninanc/Documents/VIBECODING/KIRO/Mediaclick/backups/manual/20260104_122817_manual_backup.sql';

    public function handle()
    {
        if (!file_exists($this->backupPath)) {
            $this->error("Backup file not found: {$this->backupPath}");
            return 1;
        }

        $this->info("Starting data restoration...");

        // Disable Foreign Key Checks for Postgres
        DB::statement('SET session_replication_role = replica;');

        try {
            // Priority 1: Independent/Reference Data
            $this->restoreTable('roles', ['id', 'name', 'description']);
            $this->restoreTable('countries', ['id', 'code', 'name', 'name_en', 'is_active', 'sort_order', 'created_at', 'updated_at'], [
                'nameEn' => 'name_en',
                'isActive' => 'is_active',
                'sortOrder' => 'sort_order',
                'createdAt' => 'created_at',
                'updatedAt' => 'updated_at'
            ]);
            $this->restoreTable('price_definitions', ['id', 'name', 'category', 'duration', 'price', 'currency', 'description', 'is_active', 'created_at', 'updated_at'], [
                'isActive' => 'is_active',
                'createdAt' => 'created_at',
                'updatedAt' => 'updated_at'
            ]);

            $this->restoreTable('reference_categories', ['id', 'key', 'name', 'description', 'is_active', 'created_at', 'updated_at'], [
                'isActive' => 'is_active',
                'createdAt' => 'created_at',
                'updatedAt' => 'updated_at'
            ]);

            $this->restoreTable('reference_items', ['id', 'category_key', 'key', 'display_label', 'description', 'sort_order', 'is_active', 'is_default', 'metadata', 'created_at', 'updated_at'], [
                'categoryKey' => 'category_key',
                'displayLabel' => 'display_label',
                'sortOrder' => 'sort_order',
                'isActive' => 'is_active',
                'isDefault' => 'is_default',
                'createdAt' => 'created_at',
                'updatedAt' => 'updated_at'
            ]);

            // Priority 2: Users & Cities
            $this->restoreTable('users', ['id', 'email', 'name', 'password', 'role_id', 'created_at', 'updated_at'], [
                'roleId' => 'role_id',
                'createdAt' => 'created_at',
                'updatedAt' => 'updated_at'
            ]);
            $this->restoreTable('cities', ['id', 'country_id', 'name', 'plate_code', 'is_active', 'sort_order', 'created_at', 'updated_at'], [
                'countryId' => 'country_id',
                'plateCode' => 'plate_code',
                'isActive' => 'is_active',
                'sortOrder' => 'sort_order',
                'createdAt' => 'created_at',
                'updatedAt' => 'updated_at'
            ]);

            // Priority 3: Customers
            $this->restoreTable('customers', [
                'id',
                'name',
                'title',
                'email',
                'phone',
                'phones',
                'address',
                'created_at',
                'updated_at',
                'city_id',
                'country_id',
                'tax_number',
                'tax_office',
                'website',
                'websites',
                'current_code',
                'logo_url',
                'customer_type'
            ], [
                'createdAt' => 'created_at',
                'updatedAt' => 'updated_at',
                'cityId' => 'city_id',
                'countryId' => 'country_id',
                'taxNumber' => 'tax_number',
                'taxOffice' => 'tax_office',
                'currentCode' => 'current_code',
                'logoUrl' => 'logo_url',
                'customerType' => 'customer_type'
            ]);

            // Priority 4: Related Data
            $this->restoreTable('assets', ['id', 'customer_id', 'type', 'name', 'url', 'created_at', 'updated_at'], [
                'customerId' => 'customer_id',
                'createdAt' => 'created_at',
                'updatedAt' => 'updated_at'
            ]);
            $this->restoreTable('contacts', [
                'id',
                'customer_id',
                'name',
                'email',
                'emails',
                'phone',
                'phones',
                'position',
                'status',
                'gender',
                'birth_date',
                'social_profiles',
                'created_at',
                'updated_at',
                'extensions'
            ], [
                'customerId' => 'customer_id',
                'birthDate' => 'birth_date',
                'socialProfiles' => 'social_profiles',
                'createdAt' => 'created_at',
                'updatedAt' => 'updated_at'
            ]);
            $this->restoreTable('services', [
                'id',
                'customer_id',
                'asset_id',
                'price_definition_id',
                'service_name',
                'service_category',
                'service_duration',
                'service_price',
                'service_currency',
                'start_date',
                'end_date',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'status'
            ], [
                'customerId' => 'customer_id',
                'assetId' => 'asset_id',
                'priceDefinitionId' => 'price_definition_id',
                'serviceName' => 'service_name',
                'serviceCategory' => 'service_category',
                'serviceDuration' => 'service_duration',
                'servicePrice' => 'service_price',
                'serviceCurrency' => 'service_currency',
                'startDate' => 'start_date',
                'endDate' => 'end_date',
                'isActive' => 'is_active',
                'createdAt' => 'created_at',
                'updatedAt' => 'updated_at'
            ]);

            $this->restoreTable('offers', [
                'id',
                'number',
                'customer_id',
                'status',
                'title',
                'description',
                'total_amount',
                'original_amount',
                'discount_percentage',
                'discounted_amount',
                'currency',
                'valid_until',
                'pdf_url',
                'tracking_token',
                'created_at',
                'updated_at'
            ], [
                'customerId' => 'customer_id',
                'totalAmount' => 'total_amount',
                'originalAmount' => 'original_amount',
                'discountPercentage' => 'discount_percentage',
                'discountedAmount' => 'discounted_amount',
                'validUntil' => 'valid_until',
                'pdfUrl' => 'pdf_url',
                'trackingToken' => 'tracking_token',
                'createdAt' => 'created_at',
                'updatedAt' => 'updated_at'
            ]);

            $this->restoreTable('sales', [
                'id',
                'customer_id',
                'offer_id',
                'currency',
                'sale_date',
                'amount',
                'created_at',
                'updated_at'
            ], [
                'customerId' => 'customer_id',
                'offerId' => 'offer_id',
                'saleDate' => 'sale_date',
                'createdAt' => 'created_at',
                'updatedAt' => 'updated_at'
            ]);

        } finally {
            DB::statement("SET session_replication_role = 'origin';");
        }

        $this->info("Restoration completed successfully!");
    }

    private function restoreTable(string $tableName, array $targetColumns, array $columnMap = [])
    {
        $this->comment("Restoring table: {$tableName}");

        $handle = fopen($this->backupPath, "r");
        if (!$handle)
            return;

        $inData = false;
        $sourceColumns = [];
        $rowCount = 0;
        $activeRow = "";

        while (($line = fgets($handle)) !== false) {
            $line = rtrim($line, "\r\n");

            if (!$inData) {
                if (stripos($line, "COPY ") !== false && stripos($line, "public.") !== false && stripos($line, $tableName) !== false) {
                    $start = strpos($line, '(');
                    $end = strrpos($line, ')');
                    if ($start !== false && $end !== false) {
                        $inData = true;
                        $columnsStr = substr($line, $start + 1, $end - $start - 1);
                        $sourceColumns = array_map(fn($c) => trim($c, ' "'), explode(',', $columnsStr));
                        $this->info("Header found for {$tableName}");
                        continue;
                    }
                }
            } else {
                if ($line === '\.') {
                    $inData = false;
                    break;
                }

                $activeRow .= ($activeRow === "" ? "" : "\n") . $line;
                $values = explode("\t", $activeRow);

                if (count($values) < count($sourceColumns)) {
                    continue;
                }

                if (count($values) > count($sourceColumns)) {
                    $activeRow = "";
                    continue;
                }

                $item = [];
                foreach ($sourceColumns as $index => $col) {
                    $val = $values[$index];
                    $val = ($val === '\N' || $val === '') ? null : $val;

                    $targetCol = $columnMap[$col] ?? Str::snake($col);

                    if ($targetCol === 'id' || str_ends_with($targetCol, '_id')) {
                        if ($val)
                            $val = $this->getUuid(trim($val));
                    }

                    // Strict handle postgres array string notation
                    if ($val && Str::startsWith($val, '{') && Str::endsWith($val, '}')) {
                        $val = $this->parsePostgresArray($val);
                    }

                    if ($val === 't')
                        $val = true;
                    if ($val === 'f')
                        $val = false;
                    if ($val === '{}')
                        $val = null;

                    $item[$targetCol] = $val;
                }

                $insertData = array_intersect_key($item, array_flip($targetColumns));

                try {
                    DB::table($tableName)->insertOrIgnore($insertData);
                    $rowCount++;
                } catch (\Exception $e) {
                    // Still log other errors
                    $this->error("Error in {$tableName}: " . $e->getMessage());
                }

                $activeRow = "";
            }
        }
        fclose($handle);

        $this->info("Successfully processed {$rowCount} rows for {$tableName}");
    }

    private function parsePostgresArray(string $pgArray): ?string
    {
        $content = trim($pgArray, '{}');
        if ($content === '')
            return json_encode([]);

        // Regex to split by comma but respect double quotes
        $parts = preg_split('/,(?=(?:[^\"]*\"[^\"]*\")*[^\"]*$)/', $content);

        $cleaned = [];
        foreach ($parts as $part) {
            $part = trim($part, '" ');
            if ($part !== '')
                $cleaned[] = $part;
        }

        return json_encode($cleaned);
    }

    private function getUuid(string $cuid): string
    {
        if (Str::isUuid($cuid))
            return $cuid;

        if (!isset($this->idMap[$cuid])) {
            $this->idMap[$cuid] = (string) Str::uuid();
        }

        return $this->idMap[$cuid];
    }
}
