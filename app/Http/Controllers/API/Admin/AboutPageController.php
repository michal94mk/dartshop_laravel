<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\AboutUs;
use App\Http\Requests\Admin\AboutPageRequest;
use Illuminate\Support\Facades\Log;

class AboutPageController extends BaseAdminController
{
    /**
     * Display the first about page data.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Get the first about page record or create a new one if none exists
            $aboutPage = AboutUs::first();
            
            if (!$aboutPage) {
                $aboutPage = new AboutUs();
                $aboutPage->title = 'O nas';
                $aboutPage->content = 'Dodaj treść strony O nas.';
                $aboutPage->save();
            }
            
            return $this->successResponse('Dane strony O nas pobrane pomyślnie', $aboutPage);
        } catch (\Exception $e) {
            Log::error('Failed to get about page data', [
                'error' => $e->getMessage(),
                'method' => __METHOD__
            ]);
            
            return $this->errorResponse('Błąd podczas pobierania danych strony O nas: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Display all about pages.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        try {
            $aboutPages = AboutUs::orderBy('created_at')->get();
            return $this->successResponse('Wszystkie strony O nas pobrane pomyślnie', $aboutPages);
        } catch (\Exception $e) {
            Log::error('Failed to get all about pages', [
                'error' => $e->getMessage(),
                'method' => __METHOD__
            ]);
            
            return $this->errorResponse('Błąd podczas pobierania wszystkich stron O nas: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Display the specified about page.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $aboutPage = AboutUs::findOrFail($id);
            return $this->successResponse('Strona O nas pobrana pomyślnie', $aboutPage);
        } catch (\Exception $e) {
            Log::error('Failed to get about page', [
                'error' => $e->getMessage(),
                'id' => $id,
                'method' => __METHOD__
            ]);
            
            return $this->errorResponse('Nie znaleziono strony O nas', 404);
        }
    }

    /**
     * Create a new about page.
     *
     * @param  \App\Http\Requests\Admin\AboutPageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function create(AboutPageRequest $request)
    {
        try {
            $aboutPage = new AboutUs();
            $aboutPage->fill($request->validated());
            
            $aboutPage->save();
            
            return $this->successResponse('Strona O nas została utworzona', $aboutPage, 201);
        } catch (\Exception $e) {
            Log::error('Failed to create about page', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated(),
                'method' => __METHOD__
            ]);
            
            return $this->errorResponse('Błąd podczas tworzenia strony O nas: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the first about page.
     *
     * @param  \App\Http\Requests\Admin\AboutPageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(AboutPageRequest $request)
    {
        try {
            $aboutPage = AboutUs::first();
            
            if (!$aboutPage) {
                $aboutPage = new AboutUs();
            }
            
            $aboutPage->fill($request->validated());
            $aboutPage->save();
            
            return $this->successResponse('Strona O nas została zaktualizowana', $aboutPage);
        } catch (\Exception $e) {
            Log::error('Failed to update about page', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated(),
                'method' => __METHOD__
            ]);
            
            return $this->errorResponse('Błąd podczas aktualizacji strony O nas: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified about page.
     *
     * @param  \App\Http\Requests\Admin\AboutPageRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateById(AboutPageRequest $request, $id)
    {
        try {
            $aboutPage = AboutUs::findOrFail($id);
            $aboutPage->fill($request->validated());
            $aboutPage->save();
            
            return $this->successResponse('Strona O nas została zaktualizowana', $aboutPage);
        } catch (\Exception $e) {
            Log::error('Failed to update about page by ID', [
                'error' => $e->getMessage(),
                'id' => $id,
                'request_data' => $request->validated(),
                'method' => __METHOD__
            ]);
            
            return $this->errorResponse('Błąd podczas aktualizacji strony O nas: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified about page from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $aboutPage = AboutUs::findOrFail($id);
            $aboutPage->delete();
            
            return $this->successResponse('Strona O nas została usunięta', null, 204);
        } catch (\Exception $e) {
            Log::error('Failed to delete about page', [
                'error' => $e->getMessage(),
                'id' => $id,
                'method' => __METHOD__
            ]);
            
            return $this->errorResponse('Błąd podczas usuwania strony O nas: ' . $e->getMessage(), 500);
        }
    }
} 