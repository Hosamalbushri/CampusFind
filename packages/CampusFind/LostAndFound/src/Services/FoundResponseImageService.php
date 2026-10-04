<?php

namespace CampusFind\LostAndFound\Services;

use CampusFind\LostAndFound\Models\FoundReportResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class FoundResponseImageService
{
    /**
     * @param  list<UploadedFile>  $images
     * @return list<array{staging_key: string, final_key: string, mime: string, byte_size: int}>
     */
    public function prepare(array $images): array
    {
        if (count($images) > 3) {
            throw new InvalidArgumentException('A found response may include at most three images.');
        }

        $prepared = [];
        $disk = Storage::disk($this->diskName());

        try {
            foreach ($images as $image) {
                if (! $image instanceof UploadedFile) {
                    throw new InvalidArgumentException('Every found-response image must be an uploaded file.');
                }

                $sanitized = (new LostFoundRasterSanitizer)->sanitize($image, $this->limits(), $this->encoding());
                $stagingKey = 'lost-found/staging/'.Str::uuid().'.tmp';
                $finalKey = 'lost-found/responses/'.Str::uuid().'/'.Str::uuid().'.'.$sanitized['extension'];

                if (! $disk->put($stagingKey, $sanitized['sanitized_bytes'])) {
                    throw new RuntimeException('Unable to stage the private found-response image.');
                }

                $prepared[] = [
                    'staging_key' => $stagingKey,
                    'final_key' => $finalKey,
                    'mime' => $sanitized['mime'],
                    'byte_size' => strlen($sanitized['sanitized_bytes']),
                ];
            }

            return $prepared;
        } catch (Throwable $exception) {
            $this->cleanup($prepared);

            throw $exception;
        }
    }

    /** @param list<array{staging_key: string, final_key: string, mime: string, byte_size: int}> $prepared */
    public function finalize(FoundReportResponse $response, array $prepared): void
    {
        $disk = Storage::disk($this->diskName());

        foreach ($prepared as $image) {
            if (! $disk->move($image['staging_key'], $image['final_key'])) {
                throw new RuntimeException('Unable to finalize the private found-response image.');
            }

            $response->images()->create([
                'storage_key' => $image['final_key'],
                'storage_key_hash' => hash('sha256', $image['final_key']),
                'mime_type' => $image['mime'],
                'byte_size' => $image['byte_size'],
                'submitted_at' => now(),
            ]);
        }
    }

    /** @param list<array{staging_key: string, final_key: string, mime: string, byte_size: int}> $prepared */
    public function cleanup(array $prepared): void
    {
        $disk = Storage::disk($this->diskName());

        foreach ($prepared as $image) {
            foreach ([$image['staging_key'], $image['final_key']] as $key) {
                try {
                    if ($disk->exists($key)) {
                        $disk->delete($key);
                    }
                } catch (Throwable $exception) {
                    Log::error('Unable to compensate a private found-response image.', [
                        'storage_key_hash' => hash('sha256', $key),
                        'exception' => $exception::class,
                    ]);
                }
            }
        }
    }

    public function diskName(): string
    {
        $disk = (string) config('lost_found.found_response_images.disk', 'lost_found_private');

        if ($disk === '') {
            throw new RuntimeException('The private found-response image disk is not configured.');
        }

        return $disk;
    }

    /** @return array{max_bytes: int, max_width: int, max_height: int, max_pixels: int} */
    private function limits(): array
    {
        return [
            'max_bytes' => (int) config('lost_found.found_response_images.max_bytes', 2 * 1024 * 1024),
            'max_width' => (int) config('lost_found.found_response_images.max_width', 4096),
            'max_height' => (int) config('lost_found.found_response_images.max_height', 4096),
            'max_pixels' => (int) config('lost_found.found_response_images.max_pixels', 12_000_000),
        ];
    }

    /** @return array{jpeg_quality: int, png_compression: int, webp_quality: int} */
    private function encoding(): array
    {
        return [
            'jpeg_quality' => (int) config('lost_found.found_response_images.jpeg_quality', 90),
            'png_compression' => (int) config('lost_found.found_response_images.png_compression', 6),
            'webp_quality' => (int) config('lost_found.found_response_images.webp_quality', 90),
        ];
    }
}
