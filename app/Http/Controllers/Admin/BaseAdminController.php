<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FlashMessageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Base Admin Controller
 *
 * Provides common CRUD functionality for all admin controllers.
 * Reduces duplication by centralizing standard operations.
 *
 * @property string $modelClass The fully qualified model class name
 * @property string $routePrefix The route prefix (e.g., 'admin.brands')
 * @property string $viewPrefix The view prefix (e.g., 'admin.brands')
 * @property string $permissionResource The resource name for permissions (e.g., 'brand')
 */
abstract class BaseAdminController extends Controller
{
    /**
     * The model class this controller manages.
     * Must be defined in child classes.
     */
    protected string $modelClass;

    /**
     * Route prefix for redirects (e.g., 'admin.brands').
     */
    protected string $routePrefix;

    /**
     * View prefix (e.g., 'admin.brands').
     */
    protected string $viewPrefix;

    /**
     * Resource name for translations (e.g., 'brand').
     */
    protected string $resourceName;

    /**
     * Available filters for index page.
     * Override in child classes to enable filters.
     */
    protected array $filters = ['status', 'search'];

    /**
     * Columns to search in.
     */
    protected array $searchColumns = ['name'];

    /**
     * Items per page for pagination.
     */
    protected int $perPage = 25;

    /**
     * Image upload path (if applicable).
     */
    protected ?string $imagePath = null;

    /**
     * Image field name in database.
     */
    protected string $imageField = 'image';

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = $this->getModelInstance()->query();

        // Apply filters if the model uses Filterable trait
        if (method_exists($this->modelClass, 'scopeApplyFilters')) {
            $query = $query->applyFilters($request, $this->filters, $this->searchColumns);
        }

        $items = $query->latest()->paginate($this->perPage);

        return view($this->getViewPrefix() . '.index', [
            $this->getPluralName() => $items,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view($this->getViewPrefix() . '.create', $this->getCreateData());
    }

    /**
     * Store a newly created resource in storage.
     *
     * Override in child class for custom logic.
     */
    public function store(Request $request)
    {
        $model = $this->getModelInstance();

        $data = $this->prepareStoreData($request);

        // Handle image upload if applicable
        if ($this->imagePath && $request->hasFile($this->imageField)) {
            $data[$this->imageField] = $request->file($this->imageField)->store($this->imagePath, 'public');
        }

        $model->create($data);

        return FlashMessageService::redirectSuccess(
            $this->getRoutePrefix() . '.index',
            "{$this->resourceName}.created"
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $model = $this->getModelInstance()->withTrashed()->findOrFail($id);

        return view($this->getViewPrefix() . '.show', [
            $this->getSingularName() => $model,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $model = $this->getModelInstance()->findOrFail($id);

        return view($this->getViewPrefix() . '.edit', array_merge([
            $this->getSingularName() => $model,
        ], $this->getEditData($model)));
    }

    /**
     * Update the specified resource in storage.
     *
     * Override in child class for custom logic.
     */
    public function update(Request $request, $id)
    {
        $model = $this->getModelInstance()->findOrFail($id);

        $data = $this->prepareUpdateData($request);

        // Handle image upload if applicable
        if ($this->imagePath && $request->hasFile($this->imageField)) {
            // Delete old image
            if ($model->{$this->imageField}) {
                Storage::disk('public')->delete($model->{$this->imageField});
            }

            $data[$this->imageField] = $request->file($this->imageField)->store($this->imagePath, 'public');
        }

        $model->update($data);

        return FlashMessageService::redirectSuccess(
            $this->getRoutePrefix() . '.index',
            "{$this->resourceName}.updated"
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * Handles soft deletes with image cleanup.
     */
    public function destroy($id)
    {
        $model = $this->getModelInstance()->findOrFail($id);

        // Delete image if applicable
        if ($this->imagePath && $model->{$this->imageField}) {
            Storage::disk('public')->delete($model->{$this->imageField});
        }

        $model->delete();

        return FlashMessageService::redirectSuccess(
            $this->getRoutePrefix() . '.index',
            "{$this->resourceName}.deleted",
            ['name' => $model->name ?? $model->id ?? '']
        );
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore($id)
    {
        $model = $this->getModelInstance()->withTrashed()->findOrFail($id);
        $model->restore();

        return FlashMessageService::redirectSuccess(
            $this->getRoutePrefix() . '.index',
            "{$this->resourceName}.restored"
        );
    }

    /**
     * Get a new instance of the model.
     */
    protected function getModelInstance(): Model
    {
        return new $this->modelClass;
    }

    /**
     * Get the route prefix.
     */
    protected function getRoutePrefix(): string
    {
        return $this->routePrefix;
    }

    /**
     * Get the view prefix.
     */
    protected function getViewPrefix(): string
    {
        return $this->viewPrefix;
    }

    /**
     * Get the plural resource name for views.
     */
    protected function getPluralName(): string
    {
        return str_replace('.', '_', $this->viewPrefix);
    }

    /**
     * Get the singular resource name for views.
     */
    protected function getSingularName(): string
    {
        return str($this->viewPrefix)->afterLast('.');
    }

    /**
     * Prepare data for storing.
     * Override in child class to customize.
     */
    protected function prepareStoreData(Request $request): array
    {
        return $request->validated();
    }

    /**
     * Prepare data for updating.
     * Override in child class to customize.
     */
    protected function prepareUpdateData(Request $request): array
    {
        return $request->validated();
    }

    /**
     * Get additional data for create view.
     * Override in child class to pass extra data.
     */
    protected function getCreateData(): array
    {
        return [];
    }

    /**
     * Get additional data for edit view.
     * Override in child class to pass extra data.
     */
    protected function getEditData($model): array
    {
        return [];
    }
}
