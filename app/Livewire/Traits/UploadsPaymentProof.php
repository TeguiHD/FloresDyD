<?php

namespace App\Livewire\Traits;

use App\Models\PaymentProof;
use App\Services\AuditService;
use App\Services\FileSecurityService;
use App\Services\PaymentProofVerificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Trait compartido para upload de comprobantes de pago.
 *
 * Usado por CheckoutSuccess (guest + auth) y OrderShow (solo auth).
 * Integra FileSecurityService para validación de seguridad.
 *
 * Requiere que el componente Livewire tenga:
 * - use WithFileUploads
 * - public Order $order
 * - public $proofFile
 * - public ?int $declaredAmount
 * - public string $transactionCode
 * - public string $bankOrigin
 * - public string $transactionDate
 * - public bool $proofUploaded
 */
trait UploadsPaymentProof
{
    public function uploadProof(): void
    {
        // 1. Verificar status de la orden
        if (!in_array($this->order->status, ['pending_payment', 'pending_review'], true)) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Este pedido no acepta nuevos comprobantes',
            ]);
            return;
        }

        // 2. Rate limiting (por orden + IP/usuario)
        $rateSuffix = Auth::check() ? Auth::id() : request()->ip();
        $rateKey = 'proof:upload:' . $this->order->id . ':' . $rateSuffix;
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Demasiados intentos, intenta más tarde',
            ]);
            return;
        }
        RateLimiter::hit($rateKey, 300);

        // 3. Validación Laravel estándar
        $this->validate([
            'proofFile' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'declaredAmount' => 'nullable|integer|min:1',
            'transactionCode' => 'nullable|string|max:60',
            'bankOrigin' => 'nullable|string|max:60',
            'transactionDate' => 'nullable|date|before_or_equal:today',
        ]);

        // 4. Validación de seguridad profunda (MIME real, polyglot, dimensiones)
        $securityError = FileSecurityService::validate($this->proofFile);
        if ($securityError !== null) {
            $this->addError('proofFile', $securityError);
            return;
        }

        // 5. Límite de comprobantes por orden
        if ($this->order->paymentProofs()->count() >= 3) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Límite de comprobantes alcanzado (máximo 3)',
            ]);
            return;
        }

        // 6. Hash SHA-256 para detección de duplicados globales
        $hash = hash_file('sha256', $this->proofFile->getRealPath());
        if (PaymentProof::query()->where('file_hash', $hash)->exists()) {
            $this->addError('proofFile', 'Este comprobante ya fue utilizado');
            AuditService::securityEvent('payment_proof_duplicate', [
                'order_id' => $this->order->id,
                'ip' => request()->ip(),
            ]);
            return;
        }

        // 7. Limpiar metadata EXIF antes de almacenar
        FileSecurityService::stripExifMetadata($this->proofFile->getRealPath());

        // 8. Almacenar con UUID (previene colisiones y path traversal)
        $safeExtension = strtolower($this->proofFile->getClientOriginalExtension());
        $filename = Str::uuid()->toString() . '.' . $safeExtension;
        $path = $this->proofFile->storeAs(
            'payment-proofs/' . $this->order->order_number,
            $filename,
            'local'
        );

        // 9. Construir metadata con flags de riesgo
        $sanitizedName = FileSecurityService::sanitizeFilename(
            $this->proofFile->getClientOriginalName()
        );

        $metadata = [
            'extension' => $safeExtension,
            'client_mime' => $this->proofFile->getClientMimeType(),
            'size' => $this->proofFile->getSize(),
            'ip' => request()->ip(),
            'risk_flags' => [],
        ];

        if (!$this->declaredAmount) {
            $metadata['risk_flags'][] = 'missing_declared_amount';
        }
        if (!$this->transactionCode) {
            $metadata['risk_flags'][] = 'missing_transaction_code';
        }

        // 10. Validación de monto contra total de la orden
        if ($this->declaredAmount) {
            $delta = abs($this->declaredAmount - $this->order->total);
            $tolerance = max(50, (int) round($this->order->total * 0.01));
            if ($delta > $tolerance) {
                $metadata['risk_flags'][] = 'amount_mismatch';
                AuditService::securityEvent('payment_proof_amount_mismatch', [
                    'order_id' => $this->order->id,
                    'declared_amount' => $this->declaredAmount,
                    'order_total' => $this->order->total,
                    'delta' => $delta,
                ]);
            }
        }

        // 11. Crear registro en BD
        $proof = PaymentProof::create([
            'order_id' => $this->order->id,
            'uploaded_by' => Auth::id() ?? $this->order->user_id,
            'file_path' => $path,
            'original_filename' => $sanitizedName,
            'mime_type' => $this->proofFile->getClientMimeType(),
            'file_size' => $this->proofFile->getSize(),
            'file_hash' => $hash,
            'transaction_code_encrypted' => $this->transactionCode !== '' ? $this->transactionCode : null,
            'declared_amount' => $this->declaredAmount,
            'transaction_date' => $this->transactionDate ?: null,
            'bank_origin' => $this->bankOrigin !== '' ? $this->bankOrigin : null,
            'status' => 'pending',
            'file_metadata' => $metadata,
        ]);

        // 12. Análisis OCR (si está habilitado)
        $analysis = PaymentProofVerificationService::analyze($proof);
        $proof->file_metadata = array_merge($metadata, [
            'analysis_status' => $analysis['status'] ?? 'pending',
            'analysis_flags' => $analysis['flags'] ?? [],
            'ocr_text' => $analysis['ocr_text'] ?? null,
            'ocr_confidence' => $analysis['ocr_confidence'] ?? null,
            'extracted_amount' => $analysis['extracted_amount'] ?? null,
            'extracted_amount_raw' => $analysis['extracted_amount_raw'] ?? null,
            'extracted_date' => $analysis['extracted_date'] ?? null,
            'keyword_hits' => $analysis['keyword_hits'] ?? 0,
            'analysis_score' => $analysis['analysis_score'] ?? null,
            'bank_validation' => 'manual_review',
        ]);
        $proof->save();

        // 13. Cambiar estado de la orden
        if ($this->order->status === 'pending_payment') {
            $this->order->changeStatus(
                'pending_review',
                Auth::id(),
                'Comprobante subido por el cliente'
            );
        }

        // 14. Limpiar estado del componente
        $this->order->refresh();
        if (method_exists($this->order, 'load')) {
            $this->order->load(['items', 'paymentProofs', 'statusHistory']);
        }
        $this->proofUploaded = true;
        $this->reset(['proofFile', 'declaredAmount', 'transactionCode', 'bankOrigin', 'transactionDate']);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Comprobante enviado correctamente',
        ]);
    }
}
