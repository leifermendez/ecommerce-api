<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(ShopTableSeeder::class);
        $this->call(AttachedTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(PurchaseTableSeeder::class); 
        $this->call(OrderSeeder::class);
        $this->call(CommentsTableSeeder::class);
        $this->call(AttributesTableSeeder::class);
        $this->call(CategoryAttributesTableSeeder::class);
        $this->call(ProductVariableTableSeeder::class);
        $this->call(ShippingUserTableSeeder::class);
        $this->call(BannersTableSeeder::class);
        $this->call(UserPaymentSettingTable::class);
        $this->call(AttechedProdcutsTableSeeder::class);
        $this->call(PurchaseDetailTableSeeder::class);
        $this->call(ZoneAvailableTableSeeder::class);
        $this->call(SettingTableSeeder::class);
        $this->call(PickUpShopTable::class);
        $this->call(PlatformPymentsTable::class);
    }
}
