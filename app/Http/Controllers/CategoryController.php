<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Store a newly created category in storage.
     */
    public function store(StoreCategoryRequest $request, Colocation $colocation)
    {
        $validated = $request->validated();

        $colocation->categories()->create([
            'name' => $validated['name'],
        ]);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Category added successfully. -)');
    }

    /**
     * Update the specified category in storage.
     */
    public function update(UpdateCategoryRequest $request, Colocation $colocation, Category $category)
    {
        $validated = $request->validated();

        $category->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Category updated successfully. -)');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Colocation $colocation, Category $category)
    {
        if ($category->colocation_id !== $colocation->id) {
            abort(404);
        }

        $membership = $colocation->memberships()
            ->where('user_id', auth()->id())
            ->whereNull('left_at')
            ->first();

        if (!$membership || $membership->role !== 'owner') {
            abort(403, 'You are not authorized to delete categories. (-_-)');
        }

        // Check if expenses exist before deleting
        if ($category->expenses()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete this category as it has associated expenses. (-_-)');
        }

        $category->delete();

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Category deleted successfully. -)');
    }
}
