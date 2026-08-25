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
            'expired_at'   => 'nullable|date|after:today',
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

    public function storeWithLinks(Request $request)
    {
        $this->ensureEditMode();

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string|max:1000',
            'badge'          => 'nullable|string|max:255',
            'color'          => 'nullable|string|max:255',
            'category_ids'   => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'expired_at'     => 'nullable|date|after:today',
            'links'          => 'required|array|min:1',
            'links.*.title'  => 'required|string|max:255',
            'links.*.url'    => 'required|string|max:2000',
            'links.*.subtitle'   => 'nullable|string|max:255',
            'links.*.icon'       => 'nullable|string|max:255',
            'links.*.color'      => 'nullable|string|max:255',
            'links.*.expired_at' => 'nullable|date|after:today',
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

        foreach ($validated['links'] as $index => $linkData) {
            CardLink::create([
                'uuid'       => (string) Str::uuid(),
                'card_id'    => $card->id,
                'title'      => $linkData['title'],
                'subtitle'   => $linkData['subtitle'] ?? null,
                'url'        => $this->formatUrl($linkData['url']),
                'icon'       => $linkData['icon'] ?? 'link-45deg',
                'color'      => $linkData['color'] ?? null,
                'sort_order' => $index + 1,
                'is_active'  => true,
                'expired_at' => $linkData['expired_at'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tautan berhasil ditambahkan.',
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
            'expired_at'   => 'nullable|date|after:today',
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
    private function formatUrl(string $url): string
    {
        $url = trim($url);

        // Jika diawali skema khusus seperti mailto:, tel:, whatsapp:, biarkan
        if (Str::startsWith($url, ['mailto:', 'tel:', 'whatsapp://', 'tg://'])) {
            return $url;
        }

        // Jika formatnya adalah email murni (misal: admin@gengzmijen.cloud)
        if (filter_var($url, FILTER_VALIDATE_EMAIL)) {
            return 'mailto:' . $url;
        }

        // Jika hanya angka nomor telepon (misal: +628123456789 atau 08123456789)
        if (preg_match('/^\+?[0-9\s\-()]{7,}$/', $url)) {
            return 'tel:' . str_replace([' ', '-', '(', ')'], '', $url);
        }

        // Jika belum memiliki skema dan bukan path lokal/jangkar, tambahkan https://
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9+.-]*:\/\//', $url) && !Str::startsWith($url, ['/', '#'])) {
            return 'https://' . $url;
        }

        return $url;
    }

    public function storeLink(Request $request)
    {
        $this->ensureEditMode();

        $validated = $request->validate([
            'card_uuid'  => 'required|string|exists:cards,uuid',
            'title'      => 'required|string|max:255',
            'subtitle'   => 'nullable|string|max:255',
            'url'        => 'required|string|max:2000',
            'icon'       => 'nullable|string|max:255',
            'color'      => 'nullable|string|max:255',
            'expired_at' => 'nullable|date|after:today',
        ]);

        $card = Card::where('uuid', $validated['card_uuid'])->firstOrFail();
        $maxSort = CardLink::where('card_id', $card->id)->max('sort_order') ?? 0;

        $link = CardLink::create([
            'uuid'       => (string) Str::uuid(),
            'card_id'    => $card->id,
            'title'      => $validated['title'],
            'subtitle'   => $validated['subtitle'] ?? null,
            'url'        => $this->formatUrl($validated['url']),
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
            'url'        => 'required|string|max:2000',
            'icon'       => 'nullable|string|max:255',
            'color'      => 'nullable|string|max:255',
            'is_active'  => 'nullable|boolean',
            'expired_at' => 'nullable|date',
        ]);

        $link->title      = $validated['title'];
        $link->subtitle   = $validated['subtitle'] ?? null;
        $link->url        = $this->formatUrl($validated['url']);
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
        $card = Card::find($link->card_id);
        $link->delete();

        // Jika kartu sudah tidak memiliki tautan aktif lagi, hapus kartu tersebut
        if ($card && $card->links()->count() === 0) {
            $card->delete();
        }

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

    // ==========================================
    // MOVE LINK (Cross-Card Drag & Drop)
    // ==========================================
    public function moveLink(Request $request, string $uuid)
    {
        $this->ensureEditMode();

        $link = CardLink::where('uuid', $uuid)->firstOrFail();
        $oldCard = Card::find($link->card_id);

        $validated = $request->validate([
            'card_uuid' => 'required|string|exists:cards,uuid',
            'order'     => 'nullable|array',
            'order.*'   => 'string',
        ]);

        $newCard = Card::where('uuid', $validated['card_uuid'])->firstOrFail();

        // Jika newCard sebelumnya adalah single card (atau title kosong),
        // pastikan group title terisi dengan judul link pertama sebagai default
        if ($newCard->links()->count() === 1 && empty($newCard->title)) {
            $firstLink = $newCard->links()->first();
            if ($firstLink) {
                $newCard->title = $firstLink->title;
                $newCard->save();
            }
        }

        $link->card_id = $newCard->id;
        $link->save();

        // Re-order link di card tujuan jika diberikan
        if (!empty($validated['order'])) {
            foreach ($validated['order'] as $index => $uid) {
                CardLink::where('uuid', $uid)->update(['sort_order' => $index + 1]);
            }
        }

        // Cek kartu asal: jika kartu asal berbeda dan sekarang sudah tidak memiliki link lagi,
        // hapus (soft delete) kartu asal agar tidak meninggalkan kartu kosong hantu
        if ($oldCard && $oldCard->id !== $newCard->id) {
            if ($oldCard->links()->count() === 0) {
                $oldCard->forceDelete();
            } else {
                // Reorder link yang tersisa di kartu lama
                foreach ($oldCard->links()->orderBy('sort_order')->get() as $idx => $remLink) {
                    $remLink->update(['sort_order' => $idx + 1]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tautan berhasil dipindahkan.',
        ]);
    }

    // ==========================================
    // DETACH LINK TO SINGLE CARD (Drag Out of Group)
    // ==========================================
    public function detachLinkToCard(Request $request, string $uuid)
    {
        $this->ensureEditMode();

        $link = CardLink::where('uuid', $uuid)->firstOrFail();
        $oldCard = Card::find($link->card_id);

        $validated = $request->validate([
            'new_index'    => 'nullable|integer',
            'card_order'   => 'nullable|array',
            'card_order.*' => 'string',
        ]);

        // Jika kartu asal memang hanya memiliki 1 tautan ini saja,
        // berarti kartu ini sudah merupakan single card mandiri. Cukup perbarui urutan card jika ada.
        if ($oldCard && $oldCard->links()->count() === 1) {
            if (!empty($validated['card_order'])) {
                foreach ($validated['card_order'] as $index => $cOrderUuid) {
                    Card::where('uuid', $cOrderUuid)->update(['sort_order' => $index + 1]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Tautan sudah berupa kartu tunggal.',
            ]);
        }

        // Buat Card baru khusus untuk link ini
        $maxSort = Card::max('sort_order') ?? 0;
        $newSort = isset($validated['new_index']) ? ($validated['new_index'] + 1) : ($maxSort + 1);

        $newCard = Card::create([
            'uuid'        => (string) Str::uuid(),
            'title'       => $link->title,
            'description' => $link->subtitle ?? null,
            'color'       => $link->color ?? ($oldCard ? $oldCard->color : null),
            'sort_order'  => $newSort,
            'is_active'   => true,
        ]);

        if ($oldCard && $oldCard->categories()->exists()) {
            $newCard->categories()->sync($oldCard->categories->pluck('id'));
            $newCard->category_id = $oldCard->category_id;
            $newCard->save();
        }

        // Pindahkan link ke newCard
        $link->card_id = $newCard->id;
        $link->sort_order = 1;
        $link->save();

        // Reorder kartu berdasarkan susunan baru dari drag and drop
        if (!empty($validated['card_order'])) {
            foreach ($validated['card_order'] as $index => $cOrderUuid) {
                if ($cOrderUuid === $uuid || $cOrderUuid === 'NEW_CARD') {
                    $newCard->update(['sort_order' => $index + 1]);
                } else {
                    Card::where('uuid', $cOrderUuid)->update(['sort_order' => $index + 1]);
                }
            }
        }

        // Cek kartu lama: jika sekarang sudah tidak memiliki link lagi,
        // hapus (soft delete) agar tidak menjadi kartu kosong tanpa isi
        if ($oldCard) {
            if ($oldCard->links()->count() === 0) {
                $oldCard->forceDelete();
            } else {
                foreach ($oldCard->links()->orderBy('sort_order')->get() as $idx => $remLink) {
                    $remLink->update(['sort_order' => $idx + 1]);
                }
            }
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Tautan berhasil dijadikan kartu baru.',
            'new_card' => $newCard,
        ]);
    }

    // ==========================================
    // ARCHIVE (Soft-Deleted & Expired Items)
    // ==========================================
    public function getArchived()
    {
        $this->ensureEditMode();

        // Cards yang di-soft-delete
        $archivedCards = Card::onlyTrashed()
            ->with('links')
            ->orderByDesc('deleted_at')
            ->get();

        // Links yang di-soft-delete ATAU yang expired_at sudah lewat hari ini
        $archivedLinks = CardLink::withTrashed()
            ->with('card')
            ->where(function ($q) {
                $q->whereNotNull('deleted_at')
                  ->orWhere(function ($q2) {
                      $q2->whereNull('deleted_at')
                         ->whereNotNull('expired_at')
                         ->where('expired_at', '<', today());
                  });
            })
            ->orderByDesc('deleted_at')
            ->get();

        return response()->json([
            'success'        => true,
            'archived_cards' => $archivedCards,
            'archived_links' => $archivedLinks,
        ]);
    }

    public function restoreCard(string $uuid)
    {
        $this->ensureEditMode();

        $card = Card::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $card->restore();

        return response()->json([
            'success' => true,
            'message' => 'Kartu berhasil dipulihkan.',
        ]);
    }

    public function forceDeleteCard(string $uuid)
    {
        $this->ensureEditMode();

        $card = Card::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $card->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Kartu berhasil dihapus secara permanen.',
        ]);
    }

    public function restoreLink(string $uuid)
    {
        $this->ensureEditMode();

        $link = CardLink::withTrashed()->where('uuid', $uuid)->firstOrFail();

        if ($link->trashed()) {
            $link->restore();
        }

        // Reset expired_at jika expired
        if ($link->expired_at && $link->expired_at < today()) {
            $link->expired_at = null;
            $link->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Tautan berhasil dipulihkan.',
        ]);
    }

    public function forceDeleteLink(string $uuid)
    {
        $this->ensureEditMode();

        $link = CardLink::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $link->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Tautan berhasil dihapus secara permanen.',
        ]);
    }
}
