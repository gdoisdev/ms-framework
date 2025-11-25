<?php

/** Por: Geovane Gomes **/
/***** em: 22Nov25 ******/

namespace MSFramework\Core;

class Message
{
    public const TYPE_SUCCESS = 'success';
    public const TYPE_ERROR   = 'error';
    public const TYPE_WARNING = 'warning';
    public const TYPE_INFO    = 'info';

    protected string $text;
    protected string $type;
    protected ?string $icon = null;
    protected int $duration = 5000; // milissegundos
    protected bool $persist = false;

    /**
     * Construtor
     *
     * @param string $text
     * @param string $type
     * @param string|null $icon
     * @param int $duration
     */
    public function __construct(string $text, string $type = self::TYPE_INFO, ?string $icon = null, int $duration = 5000)
    {
        $this->text     = $text;
        $this->type     = $type;
        $this->icon     = $icon;
        $this->duration = $duration;
    }

    /**
     * Define se a mensagem deve persistir na sessão
     *
     * @param bool $persist
     * @return self
     */
    public function persist(bool $persist = true): self
    {
        $this->persist = $persist;
        return $this;
    }

    /**
     * Retorna a mensagem em array, pronta para JSON
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'message'  => $this->text,
            'type'     => $this->type,
            'icon'     => $this->icon,
            'duration' => $this->duration,
            'persist'  => $this->persist,
        ];
    }

    /**
     * Getters
     */
    public function getText(): string { return $this->text; }
    public function getType(): string { return $this->type; }
    public function getIcon(): ?string { return $this->icon; }
    public function getDuration(): int { return $this->duration; }
    public function shouldPersist(): bool { return $this->persist; }

    /**
     * Setters
     */
    public function setText(string $text): self { $this->text = $text; return $this; }
    public function setType(string $type): self { $this->type = $type; return $this; }
    public function setIcon(?string $icon): self { $this->icon = $icon; return $this; }
    public function setDuration(int $duration): self { $this->duration = $duration; return $this; }
}
