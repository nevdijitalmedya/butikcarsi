<?php
/**
 * Validator.php â€” Input validation utilities
 */

class Validator {
    private array $errors = [];

    public function required(string $field, mixed $value, string $label = ''): self {
        $label = $label ?: $field;
        if (empty($value) && $value !== '0') {
            $this->errors[$field] = "$label alanÄ± zorunludur.";
        }
        return $this;
    }

    public function email(string $field, mixed $value, string $label = ''): self {
        $label = $label ?: $field;
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "$label geÃ§erli bir e-posta adresi olmalÄ±dÄ±r.";
        }
        return $this;
    }

    public function minLength(string $field, mixed $value, int $min, string $label = ''): self {
        $label = $label ?: $field;
        if (!empty($value) && mb_strlen($value) < $min) {
            $this->errors[$field] = "$label en az $min karakter olmalÄ±dÄ±r.";
        }
        return $this;
    }

    public function maxLength(string $field, mixed $value, int $max, string $label = ''): self {
        $label = $label ?: $field;
        if (!empty($value) && mb_strlen($value) > $max) {
            $this->errors[$field] = "$label en fazla $max karakter olabilir.";
        }
        return $this;
    }

    public function numeric(string $field, mixed $value, string $label = ''): self {
        $label = $label ?: $field;
        if (!empty($value) && !is_numeric($value)) {
            $this->errors[$field] = "$label sayÄ±sal bir deÄŸer olmalÄ±dÄ±r.";
        }
        return $this;
    }

    public function phone(string $field, mixed $value, string $label = ''): self {
        $label = $label ?: $field;
        if (!empty($value) && !preg_match('/^[\+]?[0-9\s\-\(\)]{7,20}$/', $value)) {
            $this->errors[$field] = "$label geÃ§erli bir telefon numarasÄ± olmalÄ±dÄ±r.";
        }
        return $this;
    }

    public function slug(string $field, mixed $value, string $label = ''): self {
        $label = $label ?: $field;
        if (!empty($value) && !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
            $this->errors[$field] = "$label geÃ§erli bir URL slug olmalÄ±dÄ±r (kÃ¼Ã§Ã¼k harf, rakam ve tire).";
        }
        return $this;
    }

    public function iban(string $field, mixed $value, string $label = ''): self {
        $label = $label ?: $field;
        if (!empty($value)) {
            $clean = preg_replace('/\s+/', '', strtoupper($value));
            if (!preg_match('/^TR\d{24}$/', $clean)) {
                $this->errors[$field] = "$label geÃ§erli bir TR IBAN olmalÄ±dÄ±r.";
            }
        }
        return $this;
    }

    public function hasErrors(): bool {
        return !empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function firstError(): string {
        return reset($this->errors) ?: '';
    }

    /**
     * Generate URL-safe slug from Turkish text
     */
    public static function makeSlug(string $text): string {
        $tr = ['Ã§' => 'c', 'Ã‡' => 'c', 'ÄŸ' => 'g', 'Ä' => 'g', 'Ä±' => 'i', 'Ä°' => 'i',
               'Ã¶' => 'o', 'Ã–' => 'o', 'ÅŸ' => 's', 'Å' => 's', 'Ã¼' => 'u', 'Ãœ' => 'u'];
        $text = strtr($text, $tr);
        $text = mb_strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }
}
