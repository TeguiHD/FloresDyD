@extends('layouts.app')

@section('title', 'Términos y Condiciones | Flores DyD')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#f8f6f2] via-white to-white legal-page">
    {{-- Header --}}
    <div class="legal-hero">
        <div class="legal-hero__inner max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <span class="legal-hero__badge">Condiciones de uso</span>
            <h1 class="legal-hero__title">Términos y Condiciones</h1>
            <p class="legal-hero__date">Última actualización: Febrero 2026</p>
            <p class="legal-hero__description">Estos términos regulan el uso de nuestro sitio web y la compra de productos en FloresDyD.cl, cumpliendo con la Ley 19.496 de Protección de los Derechos de los Consumidores.</p>
            
            <div class="legal-hero__summary">
                <p class="legal-hero__summary-title">En estos términos:</p>
                <ul class="legal-hero__summary-list">
                    <li>Uso del sitio y cuenta</li>
                    <li>Compras y pagos</li>
                    <li>Entregas y cambios</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="lg:grid lg:grid-cols-12 lg:gap-12">
            {{-- Sidebar Index - Desktop --}}
            <aside class="hidden lg:block lg:col-span-3">
                <nav class="legal-nav sticky top-24" x-data="{ activeSection: '' }" @scroll.window="
                    let sections = ['aceptacion', 'definiciones', 'registro', 'productos', 'precios', 'compra', 'pago', 'entrega', 'devoluciones', 'garantias', 'propiedad', 'responsabilidad', 'modificaciones', 'ley', 'contacto'];
                    for (let section of sections) {
                        let element = document.getElementById(section);
                        if (element) {
                            let rect = element.getBoundingClientRect();
                            if (rect.top <= 150 && rect.bottom >= 150) {
                                activeSection = section;
                                break;
                            }
                        }
                    }
                ">
                    <p class="px-3 py-2 text-sm font-semibold text-ink/70 uppercase tracking-wider">Contenido</p>
                    <a href="#aceptacion" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'aceptacion' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Aceptación de Términos
                    </a>
                    <a href="#definiciones" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'definiciones' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Definiciones
                    </a>
                    <a href="#registro" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'registro' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Registro y Cuenta
                    </a>
                    <a href="#productos" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'productos' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Productos y Servicios
                    </a>
                    <a href="#precios" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'precios' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Precios y Disponibilidad
                    </a>
                    <a href="#compra" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'compra' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Proceso de Compra
                    </a>
                    <a href="#pago" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'pago' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Métodos de Pago
                    </a>
                    <a href="#entrega" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'entrega' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Entrega y Despacho
                    </a>
                    <a href="#devoluciones" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'devoluciones' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Devoluciones y Cambios
                    </a>
                    <a href="#garantias" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'garantias' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Garantías Legales
                    </a>
                    <a href="#propiedad" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'propiedad' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Propiedad Intelectual
                    </a>
                    <a href="#responsabilidad" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'responsabilidad' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Limitación de Responsabilidad
                    </a>
                    <a href="#modificaciones" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'modificaciones' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Modificaciones
                    </a>
                    <a href="#ley" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'ley' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Ley Aplicable
                    </a>
                    <a href="#contacto" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'contacto' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Contacto
                    </a>
                </nav>
            </aside>

            {{-- Mobile Index --}}
            <div class="lg:hidden mb-8">
                <details class="legal-mobile-index">
                    <summary class="px-4 py-3 font-medium text-ink cursor-pointer hover:bg-mist/50 transition-colors rounded-lg">
                        <span class="flex items-center justify-between">
                            <span>Índice de Contenidos</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                    </summary>
                    <nav class="px-4 pb-4 pt-2 space-y-1">
                        <a href="#aceptacion" class="block py-2 text-sm text-ink/70 hover:text-primary">Aceptación de Términos</a>
                        <a href="#definiciones" class="block py-2 text-sm text-ink/70 hover:text-primary">Definiciones</a>
                        <a href="#registro" class="block py-2 text-sm text-ink/70 hover:text-primary">Registro y Cuenta</a>
                        <a href="#productos" class="block py-2 text-sm text-ink/70 hover:text-primary">Productos y Servicios</a>
                        <a href="#precios" class="block py-2 text-sm text-ink/70 hover:text-primary">Precios y Disponibilidad</a>
                        <a href="#compra" class="block py-2 text-sm text-ink/70 hover:text-primary">Proceso de Compra</a>
                        <a href="#pago" class="block py-2 text-sm text-ink/70 hover:text-primary">Métodos de Pago</a>
                        <a href="#entrega" class="block py-2 text-sm text-ink/70 hover:text-primary">Entrega y Despacho</a>
                        <a href="#devoluciones" class="block py-2 text-sm text-ink/70 hover:text-primary">Devoluciones y Cambios</a>
                        <a href="#garantias" class="block py-2 text-sm text-ink/70 hover:text-primary">Garantías Legales</a>
                        <a href="#propiedad" class="block py-2 text-sm text-ink/70 hover:text-primary">Propiedad Intelectual</a>
                        <a href="#responsabilidad" class="block py-2 text-sm text-ink/70 hover:text-primary">Limitación de Responsabilidad</a>
                        <a href="#modificaciones" class="block py-2 text-sm text-ink/70 hover:text-primary">Modificaciones</a>
                        <a href="#ley" class="block py-2 text-sm text-ink/70 hover:text-primary">Ley Aplicable</a>
                        <a href="#contacto" class="block py-2 text-sm text-ink/70 hover:text-primary">Contacto</a>
                    </nav>
                </details>
            </div>

            {{-- Main Content --}}
            <main class="lg:col-span-9">
                <div class="legal-content">
                    <div class="prose prose-lg max-w-none p-6 md:p-8 lg:p-12">
                        
                        <section id="aceptacion" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">1. Aceptación de Términos</h2>
                            <p class="text-ink/80 leading-relaxed">
                                Al acceder y utilizar el sitio web FloresDyD.cl (en adelante, el "Sitio"), así como al realizar cualquier compra a través del mismo, aceptas quedar vinculado por estos Términos y Condiciones, todas las leyes y regulaciones aplicables, y aceptas que eres responsable del cumplimiento de las leyes locales aplicables.
                            </p>
                            <p class="text-ink/80 leading-relaxed">
                                Si no estás de acuerdo con alguno de estos términos, tienes prohibido usar o acceder a este sitio. Los materiales contenidos en este sitio web están protegidos por las leyes de propiedad intelectual y marcas comerciales aplicables.
                            </p>
                        </section>

                        <section id="definiciones" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">2. Definiciones</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">Para efectos de estos términos, se entenderá por:</p>
                            <div class="space-y-3">
                                <div class="bg-mist/30 rounded-lg p-4 border-l-4 border-primary">
                                    <p class="text-ink/80"><strong>"Usuario"</strong> o <strong>"Cliente":</strong> Toda persona natural o jurídica que acceda y utilice el Sitio.</p>
                                </div>
                                <div class="bg-mist/30 rounded-lg p-4 border-l-4 border-rose">
                                    <p class="text-ink/80"><strong>"Producto":</strong> Arreglos florales, flores, plantas y artículos relacionados ofrecidos en el Sitio.</p>
                                </div>
                                <div class="bg-mist/30 rounded-lg p-4 border-l-4 border-sage">
                                    <p class="text-ink/80"><strong>"Pedido":</strong> Solicitud de compra realizada por el Usuario a través del Sitio.</p>
                                </div>
                                <div class="bg-mist/30 rounded-lg p-4 border-l-4 border-gold">
                                    <p class="text-ink/80"><strong>"Contrato":</strong> Acuerdo de compraventa perfeccionado entre Flores DyD y el Usuario.</p>
                                </div>
                            </div>
                        </section>

                        <section id="registro" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">3. Registro y Cuenta de Usuario</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Para realizar compras en nuestro Sitio, debes crear una cuenta de usuario proporcionando información veraz, exacta, actual y completa. Es tu responsabilidad:
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-ink/80 mb-4">
                                <li>Mantener la confidencialidad de tu contraseña</li>
                                <li>Notificar inmediatamente cualquier uso no autorizado de tu cuenta</li>
                                <li>Actualizar tu información personal cuando sea necesario</li>
                                <li>Asegurarte de que la información de entrega sea correcta</li>
                            </ul>
                            <div class="bg-rose/5 border border-rose/20 rounded-lg p-4">
                                <p class="text-ink/80">
                                    <strong>Importante:</strong> Eres responsable de todas las actividades que ocurran bajo tu cuenta. Flores DyD no será responsable por pérdidas derivadas del uso no autorizado de tu contraseña.
                                </p>
                            </div>
                        </section>

                        <section id="productos" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">4. Productos y Servicios</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Flores DyD ofrece la venta de arreglos florales, flores frescas, plantas y productos relacionados. Al tratarse de productos naturales:
                            </p>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="bg-white border border-ink/10 rounded-lg p-5 shadow-sm">
                                    <h4 class="font-semibold text-ink mb-2 flex items-center">
                                        <span class="w-8 h-8 bg-sage/10 rounded-full flex items-center justify-center mr-2 text-sage">✓</span>
                                        Naturaleza del Producto
                                    </h4>
                                    <p class="text-sm text-ink/70">Los productos son perecederos y únicos. Pueden existir variaciones naturales en color, tamaño y apariencia.</p>
                                </div>
                                <div class="bg-white border border-ink/10 rounded-lg p-5 shadow-sm">
                                    <h4 class="font-semibold text-ink mb-2 flex items-center">
                                        <span class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center mr-2 text-primary">✓</span>
                                        Sustituciones
                                    </h4>
                                    <p class="text-sm text-ink/70">Nos reservamos el derecho de sustituir flores por otras de igual o mayor valor si las originales no están disponibles.</p>
                                </div>
                                <div class="bg-white border border-ink/10 rounded-lg p-5 shadow-sm">
                                    <h4 class="font-semibold text-ink mb-2 flex items-center">
                                        <span class="w-8 h-8 bg-rose/10 rounded-full flex items-center justify-center mr-2 text-rose">✓</span>
                                        Imágenes Referenciales
                                    </h4>
                                    <p class="text-sm text-ink/70">Las fotografías son ilustrativas. El producto final puede presentar variaciones por tratarse de elementos naturales.</p>
                                </div>
                                <div class="bg-white border border-ink/10 rounded-lg p-5 shadow-sm">
                                    <h4 class="font-semibold text-ink mb-2 flex items-center">
                                        <span class="w-8 h-8 bg-gold/10 rounded-full flex items-center justify-center mr-2 text-gold">✓</span>
                                        Calidad Garantizada
                                    </h4>
                                    <p class="text-sm text-ink/70">Todos nuestros productos son frescos y de alta calidad, preparados con cuidado por floristas profesionales.</p>
                                </div>
                            </div>
                        </section>

                        <section id="precios" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">5. Precios y Disponibilidad</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Los precios de los productos están expresados en pesos chilenos (CLP) e incluyen IVA cuando corresponda:
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-ink/80 mb-4">
                                <li>Los precios están sujetos a cambios sin previo aviso</li>
                                <li>El precio aplicable será el vigente al momento de realizar el pedido</li>
                                <li>Los costos de envío se calcularán según la zona de despacho</li>
                                <li>La disponibilidad de productos está sujeta a stock</li>
                                <li>Nos reservamos el derecho de limitar cantidades por pedido</li>
                            </ul>
                            <div class="bg-primary/5 border border-primary/20 rounded-lg p-4">
                                <p class="text-ink/80">
                                    <strong>Errores de Precio:</strong> Si detectamos un error en el precio de un producto después de recibir tu pedido, te contactaremos para ofrecerte la opción de confirmar la compra al precio correcto o cancelar el pedido sin cargo.
                                </p>
                            </div>
                        </section>

                        <section id="compra" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">6. Proceso de Compra</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                El proceso de compra consta de los siguientes pasos:
                            </p>
                            <div class="space-y-4">
                                <div class="flex items-start bg-gradient-to-r from-primary/5 to-transparent rounded-lg p-4 border-l-4 border-primary">
                                    <div class="flex-shrink-0 w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center font-bold mr-4">1</div>
                                    <div>
                                        <h4 class="font-semibold text-ink mb-1">Selección de Productos</h4>
                                        <p class="text-sm text-ink/70">Agrega los productos deseados a tu carrito de compras.</p>
                                    </div>
                                </div>
                                <div class="flex items-start bg-gradient-to-r from-rose/5 to-transparent rounded-lg p-4 border-l-4 border-rose">
                                    <div class="flex-shrink-0 w-10 h-10 bg-rose text-white rounded-full flex items-center justify-center font-bold mr-4">2</div>
                                    <div>
                                        <h4 class="font-semibold text-ink mb-1">Revisión del Carrito</h4>
                                        <p class="text-sm text-ink/70">Verifica los productos, cantidades y costos antes de proceder.</p>
                                    </div>
                                </div>
                                <div class="flex items-start bg-gradient-to-r from-sage/5 to-transparent rounded-lg p-4 border-l-4 border-sage">
                                    <div class="flex-shrink-0 w-10 h-10 bg-sage text-white rounded-full flex items-center justify-center font-bold mr-4">3</div>
                                    <div>
                                        <h4 class="font-semibold text-ink mb-1">Datos de Entrega</h4>
                                        <p class="text-sm text-ink/70">Proporciona la dirección de entrega y datos del destinatario.</p>
                                    </div>
                                </div>
                                <div class="flex items-start bg-gradient-to-r from-gold/5 to-transparent rounded-lg p-4 border-l-4 border-gold">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gold text-white rounded-full flex items-center justify-center font-bold mr-4">4</div>
                                    <div>
                                        <h4 class="font-semibold text-ink mb-1">Método de Pago</h4>
                                        <p class="text-sm text-ink/70">Selecciona y completa la información de pago.</p>
                                    </div>
                                </div>
                                <div class="flex items-start bg-gradient-to-r from-lilac/5 to-transparent rounded-lg p-4 border-l-4 border-lilac">
                                    <div class="flex-shrink-0 w-10 h-10 bg-lilac text-white rounded-full flex items-center justify-center font-bold mr-4">5</div>
                                    <div>
                                        <h4 class="font-semibold text-ink mb-1">Confirmación</h4>
                                        <p class="text-sm text-ink/70">Revisa y confirma tu pedido. Recibirás un email de confirmación.</p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-ink/80 leading-relaxed mt-4">
                                El contrato de compraventa se perfecciona cuando recibes la confirmación de pedido por correo electrónico, momento en el cual se considera aceptada tu oferta de compra.
                            </p>
                        </section>

                        <section id="pago" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">7. Métodos de Pago</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Aceptamos los siguientes métodos de pago:
                            </p>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="bg-gradient-to-br from-primary/5 to-primary/10 rounded-lg p-5 border border-primary/20">
                                    <h4 class="font-semibold text-ink mb-2">💳 Tarjetas de Crédito</h4>
                                    <p class="text-sm text-ink/70">Visa, Mastercard, American Express. Pagos procesados de forma segura.</p>
                                </div>
                                <div class="bg-gradient-to-br from-rose/5 to-rose/10 rounded-lg p-5 border border-rose/20">
                                    <h4 class="font-semibold text-ink mb-2">💳 Tarjetas de Débito</h4>
                                    <p class="text-sm text-ink/70">Débito con Redcompra. Transacciones inmediatas y seguras.</p>
                                </div>
                                <div class="bg-gradient-to-br from-sage/5 to-sage/10 rounded-lg p-5 border border-sage/20">
                                    <h4 class="font-semibold text-ink mb-2">📱 Transferencia Bancaria</h4>
                                    <p class="text-sm text-ink/70">Transferencia directa a nuestra cuenta. Pedido se procesa al confirmar pago.</p>
                                </div>
                                <div class="bg-gradient-to-br from-gold/5 to-gold/10 rounded-lg p-5 border border-gold/20">
                                    <h4 class="font-semibold text-ink mb-2">📲 Webpay Plus</h4>
                                    <p class="text-sm text-ink/70">Plataforma de pago segura de Transbank.</p>
                                </div>
                            </div>
                            <div class="mt-4 bg-ink/5 rounded-lg p-4 border border-ink/10">
                                <p class="text-ink/80 text-sm">
                                    <strong>Seguridad:</strong> Todos los pagos se procesan a través de plataformas seguras con certificación SSL. No almacenamos información completa de tarjetas de crédito en nuestros servidores.
                                </p>
                            </div>
                        </section>

                        <section id="entrega" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">8. Entrega y Despacho</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Realizamos entregas dentro del territorio nacional, sujetas a las siguientes condiciones:
                            </p>
                            
                            <h3 class="text-xl font-semibold text-ink mb-3">8.1 Zonas de Despacho</h3>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Consulta en nuestro sitio web las zonas de cobertura y costos de envío aplicables.
                            </p>

                            <h3 class="text-xl font-semibold text-ink mb-3">8.2 Plazos de Entrega</h3>
                            <ul class="list-disc list-inside space-y-2 text-ink/80 mb-4">
                                <li><strong>Entrega el mismo día:</strong> Pedidos realizados antes de las 14:00 hrs.</li>
                                <li><strong>Entrega programada:</strong> Selecciona fecha y rango horario preferido.</li>
                                <li><strong>Regiones:</strong> 2 a 4 días hábiles según destino.</li>
                            </ul>

                            <h3 class="text-xl font-semibold text-ink mb-3">8.3 Responsabilidad de Entrega</h3>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Es responsabilidad del Cliente proporcionar información correcta y completa de entrega:
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-ink/80 mb-4">
                                <li>Si no hay nadie en la dirección indicada, se dejará aviso para coordinar nueva entrega</li>
                                <li>Entregas fallidas por datos incorrectos pueden generar cargos adicionales</li>
                                <li>Para entregas sorpresa, asegúrate de que alguien pueda recibir el pedido</li>
                            </ul>

                            <div class="bg-rose/5 border border-rose/20 rounded-lg p-4">
                                <p class="text-ink/80">
                                    <strong>Productos Perecederos:</strong> Por tratarse de flores frescas, no se aceptarán reclamos por entregas rechazadas o no recibidas por ausencia del destinatario.
                                </p>
                            </div>
                        </section>

                        <section id="devoluciones" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">9. Devoluciones y Cambios</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                De acuerdo con la Ley 19.496 de Protección de los Derechos de los Consumidores:
                            </p>
                            
                            <h3 class="text-xl font-semibold text-ink mb-3">9.1 Derecho de Retracto (Compras Online)</h3>
                            <div class="bg-primary/5 border border-primary/20 rounded-lg p-5 mb-4">
                                <p class="text-ink/80 leading-relaxed mb-2">
                                    Tienes derecho a retractarte de tu compra dentro de <strong>10 días corridos</strong> desde la recepción del producto, sin expresar causa ni pagar penalización.
                                </p>
                                <p class="text-ink/80 leading-relaxed">
                                    <strong>Excepciones:</strong> Por la naturaleza perecedera de las flores frescas, el derecho de retracto no aplica una vez entregado el producto, salvo que presente defectos o no cumpla con lo ofrecido.
                                </p>
                            </div>

                            <h3 class="text-xl font-semibold text-ink mb-3">9.2 Cambios y Cancelaciones</h3>
                            <ul class="list-disc list-inside space-y-2 text-ink/80 mb-4">
                                <li><strong>Antes de la preparación:</strong> Puedes modificar o cancelar sin cargo.</li>
                                <li><strong>Durante la preparación:</strong> Contacta inmediatamente. Evaluaremos según el estado del pedido.</li>
                                <li><strong>Después del despacho:</strong> No se aceptan cambios ni cancelaciones.</li>
                            </ul>

                            <h3 class="text-xl font-semibold text-ink mb-3">9.3 Productos Defectuosos</h3>
                            <p class="text-ink/80 leading-relaxed">
                                Si recibes un producto con defectos, en mal estado o que no corresponde a lo solicitado, contáctanos dentro de las 24 horas siguientes a la recepción. Te ofreceremos reemplazo o reembolso completo.
                            </p>
                        </section>

                        <section id="garantias" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">10. Garantías Legales</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                De conformidad con los artículos 19 y siguientes de la Ley 19.496:
                            </p>
                            <div class="space-y-4">
                                <div class="bg-white border-l-4 border-primary rounded-lg p-5 shadow-sm">
                                    <h4 class="font-semibold text-ink mb-2">Garantía Legal</h4>
                                    <p class="text-ink/80 text-sm">
                                        Los productos deben ser aptos para el uso habitual y corresponder a la descripción. Ante fallas o vicios ocultos, puedes optar por: reparación gratuita, reposición o devolución de tu dinero, según corresponda.
                                    </p>
                                </div>
                                <div class="bg-white border-l-4 border-sage rounded-lg p-5 shadow-sm">
                                    <h4 class="font-semibold text-ink mb-2">Plazo de Garantía</h4>
                                    <p class="text-ink/80 text-sm">
                                        3 meses desde la recepción del producto. Para flores frescas, se garantiza la calidad y frescura al momento de la entrega y duración acorde a cuidados normales.
                                    </p>
                                </div>
                                <div class="bg-white border-l-4 border-rose rounded-lg p-5 shadow-sm">
                                    <h4 class="font-semibold text-ink mb-2">Exclusiones</h4>
                                    <p class="text-ink/80 text-sm">
                                        La garantía no cubre deterioro por mal uso, negligencia, accidentes o falta de cuidados básicos recomendados para productos naturales.
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section id="propiedad" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">11. Propiedad Intelectual</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Todo el contenido del Sitio, incluyendo pero no limitado a:
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-ink/80 mb-4">
                                <li>Textos, gráficos, logos, imágenes, fotografías</li>
                                <li>Diseños de arreglos florales</li>
                                <li>Software, código fuente y código objeto</li>
                                <li>Marcas registradas y nombres comerciales</li>
                            </ul>
                            <p class="text-ink/80 leading-relaxed">
                                Son propiedad exclusiva de Flores DyD o de sus licenciantes, y están protegidos por las leyes de propiedad intelectual chilenas e internacionales. Queda prohibida su reproducción, distribución o uso comercial sin autorización expresa.
                            </p>
                        </section>

                        <section id="responsabilidad" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">12. Limitación de Responsabilidad</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Flores DyD no será responsable por:
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-ink/80 mb-4">
                                <li>Daños indirectos, incidentales o consecuentes</li>
                                <li>Pérdida de datos, beneficios o uso del servicio</li>
                                <li>Interrupciones del servicio por mantenimiento o causas de fuerza mayor</li>
                                <li>Entregas fallidas por información incorrecta proporcionada por el Cliente</li>
                                <li>Reacciones alérgicas a flores o plantas</li>
                            </ul>
                            <p class="text-ink/80 leading-relaxed">
                                Nuestra responsabilidad máxima estará limitada al valor del producto adquirido.
                            </p>
                        </section>

                        <section id="modificaciones" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">13. Modificaciones de los Términos</h2>
                            <p class="text-ink/80 leading-relaxed">
                                Flores DyD se reserva el derecho de modificar estos Términos y Condiciones en cualquier momento. Las modificaciones serán efectivas inmediatamente después de su publicación en el Sitio. Es tu responsabilidad revisar periódicamente estos términos. El uso continuado del Sitio después de la publicación de cambios constituye tu aceptación de los mismos.
                            </p>
                        </section>

                        <section id="ley" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">14. Ley Aplicable y Jurisdicción</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Estos Términos y Condiciones se rigen por las leyes de la República de Chile, especialmente:
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-ink/80 mb-4">
                                <li><strong>Ley 19.496</strong> sobre Protección de los Derechos de los Consumidores</li>
                                <li><strong>Ley 19.628</strong> sobre Protección de la Vida Privada</li>
                                <li><strong>Código Civil</strong> y <strong>Código de Comercio</strong></li>
                            </ul>
                            <p class="text-ink/80 leading-relaxed">
                                Para cualquier controversia derivada de estos términos, las partes se someten a la jurisdicción de los tribunales ordinarios de justicia de Chile. Los consumidores podrán recurrir también al SERNAC o a los Juzgados de Policía Local correspondientes.
                            </p>
                        </section>

                        <section id="contacto" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">15. Contacto y Atención al Cliente</h2>
                            <p class="text-ink/80 leading-relaxed mb-6">
                                Para consultas, reclamos o ejercer tus derechos como consumidor, puedes contactarnos:
                            </p>
                            <div class="bg-gradient-to-br from-primary/5 to-lilac/5 rounded-lg p-6 border border-primary/20">
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <p class="text-sm text-ink/60 mb-1">Email de Atención</p>
                                        <a href="mailto:{{ \App\Models\SiteSetting::getValue('contact.email', config('flores.email')) }}" class="text-primary hover:underline font-medium">
                                            {{ \App\Models\SiteSetting::getValue('contact.email', config('flores.email')) }}
                                        </a>
                                    </div>
                                    <div>
                                        <p class="text-sm text-ink/60 mb-1">Teléfono</p>
                                        <a href="tel:{{ \App\Models\SiteSetting::getValue('contact.phone', config('flores.phone')) }}" class="text-primary hover:underline font-medium">
                                            {{ \App\Models\SiteSetting::getValue('contact.phone_display', config('flores.phone_display')) }}
                                        </a>
                                    </div>
                                    <div class="md:col-span-2">
                                        <p class="text-sm text-ink/60 mb-1">Dirección</p>
                                        <p class="text-ink/80">{{ \App\Models\SiteSetting::getValue('contact.address', config('flores.address')) }}</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <p class="text-sm text-ink/60 mb-1">Horario de Atención</p>
                                        <p class="text-ink/80">Lunes a Viernes: 9:00 - 18:00 hrs | Sábados: 9:00 - 14:00 hrs</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 bg-sage/5 border border-sage/20 rounded-lg p-4">
                                <p class="text-ink/80 text-sm">
                                    <strong>SERNAC:</strong> También puedes presentar reclamos ante el Servicio Nacional del Consumidor a través de <a href="https://www.sernac.cl" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline">www.sernac.cl</a> o llamando al 800 700 100.
                                </p>
                            </div>
                        </section>

                        <div class="mt-12 pt-8 border-t border-ink/10">
                            <p class="text-sm text-ink/60 text-center">
                                Estos Términos y Condiciones cumplen con la <strong>Ley 19.496 sobre Protección de los Derechos de los Consumidores</strong> y demás normativa chilena aplicable.
                            </p>
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<style>
    html {
        scroll-behavior: smooth;
    }

    .legal-page,
    .legal-page .prose {
        font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: #15131a;
    }

    .legal-hero {
        position: relative;
        background: linear-gradient(135deg, #5e3452 0%, #3d2640 50%, #221a28 100%);
        color: #ffffff;
        overflow: hidden;
    }

    .legal-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(800px circle at 50% 0%, rgba(255,255,255,0.12), transparent 60%);
        opacity: 0.8;
    }

    .legal-hero__inner {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .legal-hero__badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1.2rem;
        border-radius: 999px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        font-size: 0.75rem;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        font-weight: 500;
        color: rgba(255,255,255,0.9);
    }

    .legal-hero__title {
        margin-top: 1.5rem;
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 700;
        letter-spacing: -0.02em;
        color: #ffffff;
    }

    .legal-hero__date {
        margin-top: 0.75rem;
        font-size: 0.95rem;
        color: rgba(255,255,255,0.65);
        font-weight: 400;
    }

    .legal-hero__description {
        margin-top: 1.5rem;
        font-size: 1.1rem;
        line-height: 1.7;
        color: rgba(255,255,255,0.85);
        max-width: 42rem;
        margin-left: auto;
        margin-right: auto;
    }

    .legal-hero__summary {
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(255,255,255,0.15);
    }

    .legal-hero__summary-title {
        font-size: 0.85rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.6);
        margin-bottom: 1rem;
        font-weight: 500;
    }

    .legal-hero__summary-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: inline-flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        justify-content: center;
        font-size: 0.95rem;
        color: rgba(255,255,255,0.8);
    }

    .legal-hero__summary-list li {
        position: relative;
        padding-left: 1rem;
    }

    .legal-hero__summary-list li::before {
        content: "";
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: rgba(255,255,255,0.6);
    }

    .legal-nav {
        background: #ffffff;
        border-radius: 1.25rem;
        padding: 1.25rem 1.1rem;
        box-shadow: 0 20px 45px rgba(21, 19, 26, 0.08);
        border: 1px solid rgba(21, 19, 26, 0.08);
    }

    .legal-nav p {
        margin-bottom: 0.5rem;
        font-size: 0.75rem;
        letter-spacing: 0.2em;
    }

    .legal-nav a {
        border-radius: 0.75rem;
        padding: 0.55rem 0.75rem;
        display: block;
        color: rgba(21, 19, 26, 0.7);
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .legal-nav a:hover {
        color: #744568;
        background: rgba(116, 69, 104, 0.08);
    }

    .legal-content {
        background: #ffffff;
        border-radius: 1.5rem;
        border: 1px solid rgba(21, 19, 26, 0.08);
        box-shadow: 0 24px 60px rgba(21, 19, 26, 0.08);
        overflow: hidden;
    }

    .legal-mobile-index {
        background: #ffffff;
        border-radius: 1rem;
        border: 1px solid rgba(21, 19, 26, 0.08);
        box-shadow: 0 12px 30px rgba(21, 19, 26, 0.08);
    }

    .legal-mobile-index summary {
        padding: 0.85rem 1.1rem;
    }

    .prose h2 {
        color: #15131a;
        margin-top: 2rem;
        margin-bottom: 1rem;
        scroll-margin-top: 6rem;
        letter-spacing: -0.01em;
    }

    .prose h3 {
        color: #15131a;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }

    .prose h4 {
        color: #15131a;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }

    .prose p,
    .prose ul {
        margin-bottom: 1rem;
    }

    .prose strong {
        color: #15131a;
    }

    details summary::-webkit-details-marker {
        display: none;
    }

    details[open] summary svg {
        transform: rotate(180deg);
    }
</style>
@endsection
