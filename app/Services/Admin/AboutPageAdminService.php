<?php

namespace App\Services\Admin;

use App\Models\AboutUs;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Service for handling About Us page business logic (CRUD, retrieval)
 */
class AboutPageAdminService
{
    /**
     * Get the first about page or create a default one if not exists.
     *
     * @return AboutUs
     */
    public function getFirstOrCreate(): AboutUs
    {
        $aboutPage = AboutUs::first();
        if (!$aboutPage) {
            $aboutPage = new AboutUs();
            $aboutPage->title = 'O nas';
            $aboutPage->content = 'Dodaj treść strony O nas.';
            $aboutPage->save();
        }
        return $aboutPage;
    }

    /**
     * Get all about pages.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return AboutUs::orderBy('created_at')->get();
    }

    /**
     * Get a specific about page by ID.
     *
     * @param int $id
     * @return AboutUs
     * @throws ModelNotFoundException
     */
    public function getById(int $id): AboutUs
    {
        return AboutUs::findOrFail($id);
    }

    /**
     * Create a new about page.
     *
     * @param array $data
     * @return AboutUs
     */
    public function create(array $data): AboutUs
    {
        $aboutPage = new AboutUs();
        $aboutPage->fill($data);
        $aboutPage->save();
        return $aboutPage;
    }

    /**
     * Update the first about page (or create if not exists).
     *
     * @param array $data
     * @return AboutUs
     */
    public function updateFirst(array $data): AboutUs
    {
        $aboutPage = AboutUs::first();
        if (!$aboutPage) {
            $aboutPage = new AboutUs();
        }
        $aboutPage->fill($data);
        $aboutPage->save();
        return $aboutPage;
    }

    /**
     * Update a specific about page by ID.
     *
     * @param int $id
     * @param array $data
     * @return AboutUs
     */
    public function updateById(int $id, array $data): AboutUs
    {
        $aboutPage = AboutUs::findOrFail($id);
        $aboutPage->fill($data);
        $aboutPage->save();
        return $aboutPage;
    }

    /**
     * Delete a specific about page by ID.
     *
     * @param int $id
     * @return void
     */
    public function deleteById(int $id): void
    {
        $aboutPage = AboutUs::findOrFail($id);
        $aboutPage->delete();
    }
} 