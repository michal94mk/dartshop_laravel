<?php

namespace App\Services\Admin;

use App\Models\TermsOfService;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Service class for managing Terms of Service in the admin panel.
 * Handles creation, updating, activation, and deletion of ToS versions.
 */
class TermsOfServiceAdminService
{
    /**
     * Get all terms of service ordered by creation date (desc).
     *
     * @return \Illuminate\Database\Eloquent\Collection|TermsOfService[]
     * @throws Exception
     */
    public function getAll()
    {
        try {
            return TermsOfService::orderBy('created_at', 'desc')->get();
        } catch (Exception $e) {
            Log::error('Error fetching all terms of service (admin)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get a terms of service by ID.
     *
     * @param int $id
     * @return TermsOfService|null
     * @throws Exception
     */
    public function getById(int $id): ?TermsOfService
    {
        try {
            return TermsOfService::find($id);
        } catch (Exception $e) {
            Log::error('Error fetching terms of service by ID (admin)', [
                'id' => $id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Create a new terms of service.
     *
     * @param array $data
     * @return TermsOfService
     * @throws Exception
     */
    public function create(array $data): TermsOfService
    {
        try {
            $terms = TermsOfService::create($data);
            if ($terms->is_active) {
                $terms->setAsActive();
            }
            return $terms;
        } catch (Exception $e) {
            Log::error('Error creating terms of service (admin)', [
                'data' => $data,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing terms of service.
     *
     * @param TermsOfService $terms
     * @param array $data
     * @return TermsOfService
     * @throws Exception
     */
    public function update(TermsOfService $terms, array $data): TermsOfService
    {
        try {
            $terms->update($data);
            if ($terms->is_active) {
                $terms->setAsActive();
            }
            return $terms;
        } catch (Exception $e) {
            Log::error('Error updating terms of service (admin)', [
                'id' => $terms->id,
                'data' => $data,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a terms of service (only if not active).
     *
     * @param TermsOfService $terms
     * @return void
     * @throws Exception
     */
    public function delete(TermsOfService $terms): void
    {
        try {
            if ($terms->is_active) {
                throw new Exception('Cannot delete active terms of service');
            }
            $terms->delete();
        } catch (Exception $e) {
            Log::error('Error deleting terms of service (admin)', [
                'id' => $terms->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Set the specified terms of service as active.
     *
     * @param TermsOfService $terms
     * @return TermsOfService
     * @throws Exception
     */
    public function setActive(TermsOfService $terms): TermsOfService
    {
        try {
            $terms->setAsActive();
            return $terms->fresh();
        } catch (Exception $e) {
            Log::error('Error setting terms of service as active (admin)', [
                'id' => $terms->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 