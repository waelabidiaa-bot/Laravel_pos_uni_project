<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Drinks
            [
                'name' => 'Coca-Cola 1L',
                'barcode' => '6191000000001',
                'price' => 3.500,
                'purchase_price' => 2.400,
                'category' => 'Drinks',
                'quantity' => 30,
            ],
            [
                'name' => 'Fanta Orange 1L',
                'barcode' => '6191000000002',
                'price' => 3.200,
                'purchase_price' => 2.200,
                'category' => 'Drinks',
                'quantity' => 25,
            ],
            [
                'name' => 'Bottled Water 1.5L',
                'barcode' => '6191000000003',
                'price' => 1.200,
                'purchase_price' => 0.700,
                'category' => 'Drinks',
                'quantity' => 50,
            ],
            [
                'name' => 'Orange Juice 1L',
                'barcode' => '6191000000004',
                'price' => 4.500,
                'purchase_price' => 3.200,
                'category' => 'Drinks',
                'quantity' => 20,
            ],
            [
                'name' => 'Energy Drink',
                'barcode' => '6191000000005',
                'price' => 4.000,
                'purchase_price' => 2.800,
                'category' => 'Drinks',
                'quantity' => 15,
            ],
            [
                'name' => 'Iced Tea 500ml',
                'barcode' => '6191000000006',
                'price' => 2.500,
                'purchase_price' => 1.600,
                'category' => 'Drinks',
                'quantity' => 18,
            ],
            [
                'name' => 'Lemon Soda',
                'barcode' => '6191000000007',
                'price' => 2.800,
                'purchase_price' => 1.900,
                'category' => 'Drinks',
                'quantity' => 22,
            ],
            [
                'name' => 'Apple Juice 200ml',
                'barcode' => '6191000000008',
                'price' => 1.500,
                'purchase_price' => 0.900,
                'category' => 'Drinks',
                'quantity' => 35,
            ],

            // Snacks
            [
                'name' => 'Potato Chips',
                'barcode' => '6191000000009',
                'price' => 2.000,
                'purchase_price' => 1.200,
                'category' => 'Snacks',
                'quantity' => 25,
            ],
            [
                'name' => 'Chocolate Bar',
                'barcode' => '6191000000010',
                'price' => 1.500,
                'purchase_price' => 0.900,
                'category' => 'Snacks',
                'quantity' => 40,
            ],
            [
                'name' => 'Chocolate Biscuits',
                'barcode' => '6191000000011',
                'price' => 2.200,
                'purchase_price' => 1.400,
                'category' => 'Snacks',
                'quantity' => 30,
            ],
            [
                'name' => 'Salted Peanuts',
                'barcode' => '6191000000012',
                'price' => 1.800,
                'purchase_price' => 1.100,
                'category' => 'Snacks',
                'quantity' => 20,
            ],
            [
                'name' => 'Vanilla Wafers',
                'barcode' => '6191000000013',
                'price' => 1.200,
                'purchase_price' => 0.700,
                'category' => 'Snacks',
                'quantity' => 35,
            ],
            [
                'name' => 'Caramel Candy',
                'barcode' => '6191000000014',
                'price' => 0.800,
                'purchase_price' => 0.400,
                'category' => 'Snacks',
                'quantity' => 50,
            ],
            [
                'name' => 'Popcorn',
                'barcode' => '6191000000015',
                'price' => 2.500,
                'purchase_price' => 1.500,
                'category' => 'Snacks',
                'quantity' => 15,
            ],
            [
                'name' => 'Vanilla Cake',
                'barcode' => '6191000000016',
                'price' => 3.000,
                'purchase_price' => 1.900,
                'category' => 'Snacks',
                'quantity' => 12,
            ],

            // Dairy
            [
                'name' => 'Milk 1L',
                'barcode' => '6191000000017',
                'price' => 1.800,
                'purchase_price' => 1.400,
                'category' => 'Dairy',
                'quantity' => 20,
            ],
            [
                'name' => 'Natural Yogurt',
                'barcode' => '6191000000018',
                'price' => 0.800,
                'purchase_price' => 0.550,
                'category' => 'Dairy',
                'quantity' => 30,
            ],
            [
                'name' => 'Cheese Slices',
                'barcode' => '6191000000019',
                'price' => 4.200,
                'purchase_price' => 3.000,
                'category' => 'Dairy',
                'quantity' => 15,
            ],
            [
                'name' => 'Butter 200g',
                'barcode' => '6191000000020',
                'price' => 3.500,
                'purchase_price' => 2.500,
                'category' => 'Dairy',
                'quantity' => 12,
            ],
            [
                'name' => 'Strawberry Yogurt',
                'barcode' => '6191000000021',
                'price' => 1.000,
                'purchase_price' => 0.650,
                'category' => 'Dairy',
                'quantity' => 25,
            ],
            [
                'name' => 'Cream Cheese',
                'barcode' => '6191000000022',
                'price' => 3.800,
                'purchase_price' => 2.600,
                'category' => 'Dairy',
                'quantity' => 10,
            ],
            [
                'name' => 'Chocolate Milk',
                'barcode' => '6191000000023',
                'price' => 1.500,
                'purchase_price' => 0.950,
                'category' => 'Dairy',
                'quantity' => 18,
            ],

            // Household
            [
                'name' => 'Dishwashing Liquid',
                'barcode' => '6191000000024',
                'price' => 4.500,
                'purchase_price' => 3.000,
                'category' => 'Household',
                'quantity' => 12,
            ],
            [
                'name' => 'Laundry Detergent',
                'barcode' => '6191000000025',
                'price' => 12.000,
                'purchase_price' => 9.000,
                'category' => 'Household',
                'quantity' => 8,
            ],
            [
                'name' => 'Paper Tissues',
                'barcode' => '6191000000026',
                'price' => 2.000,
                'purchase_price' => 1.200,
                'category' => 'Household',
                'quantity' => 30,
            ],
            [
                'name' => 'Kitchen Sponge',
                'barcode' => '6191000000027',
                'price' => 1.000,
                'purchase_price' => 0.500,
                'category' => 'Household',
                'quantity' => 25,
            ],
            [
                'name' => 'Floor Cleaner',
                'barcode' => '6191000000028',
                'price' => 5.500,
                'purchase_price' => 3.800,
                'category' => 'Household',
                'quantity' => 10,
            ],
            [
                'name' => 'Garbage Bags',
                'barcode' => '6191000000029',
                'price' => 3.000,
                'purchase_price' => 1.800,
                'category' => 'Household',
                'quantity' => 20,
            ],
            [
                'name' => 'Hand Soap',
                'barcode' => '6191000000030',
                'price' => 2.500,
                'purchase_price' => 1.500,
                'category' => 'Household',
                'quantity' => 24,
            ],
        ];

        foreach ($products as $data) {
            $category = Category::firstOrCreate([
                'name' => $data['category'],
            ]);

            $product = Product::updateOrCreate(
                ['barcode' => $data['barcode']],
                [
                    'name' => $data['name'],
                    'price' => $data['price'],
                    'purchase_price' => $data['purchase_price'],
                    'category_id' => $category->id,
                ]
            );

            Stock::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'quantity' => $data['quantity'],
                    'min_threshold' => 5,
                ]
            );
        }

        $this->command->info('30 test products and their stock records were created or updated.');
    }
}