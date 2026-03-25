<?php

namespace Modules\Products\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Products\Models\Product;

class ProductsController extends Controller
{
    public function destroy(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('products.delete'), 403);

        Product::query()->whereKey($id)->delete();

        return redirect()
            ->route('products.index')
            ->with('status', 'Product deleted.');
    }
}
