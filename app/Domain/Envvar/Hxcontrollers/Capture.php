<?php

namespace Leantime\Domain\Envvar\Hxcontrollers;

use Leantime\Core\Controller\HtmxController;

class Capture extends HtmxController
{
    protected static string $view = 'envvar::partials.capture';

    public function get(): void
    {
    }

    public function store(): mixed
    {
        $authHeader = '';
        if (method_exists($this->incomingRequest, 'header')) {
            $authHeader = $this->incomingRequest->header('Authorization') ?? '';
        } else {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        }

        $token = getenv('ENVVAR_CAPTURE_TOKEN') ?: '';
        $isBearer = stripos($authHeader, 'Bearer ') === 0;
        if ($isBearer && $token !== '') {
            $provided = trim(substr($authHeader, 7));
            if (hash_equals($token, $provided)) {
                $raw = @file_get_contents('php://input');
                $data = json_decode($raw ?: '', true);
                $name = $data['name'] ?? '';
                $value = $data['value'] ?? '';
            } else {
                return $this->tpl->displayJson(json_encode(['error' => 'Unauthorized']), 401);
            }
        } elseif (preg_match('/^Bearer\s+/i', $authHeader)) {
            return $this->tpl->displayJson(json_encode(['error' => 'Unauthorized']), 401);
        } else {
            $name = $this->incomingRequest->request->get('name');
            $value = $this->incomingRequest->request->get('value');
        }

        if (!preg_match('/^[A-Z][A-Z0-9_]{1,63}$/', (string) $name)) {
            $this->tpl->setNotification('Invalid environment variable name', 'error');
            return $this->tpl->emptyResponse(422);
        }
        if ($value === '' || $value === null || strlen((string) $value) > 8192) {
            $this->tpl->setNotification('Invalid environment variable value', 'error');
            return $this->tpl->emptyResponse(422);
        }

        $file = getenv('ENVVAR_CAPTURE_FILE') ?: '/var/www/leantime/storage/envvar-inbox.jsonl';
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0700, true);
        }
        $firstCreation = !file_exists($file);

        $fp = fopen($file, 'c+');
        flock($fp, LOCK_EX);
        fseek($fp, 0, SEEK_END);
        $line = json_encode(['name' => $name, 'value' => $value, 'ts' => gmdate('c')]) . "\n";
        fwrite($fp, $line);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        if ($firstCreation) {
            chmod($file, 0600);
        }

        $this->tpl->setNotification('Environment variable captured', 'success');
        return $this->tpl->emptyResponse(200);
    }
}
