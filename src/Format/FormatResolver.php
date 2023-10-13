<?php

namespace App\Format;

class FormatResolver
{
    /**
     * @var FormatInterface[]
     */
    private array $formats;

    public function addFormat(FormatInterface $format) : self
    {
        $this->formats[] = $format;

        return $this;
    }

    public function getFormat(string $format) : FormatInterface
    {
        foreach ($this->formats as $supportedFormat) {
            if ($supportedFormat->supports($format)) {
                return $supportedFormat;
            }
        }

        throw new \RuntimeException('Format not supported');
    }
}