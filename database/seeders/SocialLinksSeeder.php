<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SocialLink;

class SocialLinksSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            [
                'platform' => 'facebook',
                'url' => 'https://facebook.com/floresdyd',
                'icon' => 'fab fa-facebook-f',
                'sort_order' => 1,
                'is_active' => true,
                'open_in_new_tab' => true,
            ],
            [
                'platform' => 'instagram',
                'url' => 'https://instagram.com/floresdyd',
                'icon' => 'fab fa-instagram',
                'sort_order' => 2,
                'is_active' => true,
                'open_in_new_tab' => true,
            ],
            [
                'platform' => 'tiktok',
                'url' => 'https://tiktok.com/@floresdyd',
                'icon' => 'fab fa-tiktok',
                'sort_order' => 3,
                'is_active' => true,
                'open_in_new_tab' => true,
            ],
            [
                'platform' => 'whatsapp',
                'url' => 'https://wa.me/521234567890',
                'icon' => 'fab fa-whatsapp',
                'sort_order' => 4,
                'is_active' => true,
                'open_in_new_tab' => true,
            ],
        ];

        foreach ($links as $link) {
            SocialLink::create($link);
        }
    }
}
