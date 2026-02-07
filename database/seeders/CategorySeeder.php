<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * CategorySeeder - Estructura jerárquica de categorías
 *
 * 5 categorías padre con sus subcategorías:
 * Bouquets, Regalos, Eventos, Novios, Condolencias
 */
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Bouquets',
                'slug' => 'bouquets',
                'description' => 'Ramos artesanales con las flores más frescas de temporada. Cada bouquet es creado a mano con dedicación.',
                'icon' => '💐',
                'sort_order' => 1,
                'is_active' => true,
                'show_in_navbar' => true,
                'show_in_home' => true,
                'meta_title' => 'Bouquets y Ramos de Flores Frescas | Flores D&D',
                'meta_description' => 'Descubre nuestros bouquets artesanales: ramos primaverales, de rosas, tulipanes y girasoles. Entrega el mismo día.',
                'children' => [
                    ['name' => 'Ramos Primaverales', 'slug' => 'ramos-primaverales', 'description' => 'Ramos coloridos con flores de temporada primaveral', 'icon' => '🌷'],
                    ['name' => 'Ramos de Rosas', 'slug' => 'ramos-de-rosas', 'description' => 'Clásicos e infalibles ramos de rosas en todos los colores', 'icon' => '🌹'],
                    ['name' => 'Ramos de Tulipanes', 'slug' => 'ramos-de-tulipanes', 'description' => 'Elegantes ramos de tulipanes holandeses', 'icon' => '🌷'],
                    ['name' => 'Ramos de Girasoles', 'slug' => 'ramos-de-girasoles', 'description' => 'Alegres ramos de girasoles que iluminan cualquier día', 'icon' => '🌻'],
                ],
            ],
            [
                'name' => 'Regalos',
                'slug' => 'regalos',
                'description' => 'Arreglos florales perfectos para celebrar los momentos más importantes de la vida.',
                'icon' => '🎁',
                'sort_order' => 2,
                'is_active' => true,
                'show_in_navbar' => true,
                'show_in_home' => true,
                'meta_title' => 'Flores para Regalos - Nacimientos, Aniversarios, Cumpleaños | Flores D&D',
                'meta_description' => 'Regala flores frescas para nacimientos, aniversarios y cumpleaños. Entrega a domicilio el mismo día.',
                'children' => [
                    ['name' => 'Nacimientos', 'slug' => 'nacimientos', 'description' => 'Arreglos especiales para dar la bienvenida a un nuevo ser', 'icon' => '👶'],
                    ['name' => 'Aniversario', 'slug' => 'aniversario', 'description' => 'Flores románticas para celebrar su amor', 'icon' => '💕'],
                    ['name' => 'Cumpleaños', 'slug' => 'cumpleanos', 'description' => 'Arreglos vibrantes para hacer de su día algo especial', 'icon' => '🎂'],
                ],
            ],
            [
                'name' => 'Eventos',
                'slug' => 'eventos',
                'description' => 'Decoración floral profesional para eventos sociales y corporativos.',
                'icon' => '🎉',
                'sort_order' => 3,
                'is_active' => true,
                'show_in_navbar' => true,
                'show_in_home' => true,
                'meta_title' => 'Flores para Eventos - Centros de Mesa, Decoraciones | Flores D&D',
                'meta_description' => 'Decoración floral para eventos: centros de mesa, floreros, atriles y graduaciones. Servicio profesional.',
                'children' => [
                    ['name' => 'Centros de Mesa', 'slug' => 'centros-de-mesa', 'description' => 'Centros de mesa elegantes para todo tipo de eventos', 'icon' => '🏵️'],
                    ['name' => 'Floreros', 'slug' => 'floreros', 'description' => 'Arreglos en floreros de diseño exclusivo', 'icon' => '🏺'],
                    ['name' => 'Decoraciones', 'slug' => 'decoraciones', 'description' => 'Decoración floral integral para eventos', 'icon' => '✨'],
                    ['name' => 'Atriles', 'slug' => 'atriles-eventos', 'description' => 'Atriles florales para entradas y recepciones', 'icon' => '🌿'],
                    ['name' => 'Graduaciones', 'slug' => 'graduaciones', 'description' => 'Ramos y arreglos para celebrar logros académicos', 'icon' => '🎓'],
                ],
            ],
            [
                'name' => 'Novios',
                'slug' => 'novios',
                'description' => 'Decoración floral completa para bodas y ceremonias nupciales. Hacemos realidad la boda de tus sueños.',
                'icon' => '💒',
                'sort_order' => 4,
                'is_active' => true,
                'show_in_navbar' => true,
                'show_in_home' => true,
                'meta_title' => 'Flores para Bodas - Ramos de Novia, Decoración Nupcial | Flores D&D',
                'meta_description' => 'Todo para tu boda: ramos de novia, caminos de luz, aros y arcos de flores, decoración de altar. Servicio premium.',
                'children' => [
                    ['name' => 'Ramos de Novia', 'slug' => 'ramos-de-novia', 'description' => 'Ramos nupciales diseñados con amor para tu día especial', 'icon' => '💐'],
                    ['name' => 'Caminos de Luz', 'slug' => 'caminos-de-luz', 'description' => 'Caminos florales iluminados para la ceremonia', 'icon' => '🕯️'],
                    ['name' => 'Atriles Nupciales', 'slug' => 'atriles-nupciales', 'description' => 'Atriles decorados con flores para la ceremonia', 'icon' => '🌿'],
                    ['name' => 'Centros de Mesa Nupciales', 'slug' => 'centros-mesa-nupciales', 'description' => 'Centros de mesa románticos para la recepción', 'icon' => '🕊️'],
                    ['name' => 'Aros y Arcos de Flores', 'slug' => 'aros-arcos-flores', 'description' => 'Arcos y aros florales para la ceremonia y fotos', 'icon' => '🌸'],
                    ['name' => 'Decoración de Altar', 'slug' => 'decoracion-altar', 'description' => 'Decoración floral completa para el altar nupcial', 'icon' => '⛪'],
                ],
            ],
            [
                'name' => 'Condolencias',
                'slug' => 'condolencias',
                'description' => 'Arreglos fúnebres elaborados con respeto y sobriedad para expresar sus condolencias.',
                'icon' => '🕊️',
                'sort_order' => 5,
                'is_active' => true,
                'show_in_navbar' => true,
                'show_in_home' => true,
                'meta_title' => 'Arreglos de Condolencias - Cubre Urnas, Arreglos Fúnebres | Flores D&D',
                'meta_description' => 'Arreglos de condolencias: cubre urnas y arreglos frontales. Entrega con discreción y respeto.',
                'children' => [
                    ['name' => 'Cubre Urnas', 'slug' => 'cubre-urnas', 'description' => 'Arreglos solemnes para cubrir urnas con elegancia', 'icon' => '🤍'],
                    ['name' => 'Arreglos Frontales', 'slug' => 'arreglos-frontales', 'description' => 'Arreglos frontales para servicios fúnebres', 'icon' => '🕯️'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $parent = Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );

            foreach ($children as $childIndex => $childData) {
                Category::updateOrCreate(
                    ['slug' => $childData['slug']],
                    array_merge($childData, [
                        'parent_id' => $parent->id,
                        'sort_order' => $childIndex + 1,
                        'is_active' => true,
                        'show_in_navbar' => false,
                        'show_in_home' => false,
                        'meta_title' => $childData['name'] . ' | Flores D&D',
                        'meta_description' => $childData['description'],
                    ])
                );
            }
        }
    }
}
