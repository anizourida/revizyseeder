<?php

namespace App\Services\Raiida;

class TypecastCurlCredentialParser
{
    /**
     * @return array{authorization:?string,cookie:?string}
     */
    public function parse(string $curlCommand): array
    {
        $command = trim($curlCommand);
        if ($command === '') {
            return [
                'authorization' => null,
                'cookie' => null,
            ];
        }

        $authorization = $this->extractAuthorization($command);
        $cookie = $this->extractCookie($command);

        return [
            'authorization' => $authorization,
            'cookie' => $cookie,
        ];
    }

    private function extractAuthorization(string $command): ?string
    {
        if (preg_match('/(?:-H|--header)\s+[\'"]authorization:\s*(Bearer\s+[^\'"]+)[\'"]/i', $command, $matches) === 1) {
            return trim((string) ($matches[1] ?? '')) ?: null;
        }

        return null;
    }

    private function extractCookie(string $command): ?string
    {
        if (preg_match('/(?:-b|--cookie)\s+[\'"]([^\'"]+)[\'"]/i', $command, $matches) === 1) {
            $value = trim((string) ($matches[1] ?? ''));

            return $value !== '' ? $value : null;
        }

        if (preg_match('/(?:-H|--header)\s+[\'"]cookie:\s*([^\'"]+)[\'"]/i', $command, $matches) === 1) {
            $value = trim((string) ($matches[1] ?? ''));

            return $value !== '' ? $value : null;
        }

        return null;
    }
}
