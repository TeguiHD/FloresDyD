<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Nuestros Servicios - Flores D&D')]
class Servicios extends Component
{
    public array $services = [
        [
            'title' => 'Arreglos Florales Personalizados',
            'description' => 'Diseñamos arreglos únicos según tus preferencias, ocasión y presupuesto. Cada creación es una obra de arte hecha especialmente para ti.',
            'icon' => 'sparkles',
            'features' => [
                'Consulta personalizada',
                'Diseño exclusivo',
                'Flores premium seleccionadas',
                'Tarjeta personalizada incluida',
            ],
        ],
        [
            'title' => 'Entrega a Domicilio',
            'description' => 'Llevamos tus flores frescas a cualquier dirección en nuestra zona de cobertura, con puntualidad y cuidado.',
            'icon' => 'truck',
            'features' => [
                'Entrega el mismo día',
                'Seguimiento en tiempo real',
                'Embalaje protector especial',
                'Horarios flexibles',
            ],
        ],
        [
            'title' => 'Decoración de Eventos',
            'description' => 'Transformamos tus eventos en experiencias memorables con decoración floral profesional.',
            'icon' => 'calendar',
            'features' => [
                'Bodas y XV años',
                'Eventos corporativos',
                'Baby showers',
                'Fiestas temáticas',
            ],
        ],
        [
            'title' => 'Suscripción de Flores',
            'description' => 'Recibe flores frescas periódicamente en tu hogar u oficina. Ideal para mantener espacios siempre floridos.',
            'icon' => 'arrow-path',
            'features' => [
                'Entregas semanales o quincenales',
                'Variedad de flores de temporada',
                'Descuentos exclusivos',
                'Cancela cuando quieras',
            ],
        ],
        [
            'title' => 'Regalos Corporativos',
            'description' => 'Soluciones florales para empresas: regalos para clientes, empleados o decoración de oficinas.',
            'icon' => 'briefcase',
            'features' => [
                'Facturación empresarial',
                'Descuentos por volumen',
                'Entregas programadas',
                'Branding personalizado',
            ],
        ],
        [
            'title' => 'Flores para Condolencias',
            'description' => 'Arreglos y coronas fúnebres con la sensibilidad y respeto que el momento requiere.',
            'icon' => 'heart',
            'features' => [
                'Entrega urgente disponible',
                'Variedad de arreglos',
                'Servicio discreto',
                'Tarjetas de condolencia',
            ],
        ],
    ];

    public function render()
    {
        return view('livewire.pages.servicios');
    }
}
