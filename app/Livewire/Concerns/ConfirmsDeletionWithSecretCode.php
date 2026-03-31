<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Hash;

trait ConfirmsDeletionWithSecretCode
{
    public ?int $pendingDeleteId = null;
    public string $pendingDeleteLabel = '';
    public string $deleteSecretCode = '';
    public bool $showDeleteModal = false;

    public function openDeleteModal(int $id, string $label): void
    {
        $this->resetDeleteState();
        $this->pendingDeleteId = $id;
        $this->pendingDeleteLabel = $label;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->resetDeleteState();
    }

    public function confirmDelete(): void
    {
        $data = $this->validate([
            'deleteSecretCode' => ['required', 'string'],
        ]);

        $user = auth()->user();
        if (!$user?->secret_code) {
            $this->addError('deleteSecretCode', 'Aucun code secret n’est configuré pour votre compte.');
            return;
        }

        if (!Hash::check($data['deleteSecretCode'], $user->secret_code)) {
            $this->addError('deleteSecretCode', 'Code secret invalide.');
            return;
        }

        if (!$this->pendingDeleteId) {
            $this->addError('deleteSecretCode', 'Aucune suppression en attente.');
            return;
        }

        $this->performDelete($this->pendingDeleteId);
        $this->resetDeleteState();
    }

    private function resetDeleteState(): void
    {
        $this->reset(['pendingDeleteId', 'pendingDeleteLabel', 'deleteSecretCode', 'showDeleteModal']);
        $this->resetErrorBag('deleteSecretCode');
    }

    abstract protected function performDelete(int $id): void;
}
