<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardLink;
use App\Models\Category;
use App\Models\PortalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ManagePortalController extends Controller
{
    protected function ensureEditMode()
    {
        if (! session('portal_edit_mode')) {
            abort(response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Silakan masuk ke Mode Edit terlebih dahulu.',
            ], 403));
        }
    }

    // ==========================================
    // PORTAL SETTINGS
    // ==========================================
    public function updateSettings(Request $request)
    {
        $this->ensureEditMode();

        $validated = $request->validate([
            'portal_name'      => 'required|string|max:255',
            'homepage_message' => 'nullable|string|max:1000',
            'security_code'    => 'required|string|max:255',
        ]);

        $setting = PortalSetting::first();
        if (! $setting) {
            $setting = new PortalSetting();
        }

        $setting->portal_name      = $validated['portal_name'];
        $setting->homepage_message = $validated['homepage_message'];
        $setting->security_code    = $validated['security_code'];
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan portal berhasil diperbarui.',
            'setting' => $setting,
        ]);
    }

    // ==========================================
    // CATEGORIES
    // ==========================================
    public function getCategories()
    {
        return response()->json([
            'success'    => true,
            'categories' => Category::orderBy('sort_order')->get(),
        ]);
    }

    public function storeCategory(Request $request)
    {
        $this->ensureEditMode();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'icon'  => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
        ]);

        $slug = Str::slug($validated['name']);
        $maxSort = Category::max('sort_order') ?? 0;

        $category = Category::create([
            'uuid'       => (string) Str::uuid(),
            'name'       => $validated['name'],
            'slug'       => $slug,
            'icon'       => $validated['icon'] ?? 'folder',
            'color'      => $validated['color'] ?? 'primary',
            'sort_order' => $maxSort + 1,
            'is_active'  => true,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Kategori berhasil ditambahkan.',
            'category' => $category,
        ]);
    }

    public function updateCategory(Request $request, string $uuid)
    {
        $this->ensureEditMode();

        $category = Category::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'icon'      => 'nullable|string|max:255',
            'color'     => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $category->name  = $validated['name'];
        $category->slug  = Str::slug($validated['name']);
        if (isset($validated['icon']))  $category->icon  = $validated['icon'];
        if (isset($validated['color'])) $category->color = $validated['color'];
        if (isset($validated['is_active'])) $category->is_active = $validated['is_active'];
        $category->save();

        return response()->json([
            'success'  => true,
            'message'  => 'Kategori berhasil diperbarui.',
            'category' => $category,
        ]);
    }

    public function destroyCategory(string $uuid)
    {
        $this->ensureEditMode();

        $category = Category::where('uuid', $uuid)->firstOrFail();
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }

    // ==========================================
    // CARDS
    // ==========================================
    public function storeCard(Request $request)
    {
        $this->ensureEditMode();

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string|max:1000',
            'badge'        => 'nullable|string|max:255',
            'color'        => 'nullable|string|max:255',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'expired_at'   => 'nullable|date',
        ]);

        $maxSort = Card::max('sort_order') ?? 0;

        $card = Card::create([
            'uuid'        => (string) Str::uuid(),
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'badge'       => $validated['badge'] ?? null,
            'color'       => $validated['color'] ?? null,
            'sort_order'  => $maxSort + 1,
            'is_active'   => true,
            'expired_at'  => $validated['expired_at'] ?? null,
        ]);

        if (! empty($validated['category_ids'])) {
            $card->categories()->sync($validated['category_ids']);
            $card->category_id = $validated['category_ids'][0];
            $card->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Kartu berhasil ditambahkan.',
            'card'    => $card->load('categories', 'links'),
        ]);
    }

    public function updateCard(Request $request, string $uuid)
    {
        $this->ensureEditMode();

        $card = Card::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string|max:1000',
            'badge'        => 'nullable|string|max:255',
            'color'        => 'nullable|string|max:255',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'is_active'    => 'nullable|boolean',
            'expired_at'   => 'nullable|date',
        ]);

        $card->title       = $validated['title'];
        $card->description = $validated['description'] ?? null;
        $card->badge       = $validated['badge'] ?? null;
        $card->color       = $validated['color'] ?? null;
        if (isset($validated['is_active'])) $card->is_active = $validated['is_active'];
        $card->expired_at  = $validated['expired_at'] ?? null;
        $card->save();

        if (isset($validated['category_ids'])) {
            $card->categories()->sync($validated['category_ids']);
            $card->category_id = ! empty($validated['category_ids']) ? $validated['category_ids'][0] : null;
            $card->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Kartu berhasil diperbarui.',
            'card'    => $card->load('categories', 'links'),
        ]);
    }

    public function destroyCard(string $uuid)
    {
        $this->ensureEditMode();

        $card = Card::where('uuid', $uuid)->firstOrFail();
        $card->delete(); // Soft delete

        return response()->json([
            'success' => true,
            'message' => 'Kartu berhasil dihapus.',
        ]);
    }

    public function reorderCards(Request $request)
    {
        $this->ensureEditMode();

        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'string', // uuids
        ]);

        foreach ($validated['order'] as $index => $uuid) {
            Card::where('uuid', $uuid)->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan kartu berhasil disimpan.',
        ]);
    }

    // ==========================================
    // CARD LINKS
    // ==========================================
    public function storeLink(Request $request)
    {
        $this->ensureEditMode();

        $validated = $request->validate([
            'card_uuid'  => 'required|string|exists:cards,uuid',
            'title'      => 'required|string|max:255',
            'subtitle'   => 'nullable|string|max:255',
            'url'        => 'required|url',
            'icon'       => 'nullable|string|max:255',
            'color'      => 'nullable|string|max:255',
            'expired_at' => 'nullable|date',
        ]);

        $card = Card::where('uuid', $validated['card_uuid'])->firstOrFail();
        $maxSort = CardLink::where('card_id', $card->id)->max('sort_order') ?? 0;

        $link = CardLink::create([
            'uuid'       => (string) Str::uuid(),
            'card_id'    => $card->id,
            'title'      => $validated['title'],
            'subtitle'   => $validated['subtitle'] ?? null,
            'url'        => $validated['url'],
            'icon'       => $validated['icon'] ?? 'link-45deg',
            'color'      => $validated['color'] ?? null,
            'sort_order' => $maxSort + 1,
            'is_active'  => true,
            'expired_at' => $validated['expired_at'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tautan berhasil ditambahkan.',
            'link'    => $link,
        ]);
    }

    public function updateLink(Request $request, string $uuid)
    {
        $this->ensureEditMode();

        $link = CardLink::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'subtitle'   => 'nullable|string|max:255',
            'url'        => 'required|url',
            'icon'       => 'nullable|string|max:255',
            'color'      => 'nullable|string|max:255',
            'is_active'  => 'nullable|boolean',
            'expired_at' => 'nullable|date',
        ]);

        $link->title      = $validated['title'];
        $link->subtitle   = $validated['subtitle'] ?? null;
        $link->url        = $validated['url'];
        $link->icon       = $validated['icon'] ?? 'link-45deg';
        $link->color      = $validated['color'] ?? null;
        if (isset($validated['is_active'])) $link->is_active = $validated['is_active'];
        $link->expired_at = $validated['expired_at'] ?? null;
        $link->save();

        return response()->json([
            'success' => true,
            'message' => 'Tautan berhasil diperbarui.',
            'link'    => $link,
        ]);
    }

    public function destroyLink(string $uuid)
    {
        $this->ensureEditMode();

        $link = CardLink::where('uuid', $uuid)->firstOrFail();
        $link->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tautan berhasil dihapus.',
        ]);
    }

    public function reorderLinks(Request $request)
    {
        $this->ensureEditMode();

        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'string', // uuids
        ]);

        foreach ($validated['order'] as $index => $uuid) {
            CardLink::where('uuid', $uuid)->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan tautan berhasil disimpan.',
        ]);
    }
}
