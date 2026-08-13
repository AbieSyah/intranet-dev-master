<?php

namespace App\Services;

use App\Models\LetterType;
use Illuminate\Support\Str;

class LetterTypeService
{
    /**
     * Get all letter types ordered by name.
     */
    public function index()
    {
        return LetterType::with('activeTemplate')
            ->orderBy('name')
            ->get();
    }

    /**
     * Find letter type by ID with relationships.
     */
    public function find(int $id): LetterType
    {
        return LetterType::with('templates.creator', 'templates.updater', 'activeTemplate')
            ->findOrFail($id);
    }

    /**
     * Find letter type by slug.
     */
    public function findBySlug(string $slug): ?LetterType
    {
        return LetterType::with('templates', 'activeTemplate')
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Create a new letter type.
     */
    public function create(array $data): LetterType
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['icon'] = $data['icon'] ?? 'ri-file-text-line';
        $data['color'] = $data['color'] ?? 'primary';

        return LetterType::create($data);
    }

    /**
     * Update a letter type.
     */
    public function update(int $id, array $data): LetterType
    {
        $type = LetterType::findOrFail($id);

        $data['slug'] = Str::slug($data['name']);
        $type->update($data);

        return $type->fresh();
    }

    /**
     * Delete a letter type if it has no documents and no templates.
     */
    public function delete(int $id): void
    {
        $type = LetterType::withCount(['documents', 'templates'])->findOrFail($id);

        if ($type->templates_count > 0 || $type->documents_count > 0) {
            $reasons = [];
            if ($type->templates_count > 0) $reasons[] = "{$type->templates_count} template";
            if ($type->documents_count > 0) $reasons[] = "{$type->documents_count} dokumen";

            throw new \RuntimeException(
                "Jenis surat \"{$type->name}\" tidak dapat dihapus karena masih memiliki " . implode(' dan ', $reasons) . "."
            );
        }

        $type->delete();
    }
}
