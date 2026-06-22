<?php

namespace App\Http\Controllers;

use App\Dtos\Input\StoreCategoryData;
use App\Dtos\Input\UpdateCategoryData;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\Catalog\CategoryService;
use App\Services\Exceptions\DependencyException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService,
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function index(): View
    {
        $categories = $this->categoryRepository->paginateWithProductCount()->toPaginator();

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categoryService->create(StoreCategoryData::fromRequest($request->validated()));

        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function show(int $id): View
    {
        $category = $this->categoryRepository->findOrFail($id);
        $productsCount = $this->categoryRepository->countProducts($id);

        return view('categories.show', compact('category', 'productsCount'));
    }

    public function edit(int $id): View
    {
        $category = $this->categoryRepository->findOrFail($id);

        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, int $id): RedirectResponse
    {
        $this->categoryService->update($id, UpdateCategoryData::fromRequest($request->validated()));

        return redirect()->route('categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->categoryService->delete($id);
        } catch (DependencyException $e) {
            return redirect()->route('categories.index')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('categories.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
