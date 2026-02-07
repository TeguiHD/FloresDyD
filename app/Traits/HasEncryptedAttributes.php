<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

/**
 * Trait para manejar atributos cifrados en modelos Eloquent
 * Usa AES-256-GCM a través de Laravel Crypt
 */
trait HasEncryptedAttributes
{
    /**
     * Cifrar un valor para almacenamiento en BD
     */
    protected function encryptAttribute(mixed $value): ?string
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        try {
            return Crypt::encryptString((string) $value);
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    /**
     * Descifrar un valor de la BD
     */
    protected function decryptAttribute(?string $encryptedValue): ?string
    {
        if (is_null($encryptedValue) || $encryptedValue === '') {
            return null;
        }

        try {
            return Crypt::decryptString($encryptedValue);
        } catch (DecryptException $e) {
            // Log pero no exponer el error
            report($e);
            return null;
        }
    }

    /**
     * Boot del trait - cifrar automáticamente atributos al guardar
     */
    public static function bootHasEncryptedAttributes(): void
    {
        static::saving(function ($model) {
            if (property_exists($model, 'encryptedAttributes')) {
                foreach ($model->encryptedAttributes as $attribute) {
                    // Solo cifrar si el valor ha cambiado y no está ya cifrado
                    if ($model->isDirty($attribute) && !empty($model->attributes[$attribute])) {
                        // Verificar si ya está cifrado
                        try {
                            Crypt::decryptString($model->attributes[$attribute]);
                            // Si no lanza excepción, ya está cifrado
                        } catch (DecryptException $e) {
                            // No está cifrado, cifrarlo
                            $model->attributes[$attribute] = Crypt::encryptString($model->attributes[$attribute]);
                        }
                    }
                }
            }
        });
    }

    /**
     * Obtener valor descifrado de un atributo
     */
    public function getDecrypted(string $attribute): ?string
    {
        $encryptedAttribute = $attribute . '_encrypted';
        
        if (isset($this->attributes[$encryptedAttribute])) {
            return $this->decryptAttribute($this->attributes[$encryptedAttribute]);
        }
        
        if (isset($this->attributes[$attribute])) {
            return $this->decryptAttribute($this->attributes[$attribute]);
        }
        
        return null;
    }
}
