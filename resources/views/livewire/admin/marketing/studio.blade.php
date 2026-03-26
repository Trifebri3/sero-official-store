<?php

use Livewire\Volt\Component;
use App\Models\{MarketingContent, ContentTemplate, Product};
use Illuminate\Support\Str;

new class extends Component {
    // Basic Info
    public $product_id, $template_id, $title, $status = 'draft', $scheduled_at;

    // Dynamic Storage
    public $dynamic_fields = [];
    public $active_template = null;

    public function resetForm() {
        $this->reset(['product_id', 'template_id', 'title', 'status', 'dynamic_fields', 'active_template']);
    }

    public function updatedTemplateId($id) {
        if (!$id) {
            $this->active_template = null;
            $this->dynamic_fields = [];
            return;
        }

        $this->active_template = ContentTemplate::find($id);
        $this->dynamic_fields = [];

        if ($this->active_template && is_array($this->active_template->fields)) {
            foreach ($this->active_template->fields as $field) {
                $this->dynamic_fields[$field] = '';
            }
        }
    }

    public function save() {
        $this->validate([
            'product_id' => 'required',
            'template_id' => 'required',
            'title' => 'required|min:3',
        ]);

        MarketingContent::create([
            'product_id' => $this->product_id,
            'content_template_id' => $this->template_id,
            'title' => $this->title,
            'status' => $this->status,
            'content_data' => $this->dynamic_fields,
            'scheduled_at' => $this->scheduled_at,
        ]);

        session()->flash('message', 'Konten kreatif berhasil disimpan!');
        return redirect()->route('admin.marketing.index');
    }

    // --- INI KUNCINYA AGAR $products TIDAK UNDEFINED ---
    public function with() {
        return [
            'products' => Product::orderBy('name')->get(),
            'templates' => ContentTemplate::orderBy('platform')->get(),
        ];
    }
}; ?>

<div class="grid grid-cols-12 gap-8 animate-in fade-in duration-700">
    <div class="col-span-12 lg:col-span-4 space-y-6">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h3 class="text-[10px] font-black text-luxury-gold uppercase tracking-[0.3em] mb-6">Content Identifier</h3>

            <div class="space-y-4">
                <div>
                    <label class="text-[9px] font-bold text-gray-400 uppercase ml-2">Product Embedding</label>
                    <select wire:model="product_id" class="w-full mt-1 bg-gray-50 border-none rounded-2xl py-3 text-xs focus:ring-luxury-gold shadow-inner">
                        <option value="">Pilih Produk...</option>
                        @foreach($products as $p) <option value="{{ $p->id }}">{{ $p->name }}</option> @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[9px] font-bold text-gray-400 uppercase ml-2">Distribution Platform</label>
                    <select wire:model.live="template_id" class="w-full mt-1 bg-gray-50 border-none rounded-2xl py-3 text-xs focus:ring-luxury-gold shadow-inner">
                        <option value="">Pilih Template Form...</option>
                        @foreach($templates as $t) <option value="{{ $t->id }}">{{ $t->platform }} - {{ $t->name }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[9px] font-bold text-gray-400 uppercase ml-2">Campaign Title</label>
                    <input type="text" wire:model="title" placeholder="Misal: Promo Ramadhan Reels" class="w-full mt-1 bg-gray-50 border-none rounded-2xl py-3 text-xs focus:ring-luxury-gold shadow-inner">
                </div>
            </div>
        </div>
        </div>

    <div class="col-span-12 lg:col-span-8">
        <div class="bg-white min-h-[500px] rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                <div>
                    <h2 class="text-xl font-serif italic text-luxury-dark">Creative <span class="text-luxury-gold">Canvas</span></h2>
                    <p class="text-[9px] font-bold text-gray-400 uppercase">Input detail konten berdasarkan template pilihan</p>
                </div>
                @if($active_template)
                    <span class="px-4 py-1.5 bg-luxury-dark text-luxury-gold rounded-full text-[8px] font-black uppercase tracking-widest">
                        Mode: {{ $active_template->name }}
                    </span>
                @endif
            </div>

            <div class="p-10 flex-grow">
                @if($active_template)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach($dynamic_fields as $fieldName => $value)
                            <div class="space-y-2 {{ Str::contains(strtolower($fieldName), ['caption', 'deskripsi', 'detail']) ? 'md:col-span-2' : '' }}">
                                <label class="text-[10px] font-black text-luxury-dark uppercase tracking-widest ml-1">{{ $fieldName }}</label>
                                @if(Str::contains(strtolower($fieldName), ['caption', 'deskripsi', 'detail']))
                                    <textarea wire:model="dynamic_fields.{{ $fieldName }}" rows="5" class="w-full bg-[#FDFBF9] border-none rounded-3xl py-4 px-5 text-xs focus:ring-luxury-gold shadow-inner"></textarea>
                                @else
                                    <input type="text" wire:model="dynamic_fields.{{ $fieldName }}" class="w-full bg-[#FDFBF9] border-none rounded-2xl py-4 px-5 text-xs focus:ring-luxury-gold shadow-inner">
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-full flex flex-col items-center justify-center py-20 opacity-20">
                        <svg class="w-24 h-24 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="1.5"/></svg>
                        <p class="font-serif italic text-xl">Silahkan pilih platform terlebih dahulu</p>
                    </div>
                @endif
            </div>

            <div class="p-8 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-4">
                <button wire:click="save" class="bg-luxury-dark text-white px-12 py-4 rounded-2xl text-[10px] font-bold uppercase tracking-widest shadow-xl hover:bg-black transition-all">
                    Finalize & Save Content
                </button>
            </div>
        </div>
    </div>
</div>
