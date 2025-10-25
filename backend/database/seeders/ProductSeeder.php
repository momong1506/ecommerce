<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Product Seeder
 *
 * Seeds the database with sample products for development and testing
 */
class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Wireless Bluetooth Headphones',
                'description' => 'Premium wireless headphones with active noise cancellation and 30-hour battery life. Perfect for music lovers and commuters.',
                'price' => 199.99,
                'image' => 'https://placehold.co/400x300/42b883/white?text=Headphones',
                'quantity' => 50,
                'is_available' => true,
            ],
            [
                'name' => 'Smartphone Pro X',
                'description' => '6.5" OLED display, 128GB storage, 5G capable. Capture stunning photos with triple camera system.',
                'price' => 899.99,
                'image' => 'https://placehold.co/400x300/42b883/white?text=Smartphone',
                'quantity' => 30,
                'is_available' => true,
            ],
            [
                'name' => 'Laptop Ultra 15',
                'description' => '15.6" laptop with Intel i7 processor, 16GB RAM, 512GB SSD. Ideal for work and entertainment.',
                'price' => 1299.99,
                'image' => 'https://placehold.co/400x300/42b883/white?text=Laptop',
                'quantity' => 20,
                'is_available' => true,
            ],
            [
                'name' => 'Smartwatch Series 5',
                'description' => 'Fitness tracker with heart rate monitor, GPS, and water resistance. Track your health 24/7.',
                'price' => 349.99,
                'image' => 'https://placehold.co/400x300/42b883/white?text=Smartwatch',
                'quantity' => 75,
                'is_available' => true,
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse with precision tracking and long battery life. Compatible with all devices.',
                'price' => 49.99,
                'image' => 'https://placehold.co/400x300/42b883/white?text=Mouse',
                'quantity' => 100,
                'is_available' => true,
            ],
            [
                'name' => 'Mechanical Keyboard RGB',
                'description' => 'Professional mechanical keyboard with customizable RGB lighting and tactile switches.',
                'price' => 129.99,
                'image' => 'https://placehold.co/400x300/42b883/white?text=Keyboard',
                'quantity' => 60,
                'is_available' => true,
            ],
            [
                'name' => '4K Webcam',
                'description' => 'Ultra HD 4K webcam with autofocus and built-in noise-cancelling microphone. Perfect for video calls.',
                'price' => 149.99,
                'image' => 'https://placehold.co/400x300/42b883/white?text=Webcam',
                'quantity' => 40,
                'is_available' => true,
            ],
            [
                'name' => 'Portable SSD 1TB',
                'description' => 'Ultra-fast portable SSD with 1TB storage. Transfer files at lightning speed with USB-C connectivity.',
                'price' => 179.99,
                'image' => 'https://placehold.co/400x300/42b883/white?text=SSD',
                'quantity' => 80,
                'is_available' => true,
            ],
            [
                'name' => 'Gaming Monitor 27"',
                'description' => '27" QHD gaming monitor with 144Hz refresh rate and 1ms response time. Experience smooth gameplay.',
                'price' => 449.99,
                'image' => 'https://placehold.co/400x300/42b883/white?text=Monitor',
                'quantity' => 25,
                'is_available' => true,
            ],
            [
                'name' => 'Wireless Charging Pad',
                'description' => 'Fast wireless charging pad compatible with all Qi-enabled devices. Sleek and compact design.',
                'price' => 39.99,
                'image' => 'https://placehold.co/400x300/42b883/white?text=Charger',
                'quantity' => 150,
                'is_available' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
