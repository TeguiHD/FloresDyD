# Análisis del proyecto y propuesta de evolución (semi e-commerce) — Febrero 2026

## 1) Diagnóstico rápido del estado actual

### Lo que ya está bien resuelto
- **Stack moderno y mantenible** con Laravel + Livewire + Tailwind, con separación razonable entre páginas públicas y panel admin.
- **Modelo de pedidos orientado a pago manual**, con estados como `pending_payment`, `pending_review`, `payment_verified`, etc., útil para flujo sin pasarela.
- **Diseño de base para comprobantes de pago** existente en BD (`payment_proofs`) con hash de archivo, metadatos y trazabilidad de verificación.
- **Controles iniciales de seguridad**: middleware de headers de seguridad, rate limiting contextual y módulo de fraude por scoring.
- **Base de cupones**: estructura de datos robusta (vigencia, usos, restricciones por usuario/producto/categoría).

### Brechas clave detectadas
- El checkout actual está en modo MVP y **no implementa aún**:
  - carga real de comprobante,
  - conciliación de disponibilidad en tiempo real,
  - validación robusta de cupones en servidor,
  - creación transaccional completa del pedido.
- Existen entidades y tablas para pagos manuales/fraude, pero falta cerrar el flujo end-to-end entre UI, dominio y backoffice.

---

## 2) Funcionalidades actuales (resumen funcional)

### Catálogo y experiencia pública
- Home, colección, categorías, detalle de producto, ocasiones, búsqueda, contacto y páginas legales.
- Carrito + checkout multi-step en frontend.

### Operación admin
- Dashboard, pedidos, productos, clientes, seguridad, auditoría, usuarios y ajustes.
- Falta una sección dedicada y explícita para **gestión de cupones** y para **revisión de comprobantes** con un UX de cola antifraude.

---

## 3) Funcionalidades extra que debería tener un semi e-commerce de este tipo

## A. Flujo “confirmar disponibilidad + comprobante” (sin pasarela)
1. **Pre-reserva de stock (TTL)**
   - Al confirmar carrito: reserva temporal por SKU (ej. 10–15 min).
   - Si no sube comprobante válido dentro de la ventana, la reserva expira y libera stock.

2. **Orden en dos fases**
   - Fase 1: `quote_created` / `awaiting_payment` (con total congelado, fecha/horario y costo de envío cerrados).
   - Fase 2: `payment_submitted` al subir comprobante.
   - Aprobación manual o asistida: `payment_verified` o `payment_rejected`.

3. **Comprobante obligatorio con campos estructurados**
   - Archivo + monto declarado + fecha/hora transferencia + banco origen + últimos dígitos cuenta origen.
   - Validación cruzada automática con reglas de negocio antes de enviar a revisión humana.

4. **SLA visible en UX**
   - Mostrar tiempo estimado de validación (ej. 5–30 min horario hábil).
   - Timeline del estado en “Rastrear pedido”.

## B. UI/UX impecable (checkout/pago manual)
1. **Step dedicado “Pago por transferencia”**
   - Instrucciones claras (monto exacto, banco, cuenta, titular, referencia obligatoria).
   - Botón “Copiar datos bancarios”.

2. **Carga de comprobante asistida**
   - Drag & drop + previsualización.
   - Validaciones en cliente (tamaño, tipo permitido, nitidez mínima) y en servidor (autoritativas).
   - Confirmación explícita: “Declaro que este comprobante corresponde a esta compra”.

3. **Errores accionables**
   - Mensajes concretos (p. ej. “La imagen no muestra texto legible, vuelve a subir una más nítida”).

4. **Confianza y antifraude visibles**
   - Banner discreto: “Por seguridad validamos cada pago y podemos solicitar información adicional”.

## C. Ciberseguridad anti-comprobante falso/imagen irrelevante

### 1) Validación de archivo en capas (defensa en profundidad)
- Aceptar solo MIME real validado por contenido (no por extensión).
- Re-codificar imagen en servidor (eliminar payloads/metadata peligrosa).
- Limitar peso, dimensiones y ratio extremo.
- Calcular **SHA-256** y bloquear duplicados sospechosos.
- Antivirus/antimalware del archivo en pipeline asíncrono.

### 2) Validación semántica del comprobante
- OCR para extraer: monto, fecha, banco, referencia/operación.
- Reglas de consistencia:
  - monto OCR ≈ total pedido (tolerancia configurable),
  - fecha transfer dentro de ventana válida,
  - banco declarado coincide con OCR (si aplica),
  - referencia no reutilizada en múltiples pedidos.
- Clasificador binario “parece comprobante bancario vs imagen irrelevante”.
- En riesgo alto: cola de revisión manual obligatoria.

### 3) Controles anti-abuso
- Rate limit por IP + fingerprint + usuario + dispositivo.
- Desafío adaptativo (captcha invisible o step-up) cuando sube el riesgo.
- Bloqueo temporal progresivo ante intentos repetidos fallidos.
- Auditoría inmutable de eventos críticos (subida, reemplazo, aprobación, rechazo).

### 4) Almacenamiento seguro
- Guardar comprobantes en bucket privado (no público), URL firmada corta para visualización admin.
- Cifrado en reposo + rotación de llaves gestionada.
- Retención mínima necesaria y política de borrado automático.

## D. Lógica cibersegura de cupones (crítica)

### Principios
- **El cliente nunca calcula descuento final de forma confiable**.
- Toda validación y cálculo se hace en backend en una transacción atómica.
- El servidor emite un `pricing_snapshot_signature` para amarrar carrito+cupón+envío+vigencia.

### Flujo recomendado
1. `POST /checkout/price-preview` con carrito + cupón.
2. Backend:
   - normaliza código,
   - busca cupón activo/vigente,
   - valida restricciones (uso global, por usuario, por segmento, primera compra, productos elegibles, exclusiones, monto mínimo),
   - calcula descuento exacto con límites.
3. Devuelve `quote_id` + `expires_at` + `server_totals` + `quote_signature` (HMAC/JWS).
4. `POST /orders` exige `quote_id` y `quote_signature` válidos y no expirados.
5. En commit de orden, incrementa uso de cupón con bloqueo pesimista u optimista para evitar carreras.

### Validaciones anti-manipulación imprescindibles
- Recalcular total en servidor justo antes de persistir orden.
- Verificar integridad de `quote_signature`.
- Invalidar quote si cambia carrito, dirección o método de entrega.
- Idempotency key en creación de pedido.
- No exponer campos internos sensibles del cupón al cliente.

## E. Panel de administración (botón/ajustes de cupones)
- Módulo dedicado “Cupones” con:
  - crear/editar/desactivar,
  - fechas y zona horaria,
  - límites de uso,
  - elegibilidad avanzada,
  - simulador de impacto.
- Auditoría completa de cambios (quién, qué, cuándo, IP/dispositivo).
- Botón de **“Pausar campaña ahora”** (kill switch).
- Dashboard anti-fraude de cupones: intentos fallidos, top IP, top usuarios, anomalías.

---

## 4) Recomendaciones alineadas a OWASP/NIST (estado de práctica Feb-2026)

> Nota: estas son líneas de trabajo prácticas mapeadas a marcos ampliamente adoptados (OWASP ASVS, OWASP Top 10, OWASP API Security Top 10, NIST CSF 2.0, NIST SP 800-63B, NIST SSDF SP 800-218).

1. **Arquitectura segura por defecto**
   - Threat modeling en flujo checkout/comprobante/cupón.
   - Controles por capas (cliente, API, dominio, almacenamiento, operación).

2. **Gestión de identidad y sesiones**
   - MFA para admin, políticas de sesión estrictas, revocación activa.
   - RBAC mínimo privilegio para revisión de pagos y administración de cupones.

3. **Protección de APIs y lógica de negocio**
   - Validación server-side estricta (allowlist), límites por endpoint, anti-automation.
   - Protección contra BOLA/BFLA en endpoints de órdenes y cupones.

4. **Integridad del software y SDLC**
   - SAST/DAST/SCA en CI, firma de artefactos, SBOM y gestión de vulnerabilidades.
   - Tests de seguridad automatizados para casos de manipulación de precios/cupones.

5. **Observabilidad y respuesta**
   - Logging estructurado con trazabilidad por `request_id`/`order_id`.
   - Runbooks de incidentes (fraude comprobantes, abuso cupones, account takeover).

6. **Privacidad y cumplimiento**
   - Minimización de datos personales en comprobantes.
   - Retención y borrado por política documentada.

---

## 5) Roadmap sugerido por fases

### Fase 1 (2–4 semanas) — “Cerrar huecos críticos”
- Implementar flujo completo de comprobante (upload seguro + revisión admin).
- Cupón 100% validado en backend + quote firmado + idempotencia.
- Endpoints de disponibilidad/envío con lógica real y límites de abuso.

### Fase 2 (4–8 semanas) — “Escalar de forma segura”
- OCR + reglas antifraude de comprobantes.
- Cola de moderación con score y priorización.
- Módulo admin de cupones con auditoría y kill switch.

### Fase 3 (continuo) — “Madurez operativa”
- Hardening CI/CD (SAST/DAST/SCA/SBOM).
- Métricas antifraude y tuning de reglas.
- Simulacros de incidente y mejora de runbooks.

---

## 6) Criterios de calidad para código y mantenibilidad

- Arquitectura por capas (UI/UseCase/Domain/Infra) para checkout y cupones.
- DTOs + Form Requests para validación de entrada.
- Servicios de dominio puros para pricing/coupons/fraud.
- Repositorios transaccionales para operaciones críticas.
- Eventos de dominio (OrderPlaced, PaymentProofSubmitted, CouponRedeemed).
- Pruebas mínimas:
  - unitarias de cálculo de precios/cupones,
  - integración de checkout y estados,
  - seguridad (manipulación de payload, replay, race conditions).
- Contratos API versionados para permitir evolución sin romper frontend.

---

## 7) KPIs recomendados

- % comprobantes aprobados automáticamente vs manual.
- Tiempo medio de verificación.
- Tasa de fraude detectado y falsos positivos.
- Tasa de error de cupón (usuario legítimo bloqueado).
- Conversión checkout y abandono en paso de pago.

---

## 8) Conclusión ejecutiva

El proyecto tiene una **base técnica sólida** para operar como semi e-commerce sin pasarela, pero aún necesita cerrar el flujo operativo crítico de **disponibilidad → pago manual con comprobante → verificación antifraude → despacho** y reforzar la lógica de cupones para que sea **totalmente server-driven y anti-manipulación**. Con el roadmap propuesto, se puede alcanzar una operación segura, escalable y mantenible en el corto plazo.
