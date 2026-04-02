<?php

namespace App\Livewire\Help;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $sections = $this->guideSections()->map(function (array $section) {
            return [
                'title' => $section['title'],
                'anchor' => $section['anchor'],
                'html' => Str::markdown($section['content']),
            ];
        });

        return view('livewire.help.index', compact('sections'))
            ->layout('layouts.app');
    }

    private function guideSections(): Collection
    {
        $content = file_get_contents(base_path('docs/GUIDE_UTILISATEUR.md')) ?: '';
        $lines = preg_split("/\r\n|\n|\r/", $content) ?: [];

        $sections = [];
        $currentTitle = null;
        $currentLines = [];

        foreach ($lines as $line) {
            if (str_starts_with($line, '## ')) {
                if ($currentTitle !== null) {
                    $sections[] = $this->makeSection($currentTitle, $currentLines);
                }

                $currentTitle = trim(Str::after($line, '## '));
                $currentLines = [];

                continue;
            }

            if ($currentTitle !== null) {
                $currentLines[] = $line;
            }
        }

        if ($currentTitle !== null) {
            $sections[] = $this->makeSection($currentTitle, $currentLines);
        }

        return collect($sections);
    }

    private function makeSection(string $title, array $lines): array
    {
        return [
            'title' => $title,
            'anchor' => Str::slug($title),
            'content' => trim(implode("\n", $lines)),
        ];
    }
}
