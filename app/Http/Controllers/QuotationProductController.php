<?php

namespace App\Http\Controllers;

use App\Models\ProductSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class QuotationProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function manageImages()
    {
        $this->ensureSuperAdmin();

        $productGroups = ProductSubCategory::with(['segmentDetails', 'product'])
            ->orderBy('id', 'desc')
            ->get();

        return view('pages.quotation.group_images', compact('productGroups'));
    }

    public function updateImage(Request $request, ProductSubCategory $productGroup)
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'quotation_image' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
        ]);

        $oldImage = $productGroup->quotation_image;
        $uploadedImage = $validated['quotation_image'];
        $directory = public_path('uploads/quotation-group-images');
        $extension = $uploadedImage->getClientOriginalExtension();
        $fileName = uniqid('quotation-group-', true).'.'.$extension;

        $oldUmask = umask(0);
        if (!file_exists($directory)) {
            @mkdir($directory, 0777, true);
        }
        @chmod($directory, 0777);

        $uploadedImage->move($directory, $fileName);
        $targetFilePath = $directory . DIRECTORY_SEPARATOR . $fileName;
        @chmod($targetFilePath, 0666);
        umask($oldUmask);

        $newImage = 'uploads/quotation-group-images/'.$fileName;

        $productGroup->update(['quotation_image' => $newImage]);

        $this->deleteQuotationImage($oldImage);

        return back()->with('success', 'Quotation image updated for '.$productGroup->subcategory_name.'.');
    }

    public function deleteImage(ProductSubCategory $productGroup)
    {
        $this->ensureSuperAdmin();

        $oldImage = $productGroup->quotation_image;
        $productGroup->update(['quotation_image' => null]);

        $this->deleteQuotationImage($oldImage);

        return back()->with('success', 'Quotation image reset to default for '.$productGroup->subcategory_name.'.');
    }

    private function ensureSuperAdmin(): void
    {
        abort_unless(auth()->user()?->role === 'Super Admin', 403);
    }

    private function deleteQuotationImage(?string $image): void
    {
        if (empty($image)) {
            return;
        }

        $image = ltrim((string) $image, '/');

        if (str_starts_with($image, 'uploads/quotation-group-images/')) {
            File::delete(public_path($image));
        }

        if (str_starts_with($image, 'quotation-group-images/')) {
            File::delete(public_path($image));
        }

        // Support images uploaded before this feature stored files on the public disk.
        if (str_starts_with($image, 'storage/quotation-group-images/')) {
            File::delete(public_path($image));
        }
    }
}