<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Card;
use App\Models\CardLink;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Cache object Category
     */
    protected array $categories = [];

    /**
     * Cache object Card
     */
    protected array $cards = [];

    public function run(): void
    {
        $data = require database_path('seeders/data/demo.php');

        $this->seedSettings($data['settings']);
        $this->seedCategories($data['categories']);
        $this->seedCards($data['cards']);
        $this->seedLinks($data['links']);
    }

    protected function seedSettings(array $settings): void
    {
        foreach ($settings as $key => $value) {

            Setting::updateOrCreate(

                [
                    'key' => $key,
                ],

                [
                    'value' => $value,
                ]

            );
        }
    }

    protected function seedCategories(array $categories): void
    {
        foreach ($categories as $slug => $category) {

            $model = Category::updateOrCreate(

                [
                    'slug' => $slug,
                ],

                $category

            );

            $this->categories[$slug] = $model;
        }
    }

    protected function seedCards(array $cards): void
    {
        foreach ($cards as $key => $card) {

            $category = $this->categories[$card['category']];

            $model = Card::updateOrCreate(

                [
                    'category_id' => $category->id,
                    'title' => $card['title'],
                ],

                [
                    'category_id' => $category->id,
                    'title' => $card['title'],
                    'description' => $card['description'],
                    'badge' => $card['badge'],
                    'sort_order' => $card['sort_order'],
                    'is_active' => $card['is_active'],
                    'expired_at' => $card['expired_at'],
                ]

            );

            $this->cards[$key] = $model;
        }
    }

    protected function seedLinks(array $links): void
    {
        foreach ($links as $link) {

            $card = $this->cards[$link['card']];

            CardLink::updateOrCreate(

                [
                    'card_id' => $card->id,
                    'title'   => $link['title'],
                ],

                [
                    'card_id'    => $card->id,
                    'title'      => $link['title'],
                    'subtitle'   => $link['subtitle'],
                    'url'        => $link['url'],
                    'icon'       => $link['icon'],
                    'sort_order' => $link['sort_order'],
                    'is_active'  => $link['is_active'],
                    'expired_at' => $link['expired_at'],
                ]

            );
        }
    }
}
