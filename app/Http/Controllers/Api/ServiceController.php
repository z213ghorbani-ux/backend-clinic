<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    // لیست خدمات درختی
    public function index(Request $request)
    {
        $query = Service::query()->whereNull('parent_id')->with(['children']);

        if ($request->boolean('active_only')) {
            $query->where('is_active', true)->with(['children' => function ($q) {
                $q->where('is_active', true);
            }]);
        }

        $services = $query->orderBy('sort_order')->orderBy('id')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $services
        ]);
    }

    // ثبت خدمت جدید (والد یا فرزند)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id'      => 'nullable|exists:services,id',
            'name'           => 'required|string|max:255',
            'code'           => 'nullable|string|max:50|unique:services,code',
            'price'          => 'nullable|numeric|min:0',
            'is_active'      => 'nullable|boolean',
            'is_visit'       => 'nullable|boolean',
            'sort_order'     => 'nullable|integer',
            'has_signature'  => 'nullable|boolean',
            'signature_x'    => 'nullable|numeric|min:0',
            'signature_y'    => 'nullable|numeric|min:0',
            'signature_page' => 'nullable|string|in:first,last',
        ], [
            'name.required' => 'نام خدمت الزامی است.',
            'code.unique'   => 'این کد خدمت قبلاً ثبت شده است.',
        ]);

        $service = Service::create([
            'parent_id'  => $validated['parent_id'] ?? null,
            'name'       => trim($validated['name']),
            'code'       => !empty($validated['code']) ? trim($validated['code']) : null,
            'price'      => $validated['price'] ?? 0,
            'is_active'  => $validated['is_active'] ?? true,
            'is_visit'   => $validated['is_visit'] ?? false,
            'sort_order' => $validated['sort_order'] ?? 0,
            // فیلدهای مهر و امضا
            'has_signature'  => $request->boolean('has_signature'),
            'signature_x'    => $request->filled('signature_x') ? (float) $request->input('signature_x') : null,
            'signature_y'    => $request->filled('signature_y') ? (float) $request->input('signature_y') : null,
            'signature_page' => $validated['signature_page'] ?? 'last',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'خدمت با موفقیت ثبت شد.',
            'data'    => $service->load('children')
        ], 201);
    }

    // به‌روزرسانی خدمت
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'parent_id'      => ['nullable', 'exists:services,id', Rule::notIn([$service->id])],
            'name'           => 'required|string|max:255',
            'code'           => ['nullable', 'string', 'max:50', Rule::unique('services', 'code')->ignore($service->id)],
            'price'          => 'nullable|numeric|min:0',
            'is_active'      => 'nullable|boolean',
            'is_visit'       => 'nullable|boolean',
            'sort_order'     => 'nullable|integer',
            'has_signature'  => 'nullable|boolean',
            'signature_x'    => 'nullable|numeric|min:0',
            'signature_y'    => 'nullable|numeric|min:0',
            'signature_page' => 'nullable|string|in:first,last',
        ]);

        $service->update([
            'parent_id'  => $validated['parent_id'] ?? null,
            'name'       => trim($validated['name']),
            'code'       => !empty($validated['code']) ? trim($validated['code']) : null,
            'price'      => $validated['price'] ?? 0,
            'is_active'  => $validated['is_active'] ?? $service->is_active,
            'is_visit'   => $validated['is_visit'] ?? $service->is_visit,
            'sort_order' => $validated['sort_order'] ?? $service->sort_order,
            // فیلدهای مهر و امضا
            'has_signature'  => $request->boolean('has_signature'),
            'signature_x'    => $request->filled('signature_x') ? (float) $request->input('signature_x') : null,
            'signature_y'    => $request->filled('signature_y') ? (float) $request->input('signature_y') : null,
            'signature_page' => $validated['signature_page'] ?? 'last',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'خدمت با موفقیت ویرایش شد.',
            'data'    => $service
        ]);
    }

    // حذف خدمت
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->children()->delete();
        $service->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'خدمت حذف گردید.'
        ]);
    }

    // تغییر وضعیت فعال / غیرفعال
    public function toggleStatus($id)
    {
        $service = Service::findOrFail($id);
        $service->is_active = !$service->is_active;
        $service->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'وضعیت خدمت به‌روز شد.',
            'data'    => $service
        ]);
    }
}
