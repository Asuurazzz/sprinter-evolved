<?php

namespace App\Enums;

enum FavoriteType: string
{
    case TEAM = 'team';
    case BOARD = 'board';
    case TASK = 'task';
    case PROJECT = 'project';
    case KNOWLEDGE_CONTENT = 'knowledge_content';

    public function label(): string
    {
        return match ($this) {
            self::TEAM => 'Time',
            self::BOARD => 'Quadro',
            self::TASK => 'Tarefa',
            self::PROJECT => 'Projeto',
            self::KNOWLEDGE_CONTENT => 'Conteúdo de Conhecimento',
        };
    }
}
