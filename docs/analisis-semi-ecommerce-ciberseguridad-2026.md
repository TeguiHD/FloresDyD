# Análisis del proyecto Flores D&D y roadmap de funcionalidades (semi e-commerce)

## 1) Diagnóstico actual del proyecto

### Stack y base funcional
- El proyecto ya está orientado a **semi e-commerce** con Laravel + Livewire y enfoque en seguridad. 
- Hay rutas públicas para catálogo, checkout, tracking, páginas legales y panel administrativo.
- Existe esquema de base de datos para:
  - Pedidos con estados de pago manual (ej. `pending_payment`, `pending_review`, `payment_verified`).
  - Comprobantes de pago (`payment_proofs`) con hash único de archivo, metadatos y flujo de aprobación/rechazo.
  - Cupones y registro de usos (`coupons`, `coupon_usages`).

### Hallazgos clave (gaps)
- El checkout visual y lógico está parcialmente implementado, pero hoy:
  - La aplicación de cupón en checkout aún es un placeholder.
  - La creación de pedido aún es placeholder.
  - La UI de pago menciona tarjeta/Stripe aunque el modelo objetivo es transferencia con comprobante.
- Existen servicios de seguridad (`FraudDetectionService`, `CouponService`) con buena base, pero no están integrados end-to-end en un flujo transaccional robusto.
- Existen controles de cabeceras seguras y honeypots, pero faltan controles más estrictos para el flujo crítico de comprobantes y cupones (atomicidad, antifraude documental, anti-automatización adaptativa).

---

## 2) Funcionalidades que debe tener un semi e-commerce como este (prioridad negocio + riesgo)

## P0 (crítico para operar seguro)
1. **Flujo completo de pedido sin pasarela**:
   - Reserva temporal de stock al confirmar checkout.
   - Instrucciones de pago con ID de pedido y ventana de tiempo (ej. 30–60 min).
   - Carga de comprobante por el cliente con validaciones estrictas.
   - Estado automático: `pending_payment` → `pending_review` → `payment_verified|rejected`.

2. **Conciliación operativa y antifraude**:
   - Backoffice para revisar comprobantes con semáforo de riesgo y checklist.
   - Historial inmutable de decisiones de verificación (quién, cuándo, por qué).
   - Rechazo con motivo estructurado + reintento limitado.

3. **Cupones ciberseguros en BD y servidor**:
   - Validación exclusivamente server-side (nunca confiar en frontend).
   - Revalidación de cupón al crear pedido (no solo al “aplicar”).
   - Consumo de cupón dentro de transacción DB y bloqueo pesimista para evitar doble uso concurrente.

## P1 (escala y conversión)
4. **Disponibilidad de entrega real**:
   - Matriz por comuna/CP, franjas horarias y capacidad diaria.
   - Cierre automático de slots agotados.

5. **Notificaciones transaccionales**:
   - Email/WhatsApp de: pedido creado, comprobante recibido, comprobante rechazado/aprobado, estado logístico.

6. **Autoservicio cliente**:
   - Seguimiento de pedido por código seguro + OTP/email magic link.

## P2 (madurez)
7. **Motor de reglas antifraude configurable**.
8. **Dashboard de fraude/cupones con métricas (intentos, tasa de rechazo, abuso por IP/device)**.
9. **Runbooks de incidentes y continuidad operativa.**

---

## 3) Flujo recomendado UX/UI impecable para pago manual

1. **Checkout**
   - Paso “Disponibilidad” antes de pago: valida zona + fecha + franja.
   - Paso “Confirmación”: muestra monto final, referencia única de pago y cuenta bancaria oficial.
   - CTA claro: “Ya transferí, subir comprobante”.

2. **Subida de comprobante**
   - Drag & drop + mobile camera.
   - Feedback inmediato: tipo, tamaño, calidad mínima, fecha visible, monto legible.
   - Campos obligatorios: banco origen, código transacción, monto declarado, fecha/hora.

3. **Post-subida**
   - Ticket de recepción con SLA (ej. “validamos en ≤ 15 min horario hábil”).
   - Estado visible y actualizado en tracking.

4. **En caso de rechazo**
   - Mensaje accionable (“No se ve número de operación”, “monto no coincide”).
   - Reintento controlado (máx N intentos; luego revisión manual reforzada).

---

## 4) Ciberseguridad para comprobantes (anti comprobante falso / imagen irrelevante)

## Validaciones de ingreso (antes de guardar)
- **MIME real + magic bytes** (no confiar en extensión).
- **Límites estrictos** (peso, dimensiones, formatos permitidos).
- **Re-encode server-side** (normalizar imagen y remover payloads ocultos).
- **Escaneo antimalware** (pipeline asíncrono con cuarentena).
- **Hash SHA-256** para detectar duplicados exactos y patrones de replay.

## Validaciones semánticas (fraude documental)
- OCR + heurísticas:
  - Detectar presencia de campos esperados (monto, fecha, banco, ID transacción).
  - Similaridad entre `declared_amount` y monto OCR.
  - Fecha de transacción dentro de ventana válida.
  - Búsqueda de inconsistencias visuales (edición burda, overlays, baja confiabilidad OCR).
- Reglas antifraude:
  - Mismo comprobante (hash) usado en múltiples pedidos.
  - Mismo código transacción reutilizado.
  - Deltas sospechosos entre importe pedido y declarado.

## Seguridad de almacenamiento y acceso
- Guardar en **bucket privado** (no público), acceso por URL firmada de corta vida.
- Cifrado en reposo + rotación de claves.
- Metadatos sensibles minimizados.
- Política de retención y borrado seguro.

## Controles de abuso
- Rate limit por IP + cuenta + fingerprint.
- CAPTCHA adaptativo solo cuando sube riesgo.
- Bloqueo progresivo por intentos fallidos/rechazos reiterados.

---

## 5) Lógica cibersegura de cupones (DB-first, anti-manipulación de requests)

## Principios
- El frontend **solo muestra** estimaciones; el backend decide el descuento final.
- El servidor recalcula totales desde catálogo/DB al confirmar pedido.
- Nunca aceptar `discount`, `total` ni `coupon_value` enviados por cliente.

## Validación robusta de cupón (servidor)
1. Normalizar código (`sanitizeCode`).
2. Buscar cupón activo y vigente en BD.
3. Verificar restricciones:
   - Ventana temporal.
   - `max_uses` global.
   - `max_uses_per_user`.
   - mínimo de compra.
   - aplicabilidad por producto/categoría/usuario.
4. Calcular descuento en centavos y tope de descuento.
5. En `DB::transaction`:
   - `SELECT ... FOR UPDATE` al cupón.
   - Revalidar contador.
   - Incrementar `uses_count` + insertar `coupon_usages` idempotente.

## Anti-tampering API
- Firmar cotización de checkout con `HMAC` y TTL corto (ej. 5 min).
- Verificar firma + versión de carrito al confirmar.
- Idempotency-Key por intento de creación de pedido.

## Panel admin de cupones (botón/ajustes)
- Crear/editar/activar/desactivar cupón.
- Simulador de impacto antes de publicar.
- Interruptor “solo primera compra”, cupón por segmento, límite diario.
- Auditoría completa de cambios (quién, cuándo, diff).

---

## 6) Arquitectura y calidad para escalabilidad/mantenibilidad

- Separar casos de uso:
  - `CreateOrderAction`, `UploadPaymentProofAction`, `ValidateCouponAction`, `VerifyPaymentProofAction`.
- DTOs + Value Objects para dinero, cupón, estado de pedido.
- Eventos de dominio (`OrderCreated`, `PaymentProofUploaded`, `CouponApplied`) + listeners.
- Cola para tareas pesadas (OCR, antivirus, análisis de riesgo).
- Contratos/interfaces para proveedores externos (OCR, malware scan, mensajería).
- Pruebas:
  - Unitarias (cálculo de descuentos, reglas de fraude).
  - Integración (transacciones y concurrencia de cupones).
  - E2E (checkout→comprobante→aprobación).
- Observabilidad:
  - Logs estructurados con correlation-id.
  - Métricas de fraude, cupones, SLA de revisión.

---

## 7) Alineación con referentes (vigencia recomendada para febrero 2026)

> Nota operativa: para febrero 2026, usar siempre la **versión más reciente vigente** de cada estándar al implementar.

- **OWASP**:
  - Top 10 (web), ASVS (nivel 2 mínimo), Cheat Sheets (File Upload, Input Validation, AuthN/AuthZ, Logging).
  - API Security Top 10 para endpoints de checkout/cupones.
- **NIST**:
  - SP 800-63B (autenticación e identidad digital).
  - SP 800-53 Rev.5 (catálogo de controles, especialmente SI, AU, AC, SC).
  - SP 800-61 (respuesta a incidentes).
  - SSDF (SP 800-218) para ciclo de desarrollo seguro.
- **Otros referentes**:
  - CIS Controls v8 (priorizar controles IG1/IG2).
  - Guías de fraude documental y KYC-lite para validaciones de comprobantes.

---

## 8) Plan de implementación por fases (recomendado)

## Fase 1 (2-3 semanas)
- Cerrar flujo real de checkout sin pasarela.
- Subida segura de comprobantes + almacenamiento privado + hash + validaciones base.
- Cupón server-side transaccional + endpoint idempotente.

## Fase 2 (2-4 semanas)
- OCR + reglas de fraude documental.
- Backoffice de revisión con checklist y decisiones auditables.
- Dashboard de riesgo y abuso de cupones.

## Fase 3 (continuo)
- Hardening continuo, pentesting periódico, tabletop IR.
- SLO/SLA de revisión y métricas de conversión/abuso.

---

## 9) Checklist técnico mínimo (go-live)

- [ ] Ningún monto/descuento confiado al cliente.
- [ ] Todo cupón validado y consumido en transacción con bloqueo.
- [ ] Comprobantes fuera de acceso público y con URL firmada temporal.
- [ ] Antivirus + validación de tipo real + hash de duplicados.
- [ ] Auditoría y trazabilidad de decisiones administrativas.
- [ ] Rate limiting multicapa + protección anti-bot adaptativa.
- [ ] Pruebas de concurrencia de cupones y abuso de endpoints.
- [ ] Runbook de incidentes de fraude y fuga de datos.

