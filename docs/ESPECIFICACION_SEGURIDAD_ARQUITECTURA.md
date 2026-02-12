# Flores D&D - Especificacion Profesional de Seguridad y Arquitectura (Feb 2026)

Documento base para implementar un semi e-commerce con validacion manual de pagos, enfoque en ciberseguridad y alta mantenibilidad. Esta guia traduce el analisis previo en un plan tecnico ejecutable, alineado a estandares actuales y a buenas practicas de ingenieria.

---

## 1. Objetivo y Alcance

Construir una plataforma de floristeria con checkout sin pasarela de pago:
- El cliente confirma disponibilidad, realiza transferencia y adjunta comprobante.
- El sistema valida el comprobante con controles antifraude y pasa a revision.
- La administracion aprueba o rechaza el pago con trazabilidad completa.

Este documento cubre:
- Arquitectura, seguridad y controles operativos.
- Flujos y reglas de negocio criticas.
- Estandares de referencia y mapeo de controles.
- Practicas de calidad, mantenibilidad y escalabilidad.

No cubre: integracion directa con pasarelas (se deja preparada).

---

## 2. Referentes y Estandares (alineacion Feb 2026)

Usar como base y checklist tecnico:
- OWASP Top 10:2025
- OWASP ASVS 5.0.0
- OWASP Cheat Sheet Series (Laravel/PHP, File Upload, Rate Limiting, etc.)
- NIST Cybersecurity Framework 2.0 (identificar, proteger, detectar, responder, recuperar)
- NIST SP 800-53 Rev.5 (Release 5.2.0) para controles de seguridad
- NIST SP 800-63-4 para identidad y autenticacion
- ISO/IEC 27001:2022 para gestion de seguridad de la informacion

Principio clave: **seguridad por diseno**, no como parche.

---

## 3. Arquitectura de Alto Nivel

Stack:
- Laravel 12 + Livewire 4
- MariaDB / MySQL
- Vite + Tailwind

Patrones y capas:
- Controllers/Livewire: solo orquestacion.
- Services: reglas de negocio (cupos, fraude, pagos).
- Repositories/Queries: acceso a datos (opcional, si la complejidad crece).
- Policies y Gates: autorizacion centralizada.

Componentes principales:
- Modulo de pedidos (Order, OrderItem, OrderStatusHistory).
- Modulo de comprobantes (PaymentProof).
- Modulo de cupones (Coupon, CouponUsage).
- Modulo de seguridad (AuditService, FraudDetectionService).

---

## 4. Modelo de Datos (resumen)

Tablas criticas:
- `orders`: estado, totales, datos cifrados, metadatos antifraude.
- `order_items`: snapshot del producto al comprar.
- `payment_proofs`: archivo, hash, metadata, status y verificacion.
- `coupons`, `coupon_usages`: reglas, limites, trazabilidad.
- `order_status_history`: timeline de estados.
- `audit_logs` (log externo): trazabilidad inmutable.

Reglas clave:
- Montos siempre en **centavos** (integers).
- Datos PII cifrados en reposo (AES-256-GCM via Laravel Crypt).
- Indices en `status`, `fraud_score`, `created_at`, `order_id`.

---

## 5. Flujo de Pedido (sin pasarela)

Estados:
1. `pending_payment` (pedido creado, esperando comprobante)
2. `pending_review` (comprobante recibido)
3. `payment_verified` (aprobado)
4. `preparing` -> `ready_for_delivery` -> `in_delivery` -> `delivered`
5. `cancelled` / `refunded`

Reglas:
- El pedido se crea validando disponibilidad (sin reserva estricta).
- El stock se descuenta al verificar el pago.
- El comprobante se sube dentro de un TTL (ej. 24h) o el pedido expira.
- Cada cambio de estado genera registro en `order_status_history`.

---

## 6. Comprobantes de Pago (validacion segura)

Objetivo: minimizar fraudes y pruebas falsas.

Validaciones obligatorias:
- Tipo archivo permitido: JPG/PNG/PDF.
- Tamano maximo (ej. 5MB).
- Hash SHA-256 para detectar duplicados.
- Metadata basica (EXIF si aplica) y coherencia temporal.
- Rate limiting por IP/usuario.

Ejemplo de flujo (Laravel/Livewire):
```php
public function uploadProof(): void
{
    $this->validate([
        'proofFile' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        'declaredAmount' => 'nullable|integer|min:1',
        'transactionCode' => 'nullable|string|max:60',
    ]);

    $hash = hash_file('sha256', $this->proofFile->getRealPath());
    if (PaymentProof::where('file_hash', $hash)->exists()) {
        $this->addError('proofFile', 'Comprobante duplicado');
        return;
    }

    $path = $this->proofFile->store('payment-proofs', 'local');

    PaymentProof::create([
        'order_id' => $this->order->id,
        'uploaded_by' => auth()->id(),
        'file_path' => $path,
        'original_filename' => $this->proofFile->getClientOriginalName(),
        'mime_type' => $this->proofFile->getClientMimeType(),
        'file_size' => $this->proofFile->getSize(),
        'file_hash' => $hash,
        'declared_amount' => $this->declaredAmount,
        'transaction_code_encrypted' => $this->transactionCode,
        'status' => 'pending',
    ]);

    $this->order->changeStatus('pending_review');
}
```

Buenas practicas adicionales:
- Escaneo antivirus (ClamAV o servicio externo).
- OCR (Tesseract o similar gratuito) para extraer monto, fecha y referencia.
- Validacion bancaria opcional (manual por la clienta si no hay integracion).
- Almacenamiento en disco privado (no publico).

Nota OCR:
- El OCR puede operar offline con Tesseract (gratis).
- Para PDFs, se puede convertir la primera pagina a imagen antes del OCR (ej. `pdftoppm`).

---

## 7. Cupones (ciberseguridad y consistencia)

Principios:
- La validacion SIEMPRE en el servidor (no confiar en el cliente).
- Codigo de cupon no debe cambiar totales por client-side.
- Bloquear modificaciones por payload o query tampering.
- Control de uso por usuario y uso total.

Reglas minimas:
- `is_active = true`
- `starts_at <= now <= expires_at`
- `max_uses` y `max_uses_per_user`
- `min_purchase_amount` y `max_discount_amount`
- Restricciones por producto/categoria

Ejemplo de validacion server-side:
```php
public function validateCoupon(string $input, int $subtotal, array $cart, ?int $userId): CouponResult
{
    $code = CouponService::sanitizeCode($input);
    $coupon = Coupon::where('code', $code)->where('is_active', true)->first();
    if (!$coupon) return CouponResult::invalid('Cupon no existe');

    if ($coupon->starts_at && $coupon->starts_at->isFuture()) return CouponResult::invalid('Cupon no disponible');
    if ($coupon->expires_at && $coupon->expires_at->isPast()) return CouponResult::invalid('Cupon expirado');

    if ($coupon->max_uses && $coupon->uses_count >= $coupon->max_uses) return CouponResult::invalid('Cupon agotado');
    if ($coupon->min_purchase_amount && $subtotal < $coupon->min_purchase_amount) return CouponResult::invalid('Monto minimo no alcanzado');

    // Validar uso por usuario
    if ($userId && $coupon->max_uses_per_user) {
        $uses = CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $userId)->count();
        if ($uses >= $coupon->max_uses_per_user) return CouponResult::invalid('Uso maximo alcanzado');
    }

    return CouponResult::ok($coupon);
}
```

---

## 8. Anti-fraude (scoring + acciones)

Scoring 0-100 con factores:
- Frecuencia de pedidos por IP/usuario.
- Monto inusual vs promedio.
- Email sospechoso / dominio temporal.
- Telefono invalido.
- User-Agent de bot.
- Duplicados de comprobante.

Acciones:
- Score >= 75: bloquear o revision exhaustiva.
- Score 50-74: revision manual.
- Score < 50: permitir flujo normal.

Requisitos:
- Guardar `fraud_score` y `fraud_factors` en `orders`.
- Auditoria en canal `security`.
- Rate limiting por endpoint critico.

---

## 9. Seguridad Aplicativa (controles)

Autenticacion:
- Argon2id con parametros robustos.
- Pepper en servidor (no en BD).
- Rate limiting de login.
- (Opcional) MFA para admins.

Autorizacion:
- RBAC con policies.
- Verificacion de propiedad en cada recurso.

Criptografia:
- PII cifrado en BD.
- Tokens firmados para enlaces sensibles.

Protecciones OWASP:
- A01 Broken Access Control: policies, ownership checks.
- A02 Cryptographic Failures: cifrado AES-256-GCM, manejo de claves.
- A03 Injection: validation + ORM (sin SQL raw con input).
- A04 Insecure Design: flujos con estados y controles de fraude.
- A05 Security Misconfiguration: headers, debug off, permisos.
- A06 Vulnerable Components: composer audit, actualizaciones.
- A07 Auth Failures: rate limit, MFA admin.
- A08 Integrity Failures: CSRF, firmas y hashing.
- A09 Logging Failures: audit + security logs.
- A10 SSRF: validacion estricta de URLs (si aplica).

Headers recomendados:
- CSP, HSTS, X-Frame-Options, X-Content-Type-Options, Referrer-Policy.

---

## 10. Observabilidad y Auditoria

Log de seguridad:
- Eventos de fraude, tampering, rate limit, honeypots.

Log de auditoria:
- Cambios admin (productos, pedidos, roles).
- Historial de estados de pedidos.

Buenas practicas:
- Retencion definida (90/365 dias).
- Enmascarar datos sensibles en logs.
- Hash chain para integridad (opcional).

---

## 11. Calidad, Mantenibilidad y Escalabilidad

Calidad:
- Validaciones en FormRequest o Livewire.
- Servicios con responsabilidad unica.
- DTOs para transferencia de datos complejos.
- Tests de dominio y de integracion (pago y cupones).

Escalabilidad:
- Jobs para tareas pesadas (OCR, antivirus, emails).
- Cache de catalogo y consultas costosas.
- Indices en columnas de alta frecuencia.

Mantenibilidad:
- Configuracion centralizada (config/).
- Feature flags para activar modulos gradualmente.
- Documentacion de flujos y reglas de negocio.

---

## 12. Roadmap por Fases

Fase 1 (MVP seguro):
- Checkout funcional con ordenes y comprobantes.
- Validacion de cupones server-side.
- Anti-fraude basico + logs.

Fase 2:
- OCR de comprobantes.
- Panel de revision avanzada.
- Notificaciones y SLA de revision.

Fase 3:
- Pasarela de pago hibrida (opcional).
- Analitica avanzada y deteccion de anomalias.

---

## 13. Checklist de Cumplimiento

- [ ] OWASP Top 10:2025 mapeado y verificado.
- [ ] ASVS 5.0.0 requerido (minimo L2 en areas criticas).
- [ ] NIST CSF 2.0 aplicado a procesos.
- [ ] NIST SP 800-53 controles base para app web.
- [ ] Logs y auditoria activas.
- [ ] Backups y plan de respuesta a incidentes.

---

## 14. Notas Finales

Este documento es el estandar base. Cada entrega debe:
- Mantener integridad de datos.
- Evitar confianza en el cliente.
- Registrar eventos clave.
- Reducir riesgo de fraude con controles defensivos.
